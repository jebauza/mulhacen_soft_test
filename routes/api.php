<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Auth\Controllers\Api\AuthApiController;
use App\Modules\User\Controllers\Api\UserApiController;

Route::get('/', function () {
    return response()->json(['message' => 'Hello world!']);
});

Route::middleware('api')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthApiController::class, 'register'])->name('auth.register');
        Route::post('/login', [AuthApiController::class, 'login'])->name('auth.login');

        Route::middleware('jwt')->group(function () {
            Route::get('/me', [AuthApiController::class, 'me'])->name('auth.me');
            Route::get('/refresh', [AuthApiController::class, 'refresh'])->name('auth.refresh');
            Route::post('/logout', [AuthApiController::class, 'logout'])->name('auth.logout');
        });
    });

    Route::middleware('auth:api')->group(function () {
        // Users routes
        Route::name('')->group(function () {
            Route::get('/users/paginate', [UserApiController::class, 'paginate'])->name('users.paginate');
            Route::get('/users/offset-paginate', [UserApiController::class, 'offsetPaginate'])->name('users.offset-paginate');
            Route::get('/users/cursor-paginate', [UserApiController::class, 'cursorPaginate'])->name('users.cursor-paginate');
            Route::apiResource('users', UserApiController::class);
        });
    });
});
