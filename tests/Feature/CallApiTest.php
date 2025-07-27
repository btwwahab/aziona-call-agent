<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Call;

class CallApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_place_call_invalid_number()
    {
        $response = $this->postJson('/call', [
            'phone_number' => '12345',
        ]);
        $response->assertStatus(422)
            ->assertJsonFragment(['error' => 'Invalid phone number format.']);
    }

    public function test_place_call_valid_number()
    {
        $response = $this->postJson('/call', [
            'phone_number' => '+12345678901',
        ]);
        $response->assertStatus(200);
    }
}
