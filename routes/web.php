<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\ReactController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/{path?}', [ReactController::class, 'index'])->where('path', '.*');

Route::get('/{path?}', function () {
    //dd("KST API");
    return view('welcome');
});
Route::get('attempts/{attempt}/protocol', [AttemptController::class, 'export_protocol']);
Route::get('{id}/protocol', [AuthController::class, 'export_protocol']);

Route::get('users/{userId}/tests/{testId}/protocol', [AuthController::class, 'export_full']);
Route::get('/users/{user}/data', [UserController::class, 'export_data']);