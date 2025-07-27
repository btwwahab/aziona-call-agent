<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Call;
use App\Models\ScheduledCall;
use Illuminate\Support\Carbon;

class CallController extends Controller
{
    // Fetch dashboard stats, logs, scheduled calls
    public function dashboardData()
    {
        $totalCalls = Call::count();
        $uptime = '99.8%'; // Placeholder, calculate as needed
        $avgDuration = Call::whereNotNull('duration')->avg('duration');
        $successRate = Call::where('status', 'completed')->count() / max(1, $totalCalls) * 100;

        $calls = Call::orderByDesc('created_at')->limit(20)->get();
        $scheduled = ScheduledCall::orderByDesc('scheduled_for')->limit(20)->get();

        return response()->json([
            'totalCalls' => $totalCalls,
            'uptime' => $uptime,
            'avgDuration' => $avgDuration ? round($avgDuration / 60, 1) . 'm' : '0m',
            'successRate' => round($successRate, 1) . '%',
            'calls' => $calls,
            'scheduled' => $scheduled,
        ]);
    }

    // Place a call (outbound)
    public function placeCall(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
        ]);

        // Prepare Vapi.ai API call
        $apiKey = env('VAPI_API_KEY');

        $assistantId = env('VAPI_AGENT_ID');
        $phoneNumberId = env('VAPI_FROM_NUMBER');
        $toNumber = $request->phone_number;
        $payload = [
            'assistantId' => $assistantId,
            'phoneNumberId' => $phoneNumberId,
            'customer' => [
                'number' => $toNumber,
            ],
        ];

        $response = null;
        $callStatus = 'initiated';
        $callId = null;
        $errorMessage = null;
        try {
            $apiResponse = \Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.vapi.ai/call/phone', $payload);
            $response = $apiResponse->json();
            if (isset($response['id'])) {
                $callId = $response['id'];  // This is the correct call ID from Vapi
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

        // Save call log
        $call = Call::create([
            'phone_number' => $toNumber,
            'type' => 'outbound',
            'status' => $callStatus,
            'call_id' => $callId,
            'transcript' => $errorMessage, // Store error for user feedback
        ]);

        return response()->json([
            'success' => $callStatus !== 'failed',
            'call' => $call,
            'vapi_response' => $response,
            'error' => $errorMessage,
        ]);
    }

    // Schedule a call
    public function scheduleCall(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'scheduled_for' => 'required|date',
            'note' => 'nullable|string',
        ]);
        $request->validate([
            'phone_number' => 'required|string',
            'email' => 'required|email',
            'scheduled_for' => 'required|date_format:Y-m-d H:i:s',
            'note' => 'nullable|string',
        ]);
        // Always parse scheduled_for as UTC for reliability
        $scheduledFor = Carbon::parse($request->scheduled_for, 'UTC');
        \Log::info('Scheduling call', [
            'input' => $request->scheduled_for,
            'scheduled_for_utc' => $scheduledFor->toDateTimeString(),
        ]);
        $scheduled = ScheduledCall::create([
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'scheduled_for' => $scheduledFor,
            'note' => $request->note,
            'status' => 'pending',
        ]);
        // Dispatch job with delay until scheduled time
        $delay = $scheduledFor->diffInSeconds(Carbon::now('UTC'), false);
        if ($delay > 0) {
            \App\Jobs\ProcessSingleScheduledCall::dispatch($scheduled->id)->delay($delay);
        } else {
            \App\Jobs\ProcessSingleScheduledCall::dispatch($scheduled->id);
        }
        return response()->json(['success' => true, 'scheduled' => $scheduled]);
    }
    // Vapi.ai webhook to update call status and transcript
    public function vapiWebhook(Request $request)
    {
        // Basic security: check for a secret header
        $expectedSecret = env('VAPI_WEBHOOK_SECRET', 'changeme');
        $providedSecret = $request->header('X-Vapi-Secret');
        if ($providedSecret !== $expectedSecret) {
            \Log::warning('Webhook rejected: invalid secret', ['provided' => $providedSecret]);
            return response()->json(['error' => 'Unauthorized'], 401);
        } {
            $data = $request->all();
            // Example Vapi webhook payload: { call_id, status, duration, transcript, started_at, ended_at }
            if (!isset($data['call_id'])) {
                return response()->json(['error' => 'No call_id'], 400);
            }
            $call = Call::where('call_id', $data['call_id'])->first();
            if (!$call) {
                // Optionally, try to match by phone_number and status if needed
                return response()->json(['error' => 'Call not found'], 404);
            }
            if (isset($data['status'])) {
                $call->status = $data['status'];
            }
            if (isset($data['duration'])) {
                $call->duration = $data['duration'];
            }
            if (isset($data['transcript'])) {
                $call->transcript = $data['transcript'];
            }
            if (isset($data['started_at'])) {
                $call->started_at = $data['started_at'];
            }
            if (isset($data['ended_at'])) {
                $call->ended_at = $data['ended_at'];
            }
            $call->save();
            return response()->json(['success' => true]);
        }
    }
}