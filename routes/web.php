<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChairpersonController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\ScoreController;
use App\Http\Controllers\AcademicPeriodController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', function () {
    return view('welcome');
});

// Dashboard (after login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile Management
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ----------------------------------------
// Chairperson Routes
// ----------------------------------------

Route::prefix('chairperson')->middleware(['auth'])->name('chairperson.')->group(function () {
    Route::get('/instructors', [ChairpersonController::class, 'manageInstructors'])->name('instructors');
    Route::get('/instructors/create', [ChairpersonController::class, 'createInstructor'])->name('createInstructor');
    Route::post('/instructors/store', [ChairpersonController::class, 'storeInstructor'])->name('storeInstructor');
    Route::post('/instructors/{id}/deactivate', [ChairpersonController::class, 'deactivateInstructor'])->name('deactivateInstructor');

    Route::get('/assign-subjects', [ChairpersonController::class, 'assignSubjects'])->name('assignSubjects');
    Route::post('/assign-subjects/store', [ChairpersonController::class, 'storeAssignedSubject'])->name('storeAssignedSubject');

    Route::get('/grades', [ChairpersonController::class, 'viewGrades'])->name('viewGrades');
    Route::get('/students-by-year', [ChairpersonController::class, 'viewStudentsPerYear'])->name('studentsByYear');
});

// ----------------------------------------
// Instructor Routes
// ----------------------------------------

Route::prefix('instructor')->middleware(['auth'])->name('instructor.')->group(function () {
    // Students
    Route::get('/students', [InstructorController::class, 'manageStudents'])->name('manageStudents');
    Route::delete('/students/{student}/drop', [InstructorController::class, 'dropStudent'])->name('dropStudent');
    Route::get('/students/enroll', [InstructorController::class, 'addStudentForm'])->name('addStudentForm');
    Route::post('/students/enroll', [InstructorController::class, 'enrollStudent'])->name('enrollStudent');

    // Manage Grades (Subject Card → Term View)
    Route::get('/grades', [InstructorController::class, 'manageGrades'])->name('manageGrades');
    Route::post('/grades/save', [InstructorController::class, 'saveGrades'])->name('saveGrades');

    // Activities (Quiz, OCR, Exam)
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{id}/delete', [ActivityController::class, 'delete'])->name('activities.delete');

    // Scores
    Route::get('/scores', [ScoreController::class, 'index'])->name('scores');
    Route::post('/scores/save', [ScoreController::class, 'save'])->name('scores.save');

    // Final Grades
    Route::get('/final-grades', [ScoreController::class, 'finalGrades'])->name('finalGrades');
});

// ----------------------------------------
// Dean Routes
// ----------------------------------------

Route::prefix('dean')->middleware(['auth'])->name('dean.')->group(function () {
    Route::get('/instructors', [DeanController::class, 'viewInstructors'])->name('instructors');
    Route::get('/students', [DeanController::class, 'viewStudents'])->name('students');
    Route::get('/grades', [DeanController::class, 'viewGrades'])->name('grades');
});

// ----------------------------------------
// Admin Routes
// ----------------------------------------

Route::prefix('admin')->middleware(['auth'])->name('admin.')->group(function () {
    // Departments
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::get('/departments/create', [AdminController::class, 'createDepartment'])->name('createDepartment');
    Route::post('/departments/store', [AdminController::class, 'storeDepartment'])->name('storeDepartment');

    // Courses
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('createCourse');
    Route::post('/courses/store', [AdminController::class, 'storeCourse'])->name('storeCourse');

    // Subjects
    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('createSubject');
    Route::post('/subjects/store', [AdminController::class, 'storeSubject'])->name('storeSubject');

    // Academic Periods
    Route::get('/academic-periods', [AcademicPeriodController::class, 'index'])->name('academicPeriods');
    Route::post('/academic-periods/generate', [AcademicPeriodController::class, 'generate'])->name('academicPeriods.generate');
});

// ----------------------------------------
// Authentication Routes
require __DIR__.'/auth.php';
