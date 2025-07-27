<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\ScheduledCall;

class CallCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledCall;
    public $call;

    public function __construct(ScheduledCall $scheduledCall, $call)
    {
        $this->scheduledCall = $scheduledCall;
        $this->call = $call;
    }

    public function build()
    {
        return $this->subject('Your Call is Completed')
            ->view('emails.call_completed')
            ->with([
                'call' => $this->call,
            ]);
    }
}
