<?php

use App\Http\Controllers\Admin\IndexController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return redirect()->route('login');
// });


// Auth::routes();

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', [IndexController::class, 'index'])->name('admin.index');
});
