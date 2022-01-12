<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Application;
use App\Http\Controllers\Subscribe;

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
Route::post('/application/lookup', [Application::class, 'lookup']);
Route::post('/user/subscribe', [Subscribe::class, 'subscribe']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
