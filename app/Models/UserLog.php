<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

    protected $table = 'user_logs';
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'event_type',
        'browser',
        'device',     
        'platform',   
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
