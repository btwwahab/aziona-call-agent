<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_webhook_rejects_invalid_secret()
    {
        $response = $this->postJson('/api/vapi-webhook', [
            'event' => 'call.completed',
            'call_id' => 'test123',
        ], ['X-Vapi-Secret' => 'wrong-secret']);
        $response->assertStatus(403);
    }

    public function test_webhook_accepts_valid_secret()
    {
        $secret = config('services.vapi.webhook_secret');
        $response = $this->postJson('/api/vapi-webhook', [
            'event' => 'call.completed',
            'call_id' => 'test123',
        ], ['X-Vapi-Secret' => $secret]);
        $response->assertStatus(200);
    }
}
