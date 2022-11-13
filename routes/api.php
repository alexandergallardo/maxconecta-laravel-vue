<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('version', function () {
    return response()->json(['version' => config('app.version')]);
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    Log::debug('User:' . serialize($request->user()));
    return $request->user();
});


Route::namespace('App\\Http\\Controllers\\API\V1')->group(function () {
    Route::get('profile', 'ProfileController@profile');
    Route::put('profile', 'ProfileController@updateProfile');
    Route::post('change-password', 'ProfileController@changePassword');
    Route::get('movie/list', 'MovieController@list');
    Route::get('client/list', 'ClientController@list');
    Route::get('rental/list', 'RentalController@list');

    Route::apiResources([
        'user' => 'UserController',
        'client' => 'ClientController',
        'movie' => 'MovieController',
        'rental' => 'RentalController',
    ]);
});
