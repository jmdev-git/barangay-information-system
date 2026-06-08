<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\HouseholdController;
use App\Http\Controllers\Api\ClearanceController;
use App\Http\Controllers\Api\BlotterController;
use App\Http\Controllers\Api\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| These routes are only for JSON/API.
| API route names are prefixed with "api." so they will NOT conflict
| with your web routes like residents.index, households.index, blotters.index.
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/login',    [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

/*
|--------------------------------------------------------------------------
| Admin Bootstrap — one-time endpoint to create the first admin account.
| Only works when NO admin account exists yet (safe to call on fresh DB).
| Remove or disable this route after the first admin is created.
|--------------------------------------------------------------------------
*/
Route::post('/setup/admin', [AuthController::class, 'setupAdmin'])->name('api.setup.admin');

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->name('api.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | API Logout
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    /*
    |--------------------------------------------------------------------------
    | API Residents
    |--------------------------------------------------------------------------
    */
    Route::apiResource('residents', ResidentController::class);

    /*
    |--------------------------------------------------------------------------
    | API Households
    |--------------------------------------------------------------------------
    */
    Route::apiResource('households', HouseholdController::class);

    /*
    |--------------------------------------------------------------------------
    | API Clearances
    |--------------------------------------------------------------------------
    */
    Route::apiResource('clearances', ClearanceController::class);

    Route::put('/clearances/{clearance}/approve', [ClearanceController::class, 'approve'])
        ->name('clearances.approve');

    Route::put('/clearances/{clearance}/reject', [ClearanceController::class, 'reject'])
        ->name('clearances.reject');

    /*
    |--------------------------------------------------------------------------
    | API Blotters - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        Route::apiResource('blotters', BlotterController::class);
    });

    /*
    |--------------------------------------------------------------------------
    | API Reports - Admin Only
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/residents', [ReportController::class, 'residents'])->name('residents');
        Route::get('/blotters', [ReportController::class, 'blotters'])->name('blotters');
        Route::get('/clearances', [ReportController::class, 'clearances'])->name('clearances');
        Route::get('/export', [ReportController::class, 'export'])->name('export');
    });
});