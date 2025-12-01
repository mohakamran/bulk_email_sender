<?php

use App\Http\Controllers\BulkEmailController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('bulk-email.index');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/bulk-email', [BulkEmailController::class, 'index'])->name('bulk-email.index');
    Route::post('/bulk-email/send', [BulkEmailController::class, 'store'])->name('bulk-email.send');
    Route::post('/bulk-email/send/{id}', [BulkEmailController::class, 'sendIndividual'])->name('bulk-email.send.individual');
    Route::get('/bulk-email/data', [BulkEmailController::class, 'data'])->name('bulk-email.data');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
