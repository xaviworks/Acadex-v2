<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year', 'semester', 'is_deleted', 'created_by', 'updated_by'
    ];
}
