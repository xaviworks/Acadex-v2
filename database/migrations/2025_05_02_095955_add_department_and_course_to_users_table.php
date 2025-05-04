<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->after('is_active')->constrained('departments')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->after('department_id')->constrained('courses')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['course_id']);
            $table->dropColumn(['department_id', 'course_id']);
        });
    }
};
