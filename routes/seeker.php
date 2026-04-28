<?php

use App\Http\Controllers\JobSeeker\ApplicationController;
use App\Http\Controllers\JobSeeker\DashboardController;
use App\Http\Controllers\JobSeeker\ProfileController;
use App\Http\Controllers\JobSeeker\ResumeController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'seeker'])
    ->prefix('seeker')
    ->name('seeker.')
    ->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile',        [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',      [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo');

    // Education
    Route::post('/profile/education',                [ProfileController::class, 'storeEducation'])->name('education.store');
    Route::put('/profile/education/{education}',     [ProfileController::class, 'updateEducation'])->name('education.update');
    Route::delete('/profile/education/{education}',  [ProfileController::class, 'destroyEducation'])->name('education.destroy');

    // Experience
    Route::post('/profile/experience',               [ProfileController::class, 'storeExperience'])->name('experience.store');
    Route::put('/profile/experience/{experience}',   [ProfileController::class, 'updateExperience'])->name('experience.update');
    Route::delete('/profile/experience/{experience}',[ProfileController::class, 'destroyExperience'])->name('experience.destroy');

    // Skills
    Route::post('/profile/skills', [ProfileController::class, 'syncSkills'])->name('skills.sync');

    // Resume
    Route::get('/resume',     [ResumeController::class, 'show'])->name('resume.show');
    Route::get('/resume/pdf', [ResumeController::class, 'pdf'])->name('resume.pdf');

    // Applications
    Route::get('/applications',                          [ApplicationController::class, 'index'])->name('applications.index');
    Route::post('/jobs/{job}/apply',                     [ApplicationController::class, 'store'])->name('applications.store');
    Route::delete('/applications/{application}/withdraw',[ApplicationController::class, 'withdraw'])->name('applications.withdraw');

    // Saved jobs
    Route::post('/jobs/{job}/save',   [ApplicationController::class, 'save'])->name('jobs.save');
    Route::delete('/jobs/{job}/unsave',[ApplicationController::class, 'unsave'])->name('jobs.unsave');
});
