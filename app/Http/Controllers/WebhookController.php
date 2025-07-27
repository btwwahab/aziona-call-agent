<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Call;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.vapi.webhook_secret');
        if ($request->header('X-Vapi-Secret') !== $secret) {
            abort(403, 'Invalid webhook secret');
        }
        $payload = $request->all();
        if (!isset($payload['event']) || !isset($payload['call_id'])) {
            Log::warning('Invalid Vapi webhook payload', ['payload' => $payload]);
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $call = Call::where('vapi_call_id', $payload['call_id'])->first();
        if (!$call) {
            Log::warning('Webhook for unknown call_id', ['call_id' => $payload['call_id']]);
            return response()->json(['error' => 'Call not found'], 404);
        }

        // Idempotency: prevent duplicate updates
        if ($payload['event'] === 'call.completed' && $call->status === 'completed') {
            Log::info('Duplicate call.completed event ignored', ['call_id' => $payload['call_id']]);
            return response()->json(['status' => 'already completed'], 200);
        }

        switch ($payload['event']) {
            case 'call.started':
                $call->status = 'in_progress';
                $call->save();
                Log::info('Call started', ['call_id' => $call->id]);
                break;
            case 'call.completed':
                $call->status = 'completed';
                $call->duration = $payload['duration'] ?? null;
                $call->save();
                Log::info('Call completed', ['call_id' => $call->id]);
                break;
            case 'transcript.available':
                $call->transcript = $payload['transcript'] ?? '';
                $call->save();
                Log::info('Transcript stored', ['call_id' => $call->id]);
                break;
            default:
                Log::warning('Unknown Vapi event', ['event' => $payload['event'], 'call_id' => $payload['call_id']]);
                return response()->json(['error' => 'Unknown event'], 400);
        }

        return response()->json(['success' => true]);
    }
}
