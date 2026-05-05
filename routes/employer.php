<?php

use App\Http\Controllers\Employer\ApplicantController;
use App\Http\Controllers\Employer\CompanyController;
use App\Http\Controllers\Employer\DashboardController;
use App\Http\Controllers\Employer\JobController as EmployerJobController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'employer'])
    ->prefix('employer')
    ->name('employer.')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Company
        Route::get('/company',      [CompanyController::class, 'edit'])->name('company.edit');
        Route::patch('/company',    [CompanyController::class, 'update'])->name('company.update');
        Route::post('/company/logo', [CompanyController::class, 'updateLogo'])->name('company.logo');

        // Job Posts
        Route::resource('jobs', EmployerJobController::class)->except(['show']);
        Route::patch('/jobs/{job}/status', [EmployerJobController::class, 'updateStatus'])->name('jobs.status');

        // Applicants
        Route::get('/jobs/{job}/applicants',                         [ApplicantController::class, 'index'])->name('applicants.index');
        Route::get('/jobs/{job}/applicants/{application}',           [ApplicantController::class, 'show'])->name('applicants.show');
        Route::patch('/jobs/{job}/applicants/{application}/status',  [ApplicantController::class, 'updateStatus'])->name('applicants.status');
        Route::get('/applicants/{application}/resume',               [ApplicantController::class, 'downloadResume'])->name('applicants.resume');
    });
