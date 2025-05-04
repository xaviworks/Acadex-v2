<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    use HasFactory;

    protected $table = 'student_subjects'; // 👈 This tells Laravel the correct table name

    protected $fillable = [
        'student_id',
        'subject_id',
    ];

    // ✅ Relationship to Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // ✅ Relationship to Subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
