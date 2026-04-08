<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Rezerwacja imprezy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pl.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .menu-card.selected { border-color: #f59e0b; background-color: #fffbeb; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

<div class="max-w-3xl mx-auto px-4 py-10"
     x-data="reservationForm()"
     x-init="init()">

    {{-- Nagłówek --}}
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-gray-800">Rezerwacja imprezy</h1>
        <p class="text-gray-500 mt-2">Wypełnij formularz, a skontaktujemy się z Tobą w ciągu 24 godzin.</p>
    </div>

    {{-- Błędy walidacji --}}
    @if ($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 rounded-xl p-4 mb-6">
        <p class="font-semibold mb-1">Popraw poniższe błędy:</p>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('reservation.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Dane osobowe --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-5">Dane kontaktowe</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Imię <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('first_name') border-red-400 @enderror"
                           placeholder="Jan" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Nazwisko <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('last_name') border-red-400 @enderror"
                           placeholder="Kowalski" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Adres e-mail <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('email') border-red-400 @enderror"
                           placeholder="jan@example.pl" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Telefon</label>
                    <input type="tel" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                           placeholder="+48 123 456 789">
                </div>
            </div>
        </div>

        {{-- Szczegóły imprezy --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-5">Szczegóły imprezy</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                {{-- Rodzaj imprezy --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Rodzaj imprezy <span class="text-red-500">*</span></label>
                    <select name="event_type_id"
                            x-model="eventTypeId"
                            @change="onEventTypeChange()"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('event_type_id') border-red-400 @enderror"
                            required>
                        <option value="">— Wybierz rodzaj imprezy —</option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sala --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Preferowana sala</label>
                    <select name="room_id"
                            x-model="roomId"
                            @change="onRoomChange()"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="">— Brak preferencji —</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} ({{ $room->capacity_min }}–{{ $room->capacity_max }} os.)
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Data --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Data imprezy <span class="text-red-500">*</span></label>
                    <input type="text" id="event_date" name="event_date" value="{{ old('event_date') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('event_date') border-red-400 @enderror"
                           placeholder="dd.mm.rrrr" readonly required>
                </div>

                {{-- Godzina --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Godzina rozpoczęcia <span class="text-red-500">*</span></label>
                    <input type="text" id="event_time" name="event_time" value="{{ old('event_time') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('event_time') border-red-400 @enderror"
                           placeholder="12:00" readonly required>
                </div>

                {{-- Czas trwania --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Czas trwania <span class="text-red-500">*</span></label>
                    <select name="duration_hours"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('duration_hours') border-red-400 @enderror"
                            required>
                        <option value="">— Wybierz —</option>
                        <option value="2" {{ old('duration_hours') == 2 ? 'selected' : '' }}>2 godziny</option>
                        <option value="3" {{ old('duration_hours') == 3 ? 'selected' : '' }}>3 godziny</option>
                        <option value="4" {{ old('duration_hours') == 4 ? 'selected' : '' }}>4 godziny</option>
                        <option value="5" {{ old('duration_hours') == 5 ? 'selected' : '' }}>5 godzin</option>
                        <option value="6" {{ old('duration_hours') == 6 ? 'selected' : '' }}>6 godzin</option>
                        <option value="8" {{ old('duration_hours') == 8 ? 'selected' : '' }}>8 godzin</option>
                        <option value="10" {{ old('duration_hours') == 10 ? 'selected' : '' }}>10 godzin</option>
                        <option value="12" {{ old('duration_hours') == 12 ? 'selected' : '' }}>Cały dzień (12h)</option>
                    </select>
                </div>

                {{-- Liczba gości --}}
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Liczba gości <span class="text-red-500">*</span></label>
                    <input type="number" name="guest_count" value="{{ old('guest_count') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 @error('guest_count') border-red-400 @enderror"
                           min="1" placeholder="50" required>
                </div>

                {{-- Uwagi --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Uwagi / życzenia specjalne</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                              placeholder="Np. dieta wegetariańska dla 5 osób, dekoracje w kolorze zielonym...">{{ old('notes') }}</textarea>
                </div>

            </div>
        </div>

        {{-- Wybór menu --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6" x-show="eventTypeId" x-cloak>
            <h2 class="text-lg font-semibold text-gray-700 mb-2">Propozycje menu</h2>
            <p class="text-sm text-gray-500 mb-5">Wybierz jedną z propozycji lub pozostaw bez wyboru — doprecyzujemy przy kontakcie.</p>

            <div x-show="loadingMenus" class="text-center py-8 text-gray-400">
                <svg class="animate-spin h-6 w-6 mx-auto mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                Ładowanie menu...
            </div>

            <div x-show="!loadingMenus && menus.length === 0 && eventTypeId" x-cloak class="text-sm text-gray-400 py-4">
                Brak propozycji menu dla wybranego rodzaju imprezy.
            </div>

            <div x-show="!loadingMenus" class="space-y-4" x-cloak>
                <template x-for="menu in menus" :key="menu.id">
                    <div class="menu-card border-2 border-gray-200 rounded-xl p-4 cursor-pointer transition-all"
                         :class="{ 'selected': selectedMenuId === menu.id }"
                         @click="selectedMenuId = (selectedMenuId === menu.id ? null : menu.id)">

                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-5 h-5 rounded-full border-2 border-gray-400 flex-shrink-0 mt-0.5 transition-colors"
                                     :class="selectedMenuId === menu.id ? 'border-amber-500 bg-amber-500' : ''">
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800" x-text="menu.name"></p>
                                    <p class="text-sm text-gray-500 mt-0.5" x-text="menu.description" x-show="menu.description"></p>
                                </div>
                            </div>
                            <span class="text-amber-600 font-semibold text-sm whitespace-nowrap"
                                  x-text="menu.price_per_person > 0 ? menu.price_per_person + ' zł/os.' : ''"></span>
                        </div>

                        <div class="mt-3 ml-8 grid grid-cols-1 sm:grid-cols-2 gap-1.5" x-show="menu.courses.length > 0">
                            <template x-for="course in menu.courses" :key="course.name">
                                <div class="flex gap-2 text-sm">
                                    <span class="text-gray-400 w-24 flex-shrink-0" x-text="course.type_label + ':'"></span>
                                    <span class="text-gray-700" x-text="course.name"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <input type="hidden" name="menu_id" :value="selectedMenuId">
        </div>

        {{-- Submit --}}
        <div class="text-center pt-2">
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-10 py-3.5 rounded-xl text-base transition-colors shadow-md">
                Wyślij zapytanie
            </button>
            <p class="text-xs text-gray-400 mt-3">
                Wysyłając formularz akceptujesz kontakt w celu realizacji rezerwacji.
            </p>
        </div>

    </form>
</div>

<script>
function reservationForm() {
    return {
        eventTypeId: '{{ old('event_type_id', '') }}',
        roomId: '{{ old('room_id', '') }}',
        menus: [],
        selectedMenuId: null,
        loadingMenus: false,
        datepicker: null,
        blockedDates: [],

        init() {
            // Datepicker na datę imprezy
            this.datepicker = flatpickr('#event_date', {
                locale: 'pl',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'd.m.Y',
                minDate: 'today',
                disable: this.blockedDates,
            });

            // Timepicker
            flatpickr('#event_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr: true,
                minuteIncrement: 30,
            });

            // Jeśli old() ma event_type_id — załaduj menu
            if (this.eventTypeId) {
                this.onEventTypeChange();
            }
            if (this.roomId) {
                this.onRoomChange();
            }
        },

        async onEventTypeChange() {
            this.menus = [];
            this.selectedMenuId = null;
            if (!this.eventTypeId) return;

            this.loadingMenus = true;
            try {
                const res = await fetch(`/api/menus?event_type_id=${this.eventTypeId}`);
                this.menus = await res.json();
            } finally {
                this.loadingMenus = false;
            }
        },

        async onRoomChange() {
            this.blockedDates = [];
            if (!this.roomId) {
                this.datepicker.set('disable', []);
                return;
            }
            try {
                const res = await fetch(`/api/blocked-dates?room_id=${this.roomId}`);
                this.blockedDates = await res.json();
                this.datepicker.set('disable', this.blockedDates);
            } catch (e) {
                // cicho ignorujemy
            }
        },
    };
}
</script>
</body>
</html>
