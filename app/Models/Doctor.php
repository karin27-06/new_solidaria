<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'code',
        'start_date',
        'state',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'state' => 'boolean',
    ];

}
