<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//PROTECTED ROUTES
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/auth/signout', [AuthController::class, 'signOut']);

    Route::post('/games/{slug}/upload', [GameController::class, 'upload']);
    Route::post('/games', [GameController::class, 'store']);
    Route::put('/games/{slug}', [GameController::class, 'update']);
    Route::delete('/games/{slug}', [GameController::class, 'destroy']);

    Route::post('/games/{slug}/scores', [ScoreController::class, 'store']);
});

// PUBLIC ROUTES
Route::post('/auth/signup', [AuthController::class, 'signUp']);
Route::post('/auth/signin', [AuthController::class, 'signIn']);

Route::get('/games', [GameController::class, 'index']);

Route::get('/games/{slug}', [GameController::class, 'findBySlug']);

Route::get('/games/{slug}/scores', [ScoreController::class, 'index']);

Route::get('/games/{slug}/{version}', [GameController::class, 'show']);

Route::get('/users/{username}', [UserController::class, 'show']);



// FALLBACK 404
Route::fallback(function () {
   return response()->json([
       'status' => 'not-found',
       'message' => 'Not found'
   ], 404);
});

