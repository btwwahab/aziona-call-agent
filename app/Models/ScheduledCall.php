<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'email',
        'scheduled_for',
        'note',
        'status',
        'call_id',
    ];


    /**
     * The attributes that should be cast.
     * This ensures scheduled_for is always handled in the app timezone.
     */
    protected $casts = [
        'scheduled_for' => 'datetime',
    ];

    public function call()
    {
        return $this->belongsTo(Call::class);
    }
}
