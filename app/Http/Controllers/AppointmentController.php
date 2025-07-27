<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    public function index()
    {
        return response()->json(Appointment::orderBy('scheduled_for', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'scheduled_for' => 'required|date',
            'note' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        $appointment = Appointment::create($validated);
        return response()->json($appointment, 201);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update($request->all());
        return response()->json($appointment);
    }

    public function destroy($id)
    {
        Appointment::destroy($id);
        return response()->json(['success' => true]);
    }
}
