<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->unsignedInteger('job_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index('parent_id');
            $table->index('is_active');
        });

        // Industries
        Schema::create('industries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Employer Profiles
        Schema::create('employer_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->uuid('industry_id')->nullable();
            $table->string('company_name');
            $table->string('slug')->unique();
            $table->text('company_overview')->nullable();
            $table->string('company_type')->nullable();
            $table->unsignedSmallInteger('company_size')->nullable();
            $table->year('founded_year')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('social_links')->nullable();
            $table->string('address')->nullable();
            $table->string('district')->nullable();
            $table->string('country', 2)->default('BD');
            $table->enum('verification_status', ['pending','verified','rejected','suspended'])->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->uuid('verified_by')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->timestamp('premium_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('verification_status');
            $table->index('district');
        });

        // Job Seeker Profiles
        Schema::create('job_seeker_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->unique();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('current_job_title')->nullable();
            $table->string('current_company')->nullable();
            $table->unsignedTinyInteger('years_of_experience')->default(0);
            $table->string('location')->nullable();
            $table->string('district')->nullable();
            $table->string('country', 2)->default('BD');
            $table->enum('gender', ['male','female','other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->default('Bangladeshi');
            $table->json('resume_data')->nullable();
            $table->string('resume_file')->nullable();
            $table->enum('job_seeking_status', ['actively_looking','open_to_offers','not_looking'])->default('actively_looking');
            $table->decimal('expected_salary_min', 10, 2)->nullable();
            $table->decimal('expected_salary_max', 10, 2)->nullable();
            $table->string('preferred_job_type')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->boolean('is_profile_public')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('job_seeking_status');
        });

        // Educations
        Schema::create('educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_seeker_profile_id');
            $table->string('degree');
            $table->string('field_of_study');
            $table->string('institution_name');
            $table->string('board_or_university')->nullable();
            $table->unsignedSmallInteger('passing_year');
            $table->string('result')->nullable();
            $table->string('result_value')->nullable();
            $table->boolean('is_highest_education')->default(false);
            $table->timestamps();
            $table->foreign('job_seeker_profile_id')->references('id')->on('job_seeker_profiles')->onDelete('cascade');
        });

        // Work Experiences
        Schema::create('work_experiences', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_seeker_profile_id');
            $table->string('company_name');
            $table->string('job_title');
            $table->string('employment_type')->nullable();
            $table->string('location')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('responsibilities')->nullable();
            $table->timestamps();
            $table->foreign('job_seeker_profile_id')->references('id')->on('job_seeker_profiles')->onDelete('cascade');
        });

        // Skills
        Schema::create('skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamps();
        });

        // Job Seeker ↔ Skills pivot
        Schema::create('job_seeker_skills', function (Blueprint $table) {
            $table->uuid('job_seeker_profile_id');
            $table->uuid('skill_id');
            $table->enum('proficiency', ['beginner','intermediate','advanced','expert'])->default('intermediate');
            $table->unsignedTinyInteger('years_used')->default(0);
            $table->primary(['job_seeker_profile_id', 'skill_id']);
            $table->foreign('job_seeker_profile_id')->references('id')->on('job_seeker_profiles')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });

        // Job Posts
        Schema::create('job_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('employer_profile_id');
            $table->uuid('category_id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->enum('job_type', ['full_time','part_time','contract','internship','freelance','remote','hybrid'])->default('full_time');
            $table->enum('experience_level', ['entry','junior','mid','senior','lead','executive'])->default('mid');
            $table->enum('gender_preference', ['any','male','female'])->default('any');
            $table->enum('salary_type', ['monthly','yearly','hourly','negotiable'])->default('monthly');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('currency', 3)->default('BDT');
            $table->boolean('show_salary')->default(true);
            $table->boolean('is_remote')->default(false);
            $table->string('location')->nullable();
            $table->string('district')->nullable();
            $table->string('country', 2)->default('BD');
            $table->unsignedTinyInteger('experience_years_min')->default(0);
            $table->unsignedTinyInteger('experience_years_max')->nullable();
            $table->string('education_level')->nullable();
            $table->unsignedSmallInteger('vacancies')->default(1);
            $table->enum('status', ['draft','published','archived','expired'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_hot')->default(false);
            $table->boolean('is_urgent')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->date('application_deadline');
            $table->timestamp('featured_until')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->unsignedInteger('application_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('employer_profile_id')->references('id')->on('employer_profiles')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('restrict');
            $table->index('status');
            $table->index('is_featured');
            $table->index('application_deadline');
            $table->index('published_at');
        });

        // Job Post ↔ Skills pivot
        Schema::create('job_post_skills', function (Blueprint $table) {
            $table->uuid('job_post_id');
            $table->uuid('skill_id');
            $table->boolean('is_required')->default(true);
            $table->primary(['job_post_id', 'skill_id']);
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('skill_id')->references('id')->on('skills')->onDelete('cascade');
        });

        // Applications
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_post_id');
            $table->uuid('job_seeker_profile_id');
            $table->text('cover_letter')->nullable();
            $table->string('resume_snapshot')->nullable();
            $table->decimal('expected_salary', 12, 2)->nullable();
            $table->enum('status', ['pending','reviewed','shortlisted','interview','offered','hired','rejected','withdrawn'])->default('pending');
            $table->text('employer_notes')->nullable();
            $table->uuid('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->boolean('is_seen_by_seeker')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['job_post_id', 'job_seeker_profile_id']);
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('job_seeker_profile_id')->references('id')->on('job_seeker_profiles')->onDelete('cascade');
            $table->index('status');
        });

        // Saved Jobs
        Schema::create('saved_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('job_seeker_profile_id');
            $table->uuid('job_post_id');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['job_seeker_profile_id', 'job_post_id']);
            $table->foreign('job_seeker_profile_id')->references('id')->on('job_seeker_profiles')->onDelete('cascade');
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
        });

        // Search Logs
        Schema::create('search_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('query')->nullable();
            $table->uuid('category_id')->nullable();
            $table->string('location')->nullable();
            $table->string('job_type')->nullable();
            $table->unsignedSmallInteger('results_count')->default(0);
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('user_id');
            $table->index('query');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_logs');
        Schema::dropIfExists('saved_jobs');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('job_post_skills');
        Schema::dropIfExists('job_posts');
        Schema::dropIfExists('job_seeker_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('work_experiences');
        Schema::dropIfExists('educations');
        Schema::dropIfExists('job_seeker_profiles');
        Schema::dropIfExists('employer_profiles');
        Schema::dropIfExists('industries');
        Schema::dropIfExists('categories');
    }
};
