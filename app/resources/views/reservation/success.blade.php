<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zapytanie przyjęte</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center px-4">
<div class="max-w-lg w-full text-center bg-white rounded-2xl shadow-sm border border-gray-100 p-10">
    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-5">
        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>
    <h1 class="text-2xl font-bold text-gray-800 mb-2">Zapytanie przyjęte!</h1>
    <p class="text-gray-500 mb-6">
        Dziękujemy, <strong>{{ $reservation->first_name }}</strong>. Twoje zgłoszenie zostało zapisane.
        Skontaktujemy się z Tobą pod adresem <strong>{{ $reservation->email }}</strong> w ciągu 24 godzin.
    </p>

    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-left space-y-2 mb-6">
        <div class="flex justify-between">
            <span class="text-gray-500">Numer referencyjny:</span>
            <span class="font-mono font-semibold text-amber-700">{{ $reservation->reference }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Rodzaj imprezy:</span>
            <span class="font-medium">{{ $reservation->eventType->name }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Data:</span>
            <span class="font-medium">{{ $reservation->event_date->format('d.m.Y') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Liczba gości:</span>
            <span class="font-medium">{{ $reservation->guest_count }} os.</span>
        </div>
    </div>

    {{-- Dodaj do kalendarza --}}
    <div class="mb-6">
        <p class="text-xs text-gray-400 mb-3">Dodaj termin do swojego kalendarza:</p>
        <div class="flex flex-wrap justify-center gap-3">
            @php $icsUrl = \App\Http\Controllers\IcsController::signedUrl($reservation); @endphp

            {{-- ICS (Google, Apple, Outlook) --}}
            <a href="{{ $icsUrl }}"
               class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Pobierz plik .ics
            </a>

            {{-- Google Calendar --}}
            @php
                $gcStart  = $reservation->event_date->format('Ymd') . 'T' . str_replace(':', '', substr($reservation->event_time, 0, 5)) . '00';
                $gcEnd    = $reservation->event_date->copy()->setTimeFromTimeString($reservation->event_time)->addHours((int)$reservation->duration_hours)->format('Ymd\THis');
                $gcTitle  = urlencode($reservation->eventType->name . ' – ' . $reservation->full_name);
                $gcUrl    = "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$gcTitle}&dates={$gcStart}/{$gcEnd}";
            @endphp
            <a href="{{ $gcUrl }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-medium px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.5 3h-2.25V1.5h-1.5V3h-7.5V1.5h-1.5V3H4.5A1.5 1.5 0 003 4.5v15A1.5 1.5 0 004.5 21h15a1.5 1.5 0 001.5-1.5v-15A1.5 1.5 0 0019.5 3zm0 16.5h-15V9h15v10.5zm0-12h-15V4.5h2.25V6h1.5V4.5h7.5V6h1.5V4.5h2.25V7.5z"/>
                </svg>
                Google Calendar
            </a>
        </div>
    </div>

    <a href="{{ route('reservation.create') }}"
       class="text-amber-600 hover:text-amber-700 text-sm font-medium underline">
        Złóż kolejne zapytanie
    </a>
</div>
</body>
</html>
