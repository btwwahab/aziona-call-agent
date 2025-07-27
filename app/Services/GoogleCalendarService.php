<?php

namespace App\Services;

use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{
    /**
     * Create a Google Calendar event for a scheduled call.
     *
     * @param string $summary
     * @param string $description
     * @param string $startUtc ISO8601 UTC datetime
     * @param string $endUtc ISO8601 UTC datetime
     * @param string|null $email
     * @return \Spatie\GoogleCalendar\Event
     */
    public function createEvent($summary, $description, $startUtc, $endUtc, $email = null)
    {
        $event = new Event;
        $event->name = $summary;
        $event->description = $description;
        $event->startDateTime = $startUtc;
        $event->endDateTime = $endUtc;
        if ($email) {
            $event->addAttendee(['email' => $email]);
        }
        $event->save();
        return $event;
    }
}
