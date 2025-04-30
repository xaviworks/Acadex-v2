<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code');
            $table->string('subject_description')->nullable();
            $table->boolean('is_universal')->default(false); // For GE subjects
            $table->foreignId('academic_period_id')->nullable()->constrained('academic_periods')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_deleted')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
