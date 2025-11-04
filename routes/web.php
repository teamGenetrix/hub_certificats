<?php

use App\Http\Controllers\Admin\FormationController;
use App\Http\Controllers\Admin\IndexController;
use App\Http\Controllers\LegacyAliasController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ReferenceController;
use App\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect()->route('login');
// });


// Auth::routes();

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'index'])->name('admin.index');

    Route::get('/formations', [FormationController::class, 'index'])->name('admin.formations.index');
    Route::get('/formations/create', [FormationController::class, 'create'])->name('admin.formations.create');

    // Participant Management Routes
    Route::get('/participants/template', [ParticipantController::class, 'downloadTemplate'])->name('admin.participants.template');
    Route::post('/participants/import', [ParticipantController::class, 'import'])->name('admin.participants.import');
    Route::resource('participants', ParticipantController::class)->names([
        'index' => 'admin.participants.index',
        'create' => 'admin.participants.create',
        'store' => 'admin.participants.store',
        'show' => 'admin.participants.show',
        'edit' => 'admin.participants.edit',
        'update' => 'admin.participants.update',
        'destroy' => 'admin.participants.destroy',
    ]);

    // Session Management Routes
    Route::get('/sessions/all', [SessionController::class, 'getAll'])->name('admin.sessions.all');
    Route::resource('sessions', SessionController::class)->names([
        'index' => 'admin.sessions.index',
        'create' => 'admin.sessions.create',
        'store' => 'admin.sessions.store',
        'show' => 'admin.sessions.show',
        'edit' => 'admin.sessions.edit',
        'update' => 'admin.sessions.update',
        'destroy' => 'admin.sessions.destroy',
    ]);
    Route::get('/sessions/{session}/add-participants', [SessionController::class, 'addParticipants'])->name('admin.sessions.add-participants');
    Route::post('/sessions/{session}/add-participants', [SessionController::class, 'storeParticipants'])->name('admin.sessions.store-participants');
    Route::delete('/sessions/{session}/participants/{enrollment}', [SessionController::class, 'removeParticipant'])->name('admin.sessions.remove-participant');

    // Reference Management Routes
    Route::prefix('references')->name('admin.references.')->group(function () {
        Route::get('/', [ReferenceController::class, 'index'])->name('index');
        Route::get('/create', [ReferenceController::class, 'create'])->name('create');
        
        // AJAX endpoints for dynamic loading
        Route::get('/trainings', [ReferenceController::class, 'getTrainingsByPillar'])->name('trainings');
        Route::get('/sessions', [ReferenceController::class, 'getSessionsByTraining'])->name('sessions');
        Route::get('/enrollments', [ReferenceController::class, 'getEnrollmentsBySession'])->name('enrollments');
        
        // Generation endpoints
        Route::post('/generate', [ReferenceController::class, 'generate'])->name('generate');
        Route::post('/generate-session', [ReferenceController::class, 'generateForSession'])->name('generate.session');
        
        // Verification
        Route::get('/verify', [ReferenceController::class, 'showVerifyForm'])->name('verify.form');
        Route::post('/verify', [ReferenceController::class, 'verify'])->name('verify');
        
        // Export
        Route::get('/export-form', [ReferenceController::class, 'showExportForm'])->name('export');
        Route::get('/list', [ReferenceController::class, 'list'])->name('list');
        Route::get('/download', [ReferenceController::class, 'export'])->name('download');
    });

    // Legacy Alias Management Routes
    Route::get('/legacy-aliases/search-references', [LegacyAliasController::class, 'searchReferences'])->name('admin.legacy-aliases.search-references');
    Route::get('/legacy-aliases-import', [LegacyAliasController::class, 'showImportForm'])->name('admin.legacy-aliases.import');
    Route::post('/legacy-aliases-import', [LegacyAliasController::class, 'processImport'])->name('admin.legacy-aliases.process-import');
    Route::get('/legacy-aliases-template', [LegacyAliasController::class, 'downloadTemplate'])->name('admin.legacy-aliases.download-template');
    Route::resource('legacy-aliases', LegacyAliasController::class)->names([
        'index' => 'admin.legacy-aliases.index',
        'create' => 'admin.legacy-aliases.create',
        'store' => 'admin.legacy-aliases.store',
        'edit' => 'admin.legacy-aliases.edit',
        'update' => 'admin.legacy-aliases.update',
        'destroy' => 'admin.legacy-aliases.destroy',
    ]);
});
