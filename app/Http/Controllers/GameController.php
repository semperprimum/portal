<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'size' => ['nullable', 'integer', 'min:1'],
            'sortBy' => ['nullable', Rule::in(['title', 'popular', 'uploaddate'])],
            'sortDir' => ['nullable', Rule::in(['asc', 'desc'])],
        ]);

        $query = Game::query()->withCount('scores')->with('author:id,username');

        $size = $request->input('size', 10);
        $sortBy = $request->input('sortBy', 'title');
        $sortDir = $request->input('sortDir', 'asc');

        if ($sortBy === 'popular') {
            $query->orderBy('scores_count', $sortDir);
        } elseif ($sortBy === 'uploaddate') {
            $query->orderBy('created_at', $sortDir);
        } else {
            $query->orderBy('title', $sortDir);
        }

        $games = $query->paginate($size);

        return response()->json(['games' => $games], 200);
    }

    public function findBySlug($slug) {
        $game = Game::where('slug', $slug)->firstOrFail();
        $latestVersion = GameVersion::where('game_id', $game->id)->latest('version')->first();

        return response()->json([
            'game' => $game,
            'latestVersion' => $latestVersion
        ], 200);
    }

    public function upload(Request $request, $slug)
    {
        $request->validate([
            'zipfile' => ['required', 'file', 'mimes:zip'],
            'token' => ['required'],
        ]);

        $game = Game::where('slug', $slug)->firstOrFail();

        if ($game->author_id !== auth()->id()) {
            return response()->json(['message' => 'You are not authorized to upload a new version for this game.'], 403);
        }

        $zipPath = $request->file('zipfile')->store('temp');
        $zip = new \ZipArchive;
        if ($zip->open(storage_path('app/' . $zipPath)) === true) {
            $versionNumber = GameVersion::where('game_id', $game->id)->max('version') + 1;

            $extractPath = storage_path('app/public/games/' . $slug . '/' . $versionNumber);
            if (!file_exists($extractPath)) {
                mkdir($extractPath, 0755, true);
            }

            $zip->extractTo($extractPath);
            $zip->close();
        } else {
            return response()->json(['message' => 'Failed to extract the uploaded zip file.'], 500);
        }

        $gameVersion = new GameVersion();
        $gameVersion->game_id = $game->id;
        $gameVersion->version = $versionNumber;
        $gameVersion->path = $extractPath;
        $gameVersion->save();

        $thumbnailPath = $extractPath . '/thumbnail.png';
        if (file_exists($thumbnailPath)) {
            $game->thumbnail = 'public/games/' . $slug . '/thumbnail.png';
            $game->save();
        }

        return response()->json(['message' => 'Game version uploaded successfully.'], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'min:3', 'max:60', Rule::unique('games', 'title')],
            'description' => ['required', 'min:0', 'max:200']
        ]);

        $slug = Str::of($request->input('title'))->slug('-');

        Game::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'slug' => $slug,
            'author_id' => auth()->id()
        ]);

        return response()->json(['status' => 'success', 'slug' => $slug], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug, $version)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $gameVersion = GameVersion::where('game_id', $game->id)
            ->where('version', $version)
            ->first();

        if (!$gameVersion) {
            return response()->json(['message' => 'Game version not found.'], 404);
        }

        $path = 'public/games/' . $slug . '/' . $gameVersion->version . '/';
        $files = Storage::files($path);

        $fileContents = [];

        foreach ($files as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $filename = basename($file);

            if (in_array($extension, ['js', 'html', 'css'])) {
                $fileContents[$filename] = Storage::get($path . $filename);
            }
        }

        return response()->json(['files' => $fileContents], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $request->validate([
            'title' => ['min:3', 'max:60', Rule::unique('games', 'title')->ignore($game->id)],
            'description' => ['min:0', 'max:200']
        ]);

        if ($game->author_id !== auth()->id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author',
            ], 403);
        }

        $game->update($request->all());
        return response()->json([
            'status' => 'success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        if ($game->author_id !== auth()->id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author',
            ], 403);
        }

        $game->scores()->delete();

        $gameVersions = GameVersion::where('game_id', $game->id)->get();

        foreach ($gameVersions as $version) {
            $path = storage_path('app/public/' . $version->path);
            Storage::deleteDirectory($path);
            $version->delete();
        }

        $game->delete();

        return response()->json([], 204);
    }

}
