<?php
namespace App\Jobs;

use App\Models\ScheduledCall;
use App\Models\Call;
use App\Events\ScheduledCallProcessed;
use Illuminate\Support\Facades\Mail;
use App\Mail\CallCompletedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Carbon;

class ProcessSingleScheduledCall implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scheduledCallId;

    public function __construct($scheduledCallId)
    {
        $this->scheduledCallId = $scheduledCallId;
    }

    public function handle()
    {
        \Log::info('ProcessSingleScheduledCall: Job started', [
            'scheduledCallId' => $this->scheduledCallId
        ]);
        $scheduled = ScheduledCall::find($this->scheduledCallId);
        if (!$scheduled) {
            \Log::error('ProcessSingleScheduledCall: ScheduledCall not found', [
                'scheduledCallId' => $this->scheduledCallId
            ]);
            return;
        }
        $appTz = config('app.timezone');
        $now = Carbon::now($appTz);
        $scheduledFor = Carbon::parse($scheduled->scheduled_for)->setTimezone($appTz);
        \Log::info('ProcessSingleScheduledCall: Timing info', [
            'scheduled_for_app_tz' => $scheduledFor->toDateTimeString(),
            'now_app_tz' => $now->toDateTimeString(),
        ]);
        if ($scheduledFor->gt($now)) {
            \Log::info('ProcessSingleScheduledCall: Not time yet, exiting', [
                'scheduled_for_app_tz' => $scheduledFor->toDateTimeString(),
                'now_app_tz' => $now->toDateTimeString(),
            ]);
            return;
        }
        \Log::info('ProcessSingleScheduledCall: Processing scheduled call', $scheduled->toArray());
        $apiKey = env('VAPI_API_KEY');
        $assistantId = env('VAPI_AGENT_ID');
        $phoneNumberId = env('VAPI_FROM_NUMBER');
        $toNumber = $scheduled->phone_number;
        $payload = [
            'assistantId' => $assistantId,
            'phoneNumberId' => $phoneNumberId,
            'customer' => [
                'number' => $toNumber,
            ],
        ];
        \Log::info('ProcessSingleScheduledCall: Payload', ['payload' => $payload]);
        $callStatus = 'initiated';
        $callId = null;
        $response = null;
        $errorMessage = null;
        try {
            $apiResponse = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.vapi.ai/call/phone', $payload);
            $response = $apiResponse->json();
            \Log::info('ProcessSingleScheduledCall: Vapi.ai response', ['response' => $response]);
            if (isset($response['id'])) {
                $callId = $response['id'];
                $callStatus = 'in_progress';
            } elseif (isset($response['error'])) {
                $callStatus = 'failed';
                $errorMessage = $response['error'];
                \Log::error('ProcessSingleScheduledCall: Vapi.ai error', ['response' => $response]);
            }
        } catch (\Exception $e) {
            $callStatus = 'failed';
            $errorMessage = $e->getMessage();
            \Log::error('ProcessSingleScheduledCall: Exception', ['error' => $e->getMessage()]);
        }
        $call = Call::create([
            'phone_number' => $toNumber,
            'type' => 'outbound',
            'status' => $callStatus,
            'call_id' => $callId,
            'transcript' => $errorMessage ?? null,
        ]);
        \Log::info('ProcessSingleScheduledCall: Call saved', $call->toArray());
        event(new ScheduledCallProcessed($scheduled, $call));
        \Log::info('ProcessSingleScheduledCall: Event dispatched', [
            'scheduledCallId' => $scheduled->id,
            'callId' => $call->id,
        ]);
        // Send email notification if call completed
        if ($callStatus === 'completed' && !empty($scheduled->email)) {
            \Mail::to($scheduled->email)->send(new \App\Mail\CallCompletedMail($scheduled));
            \Log::info('ProcessSingleScheduledCall: Email sent to user', [
                'email' => $scheduled->email,
                'scheduledCallId' => $scheduled->id
            ]);
        }
        // Send email notification if call completed
        if ($callStatus === 'completed' && !empty($scheduled->email)) {
            \Mail::to($scheduled->email)->send(new \App\Mail\CallCompletedMail($scheduled));
            \Log::info('ProcessSingleScheduledCall: Email sent to user', [
                'email' => $scheduled->email,
                'scheduledCallId' => $scheduled->id
            ]);
        }
        $scheduled->delete();
        \Log::info('ProcessSingleScheduledCall: ScheduledCall deleted', [
            'scheduledCallId' => $scheduled->id
        ]);
    }
}
