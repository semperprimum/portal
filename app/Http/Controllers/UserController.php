<?php

namespace App\Http\Controllers;

use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $userData = [
            'username' => $user->username,
            'registeredTimestamp' => $user->registered_at,
            'authoredGames' => [],
            'highscores' => [],
        ];

        $authoredGames = $user->games()->with('versions')->get();
        foreach ($authoredGames as $game) {
            $gameData = [
                'slug' => $game->slug,
                'title' => $game->title,
                'description' => $game->description,
            ];
            $userData['authoredGames'][] = $gameData;
        }

        $highscores = Score::where('user_id', $user->id)->with('gameVersion.game')->get();
        foreach ($highscores as $score) {
            $highscoreData = [
//                'game_version_id' => $score->game_version_id,
//                'score' => $score->score,
//                'timestamp' => $score->timestamp,
//                'game_version' => $score->gameVersion,
            'game' => [
                'slug' => $score->gameVersion->game->slug,
                'title' => $score->gameVersion->game->title,
                'description' => $score->gameVersion->game->description,

            ],
                'score' => $score->score,
                'timestamp' => $score->timestamp
            ];
            $userData['highscores'][] = $highscoreData;
        }

        return response()->json($userData, 200);
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
