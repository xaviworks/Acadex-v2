<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinalGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'subject_id', 'academic_period_id',
        'final_grade', 'remarks', 'is_deleted', 'created_by', 'updated_by'
    ];

    // Relationship with Student model
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Relationship with Subject model
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
