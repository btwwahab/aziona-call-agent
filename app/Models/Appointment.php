<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_name',
        'phone',
        'email',
        'scheduled_for',
        'note',
        'status',
    ];
}
