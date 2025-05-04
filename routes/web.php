<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ChairpersonController;
use App\Http\Controllers\InstructorController;
use App\Http\Controllers\DeanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AcademicPeriodController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\FinalGradeController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Welcome Page
Route::get('/', fn () => view('welcome'));

// Dashboard (after login)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile Management
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------
// Chairperson Routes
// -----------------------------
Route::prefix('chairperson')->middleware('auth')->name('chairperson.')->group(function () {
    Route::get('/instructors', [ChairpersonController::class, 'manageInstructors'])->name('instructors');
    Route::get('/instructors/create', [ChairpersonController::class, 'createInstructor'])->name('createInstructor');
    Route::post('/instructors/store', [ChairpersonController::class, 'storeInstructor'])->name('storeInstructor');
    Route::post('/instructors/{id}/deactivate', [ChairpersonController::class, 'deactivateInstructor'])->name('deactivateInstructor');

    Route::get('/assign-subjects', [ChairpersonController::class, 'assignSubjects'])->name('assignSubjects');
    Route::post('/assign-subjects/store', [ChairpersonController::class, 'storeAssignedSubject'])->name('storeAssignedSubject');

    Route::get('/grades', [ChairpersonController::class, 'viewGrades'])->name('viewGrades');
    Route::get('/students-by-year', [ChairpersonController::class, 'viewStudentsPerYear'])->name('studentsByYear');
});

// -----------------------------
// Instructor Routes
// -----------------------------
Route::prefix('instructor')->middleware('auth')->name('instructor.')->group(function () {
    Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

    // Student Management
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/enroll', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::delete('/students/{student}/drop', [StudentController::class, 'drop'])->name('students.drop');

    // Grades and Scores
    Route::get('/grades', [GradeController::class, 'index'])->name('grades.index');
    Route::get('/grades/partial', [GradeController::class, 'partial'])->name('grades.partial'); // ✅ Added this line
    Route::post('/grades/save', [GradeController::class, 'store'])->name('grades.store');
    Route::post('/grades/ajax-save-score', [GradeController::class, 'ajaxSaveScore'])->name('grades.ajaxSaveScore');

    // Final Grades
    Route::get('/final-grades', [FinalGradeController::class, 'index'])->name('final-grades.index');
    Route::post('/final-grades/generate', [FinalGradeController::class, 'generate'])->name('final-grades.generate');

    // Activities
    Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
    Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
    Route::post('/activities/store', [ActivityController::class, 'store'])->name('activities.store');
    Route::delete('/activities/{id}', [ActivityController::class, 'delete'])->name('activities.delete');
});

// -----------------------------
// Dean Routes
// -----------------------------
Route::prefix('dean')->middleware('auth')->name('dean.')->group(function () {
    Route::get('/instructors', [DeanController::class, 'viewInstructors'])->name('instructors');
    Route::get('/students', [DeanController::class, 'viewStudents'])->name('students');
    Route::get('/grades', [DeanController::class, 'viewGrades'])->name('grades');
    Route::get('/instructor/grades/partial', [GradeController::class, 'partial'])->name('instructor.grades.partial');
});

// -----------------------------
// Admin Routes
// -----------------------------
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/departments', [AdminController::class, 'departments'])->name('departments');
    Route::get('/departments/create', [AdminController::class, 'createDepartment'])->name('createDepartment');
    Route::post('/departments/store', [AdminController::class, 'storeDepartment'])->name('storeDepartment');

    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('createCourse');
    Route::post('/courses/store', [AdminController::class, 'storeCourse'])->name('storeCourse');

    Route::get('/subjects', [AdminController::class, 'subjects'])->name('subjects');
    Route::get('/subjects/create', [AdminController::class, 'createSubject'])->name('createSubject');
    Route::post('/subjects/store', [AdminController::class, 'storeSubject'])->name('storeSubject');

    Route::get('/academic-periods', [AcademicPeriodController::class, 'index'])->name('academicPeriods');
    Route::post('/academic-periods/generate', [AcademicPeriodController::class, 'generate'])->name('academicPeriods.generate');
});

// -----------------------------
// Auth Routes
// -----------------------------
require __DIR__.'/auth.php';
