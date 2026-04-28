<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Jobs\JobController;
use Illuminate\Support\Facades\Route;

// ── Public Routes ─────────────────────────────────────────────────────────────
Route::get('/', fn() => inertia('Welcome'))->name('home');

Route::prefix('jobs')->name('jobs.')->group(function () {
    Route::get('/',           [JobController::class, 'index'])->name('index');
    Route::get('/{job:slug}', [JobController::class, 'show'])->name('show');
});

// ── Auth Routes ───────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',             [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login',            [AuthenticatedSessionController::class, 'store']);
    Route::get('/register/seeker',   [RegisteredUserController::class, 'createSeeker'])->name('register.seeker');
    Route::get('/register/employer', [RegisteredUserController::class, 'createEmployer'])->name('register.employer');
    Route::post('/register',         [RegisteredUserController::class, 'store'])->name('register');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

require __DIR__.'/seeker.php';
require __DIR__.'/employer.php';
