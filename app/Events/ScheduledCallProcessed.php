<?php
namespace App\Events;

use App\Models\ScheduledCall;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScheduledCallProcessed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $scheduledCall;
    public $call;

    public function __construct(ScheduledCall $scheduledCall, $call)
    {
        $this->scheduledCall = $scheduledCall;
        $this->call = $call;
    }
}
