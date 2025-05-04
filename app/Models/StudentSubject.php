<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    use HasFactory;

    protected $table = 'student_subjects';

    protected $fillable = [
        'student_id',
        'subject_id',
        'is_deleted',
    ];

    public $timestamps = true; // 👈 Only if your table has created_at/updated_at

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
