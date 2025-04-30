<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'department_id',  // Added
        'course_id',      // Added
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Subjects assigned to the instructor
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'instructor_id');
    }

    // Students created by the user
    public function createdStudents()
    {
        return $this->hasMany(Student::class, 'created_by');
    }

    // Subjects created by the user
    public function createdSubjects()
    {
        return $this->hasMany(Subject::class, 'created_by');
    }

    // Activities created by the user
    public function createdActivities()
    {
        return $this->hasMany(Activity::class, 'created_by');
    }

    // New: User belongs to a Department
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // New: User belongs to a Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
