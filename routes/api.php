<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\AttemptController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\FilialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Crypt;

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
Route::post('/login', [AuthController::class, 'login']);
Route::post('/test_login', [AuthController::class, 'test_login']);
Route::post('/register', [RegisterController::class, 'store']);
Route::get('/settings/public', [SettingController::class, 'publicSettings']);
Route::get('/filials/public', [FilialController::class, 'publicIndex']);

Route::post('/store_raw2', [AuthController::class, 'store_raw']);
Route::post('/store_raw', [AttemptController::class, 'store']);
Route::get('attempts/{attempt}/protocol', [AttemptController::class, 'export_protocol']);
Route::get('users/{user}/tests/{test}/protocol', [UserController::class, 'export_test_result']);
Route::get('/users/{user}/data', [UserController::class, 'export_data']);
Route::get('/{id}/protocol', [AuthController::class, 'export_protocol']);
Route::get('/logs/export_logs',[AdminController::class,'export_logs']);
Route::get('/report/export_report', [AdminController::class, 'export_report']);
Route::get('/start_test', [TestController::class, 'start_test']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/test/{id}/photo', [TestController::class, 'store_photo']);
    Route::patch('/users/update_password', [UserController::class, 'update_password']);
    Route::apiResource('/tests', TestController::class);
    Route::apiResource('/attempts', AttemptController::class);
    Route::apiResource('/users', UserController::class);
    Route::apiResource('/filials', FilialController::class);
    Route::apiResource('/roles', RoleController::class);
    Route::get('/control', [UserController::class, 'control']);
    Route::post('/users/{user}/reset_password', [UserController::class, 'reset_password']);



    Route::get('/result', [UserController::class, 'result']);
    Route::get('/settings', [SettingController::class, 'index']);
    Route::post('/settings', [SettingController::class, 'store']);
    Route::patch('/settings/{setting}', [SettingController::class, 'update']);
    Route::delete('/settings/{setting}', [SettingController::class, 'destroy']);

    Route::get('/logs', [AdminController::class, 'logs']);
    Route::get('/report', [AdminController::class, 'report']);

    Route::delete('/logs/delete_old_logs',[AdminController::class,'delete_old_logs']);

    Route::post('/sql', [AdminController::class, 'sql']);

    Route::get('/my_tests', [UserController::class, 'my_tests']);
    Route::get('/user', function (Request $request) {
        $arr = $request->user();
        $arr['code'] = Crypt::encryptString($arr['id']);
        return $arr;
    });
});
