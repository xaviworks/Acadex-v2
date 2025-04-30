<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id', 'term', 'type', 'title', 'number_of_items',
        'is_deleted', 'created_by', 'updated_by'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
