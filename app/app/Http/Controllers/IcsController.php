<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;

class IcsController extends Controller
{
    /**
     * Eksport pojedynczej rezerwacji (signed URL — dostępny bez logowania).
     */
    public function single(Request $request, Reservation $reservation): Response
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        return $this->icsResponse(
            $this->buildIcs([$reservation]),
            'rezerwacja-' . $reservation->reference . '.ics'
        );
    }

    /**
     * Eksport pojedynczej rezerwacji z panelu admina (wymaga auth).
     */
    public function adminSingle(Reservation $reservation): Response
    {
        return $this->icsResponse(
            $this->buildIcs([$reservation]),
            'rezerwacja-' . $reservation->reference . '.ics'
        );
    }

    /**
     * Eksport wszystkich aktywnych rezerwacji (wymaga auth).
     */
    public function adminAll(): Response
    {
        $reservations = Reservation::with(['eventType', 'room'])
            ->whereNotIn('status', ['cancelled'])
            ->orderBy('event_date')
            ->get();

        return $this->icsResponse(
            $this->buildIcs($reservations->all()),
            'eventownik-rezerwacje.ics'
        );
    }

    /**
     * Generuje link do eksportu ICS dla klienta (signed, ważny 1 rok).
     */
    public static function signedUrl(Reservation $reservation): string
    {
        return URL::signedRoute(
            'ics.single',
            ['reservation' => $reservation->id],
            now()->addYear()
        );
    }

    // -------------------------------------------------------------------------

    private function buildIcs(array $reservations): string
    {
        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Eventownik//PL',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'X-WR-CALNAME:Eventownik',
            'X-WR-TIMEZONE:Europe/Warsaw',
        ];

        foreach ($reservations as $r) {
            $r->loadMissing(['eventType', 'room']);

            $dtstart = $r->event_date->format('Ymd') . 'T' . str_replace(':', '', substr($r->event_time, 0, 5)) . '00';
            $dtend   = $r->event_date->copy()
                ->setTimeFromTimeString($r->event_time)
                ->addHours((int) $r->duration_hours)
                ->format('Ymd\THis');

            $description = implode('\n', array_filter([
                'Nr ref.: ' . $r->reference,
                'Status: ' . $r->status_label,
                'Gości: ' . $r->guest_count,
                $r->menu ? 'Menu: ' . $r->menu->name : null,
                $r->notes ? 'Uwagi: ' . $r->notes : null,
            ]));

            $lines = array_merge($lines, [
                'BEGIN:VEVENT',
                'UID:eventownik-' . $r->id . '@eventownik',
                'DTSTAMP:' . now()->format('Ymd\THis\Z'),
                'DTSTART;TZID=Europe/Warsaw:' . $dtstart,
                'DTEND;TZID=Europe/Warsaw:' . $dtend,
                'SUMMARY:' . $this->escapeIcs($r->eventType->name . ' – ' . $r->full_name),
                'DESCRIPTION:' . $this->escapeIcs($description),
                'LOCATION:' . $this->escapeIcs($r->room?->name ?? ''),
                'STATUS:' . ($r->status === 'confirmed' ? 'CONFIRMED' : 'TENTATIVE'),
                'END:VEVENT',
            ]);
        }

        $lines[] = 'END:VCALENDAR';

        return implode("\r\n", $lines) . "\r\n";
    }

    private function escapeIcs(string $value): string
    {
        return str_replace(["\r\n", "\n", "\r", ',', ';'], ['\\n', '\\n', '\\n', '\\,', '\\;'], $value);
    }

    private function icsResponse(string $content, string $filename): Response
    {
        return response($content, 200, [
            'Content-Type'        => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control'       => 'no-cache, no-store',
        ]);
    }
}
