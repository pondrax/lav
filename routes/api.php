<?php

use App\Http\Controllers\App\AuthController;
use App\Http\Controllers\App\DashboardController;
use App\Http\Controllers\App\DevController;
use App\Http\Controllers\App\MenuController;
use App\Http\Controllers\App\PermissionController;
use App\Http\Controllers\App\RoleController;
use App\Http\Controllers\App\UserController;
use App\Http\Controllers\ProfileController;
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
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/auth/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::post('/auth/register', [AuthController::class, 'register'])->name('auth.register');

// Route::get('/dashboard', function(){
//   return response()->json(['message'=>'Success']);
// })->middleware('auth:sanctum');

Route::group(['middleware' => [
    'auth:sanctum',
    'route.permission',
]], function () {
    Route::group(['prefix' => 'app'], function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        Route::apiResource('menu', MenuController::class)->except([]);

        Route::apiResource('user', UserController::class)->except([]);
        Route::apiResource('permission', PermissionController::class)->only(['index', 'show']);
        Route::apiResource('role', RoleController::class)->except([]);
        Route::post('role/{role}/assign-permission', [RoleController::class, 'assignPermission'])->name('role.assign.permission');
        Route::post('user/{user}/assign-role', [UserController::class, 'assignRole'])->name('user.assign.role');

        Route::apiResource('profile', ProfileController::class)->only(['index', 'store']);

        Route::apiResource('dev', DevController::class)->only(['index', 'store']);
    });
});

Route::apiResource('posttest', App\Http\Controllers\PosttestController::class)->only(['store','update'])->middleware(['auth:sanctum','route.permission']); #generated#
Route::apiResource('postest', App\Http\Controllers\PostestController::class)->only(['store','update'])->middleware(['auth:sanctum','route.permission']); #generated#
Route::apiResource('posttest', App\Http\Controllers\PosttestController::class)->only(['store','update'])->middleware(['auth:sanctum','route.permission']); #generated#