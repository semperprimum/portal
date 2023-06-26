<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameVersion;
use App\Models\Score;
use Illuminate\Http\Request;

class ScoreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $scores = Score::whereHas('gameVersion', function ($query) use ($game) {
            $query->where('game_id', $game->id);
        })
            ->orderBy('score', 'desc')
            ->get();

        $formattedScores = $scores->map(function ($score) {
            return [
                'username' => $score->user->username,
                'score' => $score->score,
                'timestamp' => $score->timestamp,
            ];
        });

        return response()->json(['scores' => $formattedScores], 200);
    }





    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $slug)
    {
        $request->validate([
            'score' => ['required', 'integer'],
        ]);

        $game = Game::where('slug', $slug)->firstOrFail();

        $gameVersion = GameVersion::where('game_id', $game->id)
            ->orderByDesc('version')
            ->first();

        if (!$gameVersion) {
            return response()->json(['message' => 'Game version not found.'], 404);
        }

        $score = new Score();
        $score->game_version_id = $gameVersion->id;
        $score->user_id = auth()->id();
        $score->score = $request->input('score');
        $score->timestamp = time();
        $score->save();

        return response()->json(['status' => 'success'], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
