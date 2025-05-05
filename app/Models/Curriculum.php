<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    // Fix the table name because Laravel will assume "curricula"
    protected $table = 'curriculums';

    protected $fillable = [
        'course_id',
        'name',
        'is_active',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subjects()
    {
        return $this->hasMany(CurriculumSubject::class);
    }
}
