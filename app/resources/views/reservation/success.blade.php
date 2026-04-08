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

    <a href="{{ route('reservation.create') }}"
       class="text-amber-600 hover:text-amber-700 text-sm font-medium underline">
        Złóż kolejne zapytanie
    </a>
</div>
</body>
</html>
