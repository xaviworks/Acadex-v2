<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('review_students', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('course_id'); // required
            $table->unsignedBigInteger('subject_id')->nullable(); // optional

            // Grouping and identity
            $table->string('list_name'); // Used to group bulk uploads

            // Student details
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->unsignedTinyInteger('year_level');

            // Status
            $table->boolean('is_confirmed')->default(false); // Will be true once instructor approves

            $table->timestamps();

            // Foreign keys
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('restrict');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_students');
    }
};
