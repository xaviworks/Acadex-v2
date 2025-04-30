<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'student_id', 'score',
        'is_deleted', 'created_by', 'updated_by'
    ];
}
