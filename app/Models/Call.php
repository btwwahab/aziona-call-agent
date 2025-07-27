<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone_number',
        'type',
        'status',
        'duration',
        'transcript',
        'started_at',
        'ended_at',
        'call_id',
    ];

    protected $dates = [
        'started_at',
        'ended_at',
    ];

    public function scheduledCall()
    {
        return $this->hasOne(ScheduledCall::class);
    }
}
