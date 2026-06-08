<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ResidentController;
use App\Http\Controllers\HouseholdController;
use App\Http\Controllers\ClearanceController;
use App\Http\Controllers\BlotterController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\Admin\AccountApprovalController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Resident\BlotterController as ResidentBlotterController;
use App\Http\Controllers\Census\CensusController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
})->name('welcome');

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

/*
|--------------------------------------------------------------------------
| Authenticated — All Roles
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Account status information pages (Req 3 AC5, AC6)
    Route::get('/account/pending', [AccountStatusController::class, 'pending'])->name('account.pending');
    Route::get('/account/rejected', [AccountStatusController::class, 'rejected'])->name('account.rejected');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Resident Routes — active accounts only (Req 3 AC5, AC6)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['account_status'])->group(function () {

        // Resident clearance self-service (Req 5)
        Route::get('/clearances', [ClearanceController::class, 'index'])->name('clearances.index');
        Route::get('/clearances/create', [ClearanceController::class, 'create'])->name('clearances.create');
        Route::post('/clearances', [ClearanceController::class, 'store'])->name('clearances.store');
        Route::get('/clearances/{clearance}', [ClearanceController::class, 'show'])->name('clearances.show');
        Route::get('/clearances/{clearance}/download', [ClearanceController::class, 'download'])->name('clearances.download');

        // Resident blotter self-service (Req 6)
        Route::get('/my-blotters', [ResidentBlotterController::class, 'index'])->name('resident.blotters.index');
        Route::get('/my-blotters/create', [ResidentBlotterController::class, 'create'])->name('resident.blotters.create');
        Route::post('/my-blotters', [ResidentBlotterController::class, 'store'])->name('resident.blotters.store');
        Route::get('/my-blotters/{blotter}', [ResidentBlotterController::class, 'show'])->name('resident.blotters.show');

        // Announcements
        Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {

        // ------------------------------------------------------------------
        // Account Approval (Req 2)
        // ------------------------------------------------------------------
        Route::prefix('admin/accounts')->name('admin.accounts.')->group(function () {
            Route::get('/', [AccountApprovalController::class, 'index'])->name('index');
            Route::get('/{user}', [AccountApprovalController::class, 'show'])->name('show');
            Route::post('/{user}/approve', [AccountApprovalController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [AccountApprovalController::class, 'reject'])->name('reject');
        });

        // Secure document viewer for ID files (Req 2 AC1)
        Route::get('/admin/documents/{document}', [DocumentController::class, 'show'])->name('admin.documents.show');

        // ------------------------------------------------------------------
        // Unified Resident & Household Management (Req 4)
        // ------------------------------------------------------------------
        Route::resource('residents', ResidentController::class);
        Route::resource('households', HouseholdController::class);

        // ------------------------------------------------------------------
        // Admin Blotter Management (Req 7)
        // ------------------------------------------------------------------
        Route::resource('blotters', BlotterController::class);
        Route::post('/blotters/{blotter}/approve', [BlotterController::class, 'approve'])->name('blotters.approve');
        Route::post('/blotters/{blotter}/reject', [BlotterController::class, 'reject'])->name('blotters.reject');

        // ------------------------------------------------------------------
        // Admin Clearance Management (Req 5)
        // ------------------------------------------------------------------
        Route::get('/admin/clearances', [ClearanceController::class, 'adminIndex'])->name('clearances.admin');
        Route::put('/clearances/{clearance}/approve', [ClearanceController::class, 'approve'])->name('clearances.approve');
        Route::put('/clearances/{clearance}/reject',  [ClearanceController::class, 'reject'])->name('clearances.reject');

        // ------------------------------------------------------------------
        // Census Workflow (Req 8) — 8-step flow from the diagram
        // ------------------------------------------------------------------
        Route::prefix('census')->name('census.')->group(function () {
            Route::get('/', [CensusController::class, 'index'])->name('index');
            // Step 1: Search or create household
            Route::get('/step/1', [CensusController::class, 'step1'])->name('step1');
            Route::post('/step/1', [CensusController::class, 'step1Store'])->name('step1.store');
            // Step 2: Collect resident information
            Route::get('/step/2', [CensusController::class, 'step2'])->name('step2');
            Route::post('/step/2', [CensusController::class, 'step2Store'])->name('step2.store');
            // Step 3: Capture/Upload valid ID + resident photo
            Route::get('/step/3', [CensusController::class, 'step3'])->name('step3');
            Route::post('/step/3', [CensusController::class, 'step3Store'])->name('step3.store');
            // Step 4: Validate information (duplicate check)
            Route::get('/step/4', [CensusController::class, 'step4'])->name('step4');
            Route::post('/step/4', [CensusController::class, 'step4Confirm'])->name('step4.confirm');
            // Step 5: Create resident profile + save to DB
            Route::post('/step/5', [CensusController::class, 'step5Save'])->name('step5.save');
            // Step 6: Success / Generate reports link
            Route::get('/complete/{resident}', [CensusController::class, 'complete'])->name('complete');
            // Reset / start over
            Route::post('/reset', [CensusController::class, 'reset'])->name('reset');
        });

        // ------------------------------------------------------------------
        // Announcements
        // ------------------------------------------------------------------
        Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
        Route::put('/announcements/{announcement}', [AnnouncementController::class, 'update'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AnnouncementController::class, 'destroy'])->name('announcements.destroy');

        // ------------------------------------------------------------------
        // Reports & Export (Req 12)
        // ------------------------------------------------------------------
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/residents', [ReportController::class, 'residents'])->name('residents');
            Route::get('/blotters', [ReportController::class, 'blotters'])->name('blotters');
            Route::get('/clearances', [ReportController::class, 'clearances'])->name('clearances');
            Route::get('/export', [ReportController::class, 'export'])->name('export');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
