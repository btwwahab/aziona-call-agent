<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScheduledCall;
use App\Models\Call;
use Illuminate\Support\Carbon;

class ProcessScheduledCalls extends Command
{
    protected $signature = 'calls:process-scheduled';
    protected $description = 'Process and trigger scheduled calls via Vapi.ai';

    public function handle()
    {
        $appTz = config('app.timezone');
        $now = Carbon::now($appTz);

        \Log::info('ProcessScheduledCalls: Now (app timezone) = ' . $now->toDateTimeString());
        $calls = ScheduledCall::where('status', 'pending')->get();
        \Log::info('ProcessScheduledCalls: Found ' . $calls->count() . ' scheduled calls to check.');
        foreach ($calls as $scheduled) {
            $scheduledFor = Carbon::parse($scheduled->scheduled_for)->setTimezone($appTz);
            if ($scheduledFor->lt($now)) {
                // If scheduled time has passed and not processed, remove from DB
                \Log::info('Scheduled call expired and will be deleted', [
                    'id' => $scheduled->id,
                    'scheduled_for_app_tz' => $scheduledFor->toDateTimeString(),
                    'now_app_tz' => $now->toDateTimeString(),
                ]);
                $scheduled->delete();
                continue;
            }
            if ($scheduledFor->gt($now)) {
                // Not time yet
                continue;
            }
            \Log::info('Processing scheduled call', array_merge($scheduled->toArray(), [
                'scheduled_for_app_tz' => $scheduledFor->toDateTimeString(),
                'now_app_tz' => $now->toDateTimeString(),
            ]));
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
            \Log::info('ProcessScheduledCalls: Payload', ['payload' => $payload]);
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

                \Log::info('ProcessScheduledCalls: Vapi.ai response', ['response' => $response]);
                if (isset($response['id'])) {
                    $callId = $response['id'];
                    $callStatus = 'in_progress';
                } elseif (isset($response['error'])) {
                    $callStatus = 'failed';
                    $errorMessage = $response['error'];
                    \Log::error('Vapi.ai error: ' . json_encode($response));
                }
            } catch (\Exception $e) {
                $callStatus = 'failed';
                $errorMessage = $e->getMessage();
                \Log::error('Vapi.ai call failed: ' . $e->getMessage());
            }
            $call = Call::create([
                'phone_number' => $toNumber,
                'type' => 'outbound',
                'status' => $callStatus,
                'call_id' => $callId,
                'transcript' => $errorMessage ?? null,
            ]);
            // Dispatch event for processed scheduled call
            event(new \App\Events\ScheduledCallProcessed($scheduled, $call));
            // Remove scheduled call from DB after processing
            \Log::info('Scheduled call processed and will be deleted', [
                'id' => $scheduled->id,
                'status' => $callStatus,
                'call_id' => $callId,
            ]);
            $scheduled->delete();
        }
        $this->info('Processed scheduled calls.');
    }
}
