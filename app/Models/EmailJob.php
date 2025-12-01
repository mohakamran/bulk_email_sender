<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    protected $fillable = [
        'name',
        'email',
        'university',
        'research',
        'cv_path',
        'scheduled_at',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}
