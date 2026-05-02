<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master list of all possible benefits
        Schema::create('benefits', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');                     // "Festival Bonus"
            $table->string('icon')->nullable();         // emoji "🎁"
            $table->string('category')->default('General'); // "Leave", "Finance", "Health"
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pivot: which benefits a job post offers
        Schema::create('job_post_benefits', function (Blueprint $table) {
            $table->uuid('job_post_id');
            $table->uuid('benefit_id');
            $table->primary(['job_post_id', 'benefit_id']);
            $table->foreign('job_post_id')->references('id')->on('job_posts')->onDelete('cascade');
            $table->foreign('benefit_id')->references('id')->on('benefits')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_post_benefits');
        Schema::dropIfExists('benefits');
    }
};
