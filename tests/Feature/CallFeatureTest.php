<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ScheduledCall;
use App\Models\Call;

class CallFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_schedule_a_call()
    {
        $payload = [
            'phone_number' => '+12025550123',
            'scheduled_for' => now()->addMinutes(5)->toDateTimeString(),
            'note' => 'Test scheduled call',
        ];

        $response = $this->postJson('/schedule', $payload);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('scheduled_calls', [
            'phone_number' => $payload['phone_number'],
            'note' => $payload['note'],
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function user_can_place_a_call()
    {
        $payload = [
            'phone_number' => '+12025550123',
        ];

        // Mock the Vapi.ai API call
        \Http::fake([
            'https://api.vapi.ai/v1/call' => \Http::response(['call_id' => 'test123'], 200),
        ]);

        $response = $this->postJson('/call', $payload);
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('calls', [
            'phone_number' => $payload['phone_number'],
            'status' => 'in_progress',
        ]);
    }
}
