<?php

use App\Modules\Appointment\Controllers\Api\AppointmentScheduleApiController;
use App\Modules\Appointment\Controllers\Api\AppointmentStoreApiController;
use App\Modules\Auth\Controllers\Api\AuthApiController;
use App\Modules\Patient\Controllers\Api\StorePatientApiController;
use App\Modules\User\Controllers\Api\UserApiController;
use App\Modules\User\Controllers\Api\UserPaginateApiController;
use Illuminate\Support\Facades\Route;

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

        // Users
        Route::name('')->group(function () {
            Route::get('/users/paginate', [UserPaginateApiController::class, 'paginate'])->name('users.paginate');
            Route::get('/users/offset-paginate', [UserPaginateApiController::class, 'offsetPaginate'])->name('users.offset-paginate');
            Route::get('/users/cursor-paginate', [UserPaginateApiController::class, 'cursorPaginate'])->name('users.cursor-paginate');
            Route::apiResource('users', UserApiController::class);
        });

        // Patients
        Route::post('/patients', StorePatientApiController::class)->name('patients.store');

        // Appointments
        Route::post('/appointments', AppointmentStoreApiController::class)->name('appointments.store');
        Route::get('/appointments/schedule', AppointmentScheduleApiController::class)->name('appointments.schedule');
    });
});
