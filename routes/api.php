<?php

use App\Http\Controllers\API\ArtistController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\SongController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);

// Songs
Route::get('/all-songs', [SongController::class, 'getAllSongs']);
Route::get('/get-song/{id}', [SongController::class, 'getSong']);
Route::get('/search-song', [SongController::class, 'searchSong']);

// Artists
Route::get('/all-artists', [ArtistController::class, 'getAllArtists']);
Route::get('/get-artist/{id}', [ArtistController::class, 'getArtist']);
Route::get('/search-artist', [ArtistController::class, 'searchArtist']);
Route::get('/artists-list', [ArtistController::class, 'artistsList']);

// Authenticated Routes
Route::group(['middleware' => 'auth:sanctum'], function () {
    // checkout route
    Route::post('/add-order', [OrderController::class, 'addOrder']);

    // Admin Routes
    Route::group(['middleware' => 'isAdmin'], function () {
        Route::post('/add-song', [SongController::class, 'addSong']);
        Route::post('/add-artist', [ArtistController::class, 'addArtist']);
    });
});

