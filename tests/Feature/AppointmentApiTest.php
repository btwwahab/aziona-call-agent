<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Appointment;

class AppointmentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_appointment()
    {
        $response = $this->postJson('/api/appointments', [
            'person_name' => 'John Doe',
            'phone' => '+12345678901',
            'email' => 'john@example.com',
            'scheduled_for' => now()->addDay()->toDateTimeString(),
            'note' => 'Test appointment',
        ]);
        $response->assertStatus(201)
            ->assertJsonFragment(['person_name' => 'John Doe']);
    }

    public function test_can_list_appointments()
    {
        Appointment::factory()->create(['person_name' => 'Jane']);
        $response = $this->getJson('/api/appointments');
        $response->assertStatus(200)
            ->assertJsonFragment(['person_name' => 'Jane']);
    }

    public function test_can_update_appointment()
    {
        $appointment = Appointment::factory()->create();
        $response = $this->putJson('/api/appointments/' . $appointment->id, [
            'note' => 'Updated note',
        ]);
        $response->assertStatus(200)
            ->assertJsonFragment(['note' => 'Updated note']);
    }

    public function test_can_delete_appointment()
    {
        $appointment = Appointment::factory()->create();
        $response = $this->deleteJson('/api/appointments/' . $appointment->id);
        $response->assertStatus(200)
            ->assertJsonFragment(['success' => true]);
    }
}
