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
        .menu-card { transition: border-color .15s, background .15s; }
        .menu-card.selected { border-color: #f59e0b; background-color: #fffbeb; }
        .product-row { transition: background .12s; }
        .product-row.checked { background: #fef9ec; }
        input[type=range]::-webkit-slider-thumb { background: #f59e0b; }
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

    <form action="{{ route('reservation.store') }}" method="POST" class="space-y-8" novalidate>
        @csrf

        {{-- ── Dane kontaktowe ─────────────────────────────────────────── --}}
        {{-- honeypot - ukryte pole, boty je wypełnią --}}
        <input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">
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
                    <label class="block text-sm font-medium text-gray-600 mb-1">E-mail <span class="text-red-500">*</span></label>
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

        {{-- ── Szczegóły imprezy ───────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-5">Szczegóły imprezy</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Rodzaj imprezy <span class="text-red-500">*</span></label>
                    <select name="event_type_id" x-model="eventTypeId" @change="onEventTypeChange()"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" required>
                        <option value="">— Wybierz rodzaj imprezy —</option>
                        @foreach($eventTypes as $type)
                            <option value="{{ $type->id }}" {{ old('event_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Preferowana sala</label>
                    <select name="room_id" x-model="roomId" @change="onRoomChange()"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <option value="">— Brak preferencji —</option>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                {{ $room->name }} ({{ $room->capacity_min }}–{{ $room->capacity_max }} os.)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Data imprezy <span class="text-red-500">*</span></label>
                    <input type="text" id="event_date" name="event_date" value="{{ old('event_date') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                           placeholder="dd.mm.rrrr" readonly required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Godzina rozpoczęcia <span class="text-red-500">*</span></label>
                    <input type="text" id="event_time" name="event_time" value="{{ old('event_time') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                           placeholder="12:00" readonly required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Czas trwania <span class="text-red-500">*</span></label>
                    <select name="duration_hours"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400" required>
                        <option value="">— Wybierz —</option>
                        @foreach([2=>2,3=>3,4=>4,5=>5,6=>6,8=>8,10=>10,12=>'Cały dzień (12h)'] as $val=>$label)
                        <option value="{{ $val }}" {{ old('duration_hours') == $val ? 'selected' : '' }}>
                            {{ is_int($label) ? $label . ' godzin' . ($label < 5 ? 'y' : '') : $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1">Liczba gości <span class="text-red-500">*</span></label>
                    <input type="number" name="guest_count" x-model.number="guestCount" value="{{ old('guest_count') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                           min="1" placeholder="50" required>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Uwagi / życzenia specjalne</label>
                    <textarea name="notes" rows="3"
                              class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                              placeholder="Np. dieta wegetariańska dla 5 osób...">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- ── Wybór menu ─────────────────────────────────────────────── --}}
        <div x-show="eventTypeId" x-cloak class="space-y-5">

            {{-- Propozycje menu --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-700 mb-1">Propozycje menu</h2>
                <p class="text-sm text-gray-400 mb-5">Wybierz jedną z gotowych propozycji lub zbuduj własne menu poniżej.</p>

                <div x-show="loadingMenus" class="text-center py-6 text-gray-400 text-sm">Ładowanie...</div>

                <div x-show="!loadingMenus" class="space-y-3" x-cloak>
                    <template x-for="menu in menus" :key="menu.id">
                        <div class="menu-card border-2 border-gray-200 rounded-xl p-4 cursor-pointer"
                             :class="{ 'selected': menuType === 'proposal' && selectedMenuId === menu.id }"
                             @click="selectProposal(menu.id)">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex-shrink-0 mt-0.5 transition-colors"
                                         :class="menuType === 'proposal' && selectedMenuId === menu.id
                                            ? 'border-amber-500 bg-amber-500' : 'border-gray-300'"></div>
                                    <div>
                                        <p class="font-semibold text-gray-800" x-text="menu.name"></p>
                                        <p class="text-sm text-gray-500" x-text="menu.description" x-show="menu.description"></p>
                                    </div>
                                </div>
                            </div>
                            {{-- Dania w propozycji --}}
                            <div class="mt-3 ml-8 space-y-1" x-show="menu.courses && menu.courses.length">
                                <template x-for="course in menu.courses" :key="course.name">
                                    <div class="flex gap-2 text-sm">
                                        <span class="text-gray-400 w-28 flex-shrink-0" x-text="course.type_label + ':'"></span>
                                        <span class="text-gray-700" x-text="course.name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div x-show="menus.length === 0 && eventTypeId && !loadingMenus"
                         class="text-sm text-gray-400 text-center py-2">
                        Brak gotowych propozycji — skonfiguruj własne menu poniżej.
                    </div>
                </div>
            </div>

            {{-- ── Custom menu builder ──────────────────────────────── --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-lg font-semibold text-gray-700">Własne menu</h2>
                    <button type="button"
                            @click="toggleCustomMenu()"
                            class="text-sm font-medium px-4 py-1.5 rounded-lg border transition-colors"
                            :class="menuType === 'custom'
                                ? 'bg-amber-500 text-white border-amber-500'
                                : 'border-gray-300 text-gray-600 hover:border-amber-400'">
                        <span x-text="menuType === 'custom' ? '✓ Wybrany' : 'Skonfiguruj własne'"></span>
                    </button>
                </div>
                <p class="text-sm text-gray-400 mb-5">
                    Wybierz dania z każdej kategorii. Dla półmisków ustaw proporcje — możesz wybrać więcej niż 100% (np. 60% drób + 60% wołowe = każdy gość może spróbować obu).
                </p>

                <div x-show="loadingProducts" class="text-center py-6 text-gray-400 text-sm">Ładowanie katalogu...</div>

                <div x-show="!loadingProducts && productCategories.length > 0" x-cloak class="space-y-6">
                    <template x-for="cat in productCategories" :key="cat.category">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-2"
                                x-text="cat.label"></h3>
                            <div class="space-y-2">
                                <template x-for="product in cat.items" :key="product.id">
                                    <div class="product-row rounded-xl border border-gray-200 p-3 transition-colors"
                                         :class="{ 'checked': isSelected(product.id) }">

                                        {{-- Wiersz: checkbox + nazwa + % slider --}}
                                        <div class="flex items-center gap-3">
                                            <input type="checkbox"
                                                   :id="'prod-' + product.id"
                                                   :checked="isSelected(product.id)"
                                                   @change="toggleProduct(product)"
                                                   class="w-4 h-4 accent-amber-500 flex-shrink-0 cursor-pointer">
                                            <label :for="'prod-' + product.id"
                                                   class="flex-1 cursor-pointer">
                                                <span class="text-sm font-medium text-gray-800" x-text="product.name"></span>
                                                <span x-show="product.serving_type === 'platter'"
                                                      class="ml-2 text-xs text-amber-600 font-medium">półmisek</span>
                                                <span x-show="product.serving_type === 'plate'"
                                                      class="ml-2 text-xs text-gray-400">talerz</span>
                                                <p x-show="product.description" x-text="product.description"
                                                   class="text-xs text-gray-400 mt-0.5"></p>
                                            </label>
                                        </div>

                                        {{-- Suwak procentów (tylko dla produktów z has_percentage) --}}
                                        <div x-show="isSelected(product.id) && product.has_percentage"
                                             class="mt-3 ml-7 space-y-1">
                                            <div class="flex items-center gap-3">
                                                <input type="range"
                                                       :min="product.min_percentage"
                                                       max="200"
                                                       step="10"
                                                       :value="getPercentage(product.id)"
                                                       @input="setPercentage(product.id, $event.target.value)"
                                                       class="flex-1 accent-amber-500">
                                                <span class="w-16 text-right text-sm font-semibold text-amber-700"
                                                      x-text="getPercentage(product.id) + '%'"></span>
                                            </div>
                                            <p class="text-xs text-gray-400">
                                                Min. <span x-text="product.min_percentage"></span>% •
                                                100% = pełna porcja na osobę
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    {{-- Podsumowanie wyboru --}}
                    <div x-show="menuType === 'custom' && customItems.length > 0"
                         class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm">
                        <p class="font-semibold text-amber-800 mb-2">Twój wybór (<span x-text="customItems.length"></span> pozycji):</p>
                        <template x-for="item in customItems" :key="item.product_id">
                            <div class="flex justify-between text-gray-700 py-0.5">
                                <span x-text="item.name"></span>
                                <span x-show="item.has_percentage" x-text="item.percentage + '%'"
                                      class="text-amber-700 font-medium"></span>
                                <span x-show="!item.has_percentage" class="text-gray-400 text-xs">1 szt./os.</span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ukryte pola --}}
        <input type="hidden" name="menu_id"   :value="menuType === 'proposal' ? selectedMenuId : ''">
        <input type="hidden" name="menu_type" :value="menuType ?? ''">

        {{-- Custom items jako JSON --}}
        <template x-if="menuType === 'custom'">
            <template x-for="(item, idx) in customItems" :key="item.product_id">
                <span>
                    <input type="hidden" :name="'custom_items[' + idx + '][product_id]'" :value="item.product_id">
                    <input type="hidden" :name="'custom_items[' + idx + '][percentage]'" :value="item.percentage">
                </span>
            </template>
        </template>

        {{-- ── Zgoda RODO ──────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">Zgoda na przetwarzanie danych</h2>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="rodo_consent" value="1" required
                       class="mt-1 h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400
                              @error('rodo_consent') border-red-400 @enderror">
                <span class="text-sm text-gray-600 leading-relaxed">
                    Wyrażam zgodę na przetwarzanie moich danych osobowych (imię, nazwisko, adres e-mail,
                    nr telefonu) przez administratora serwisu w celu obsługi zapytania rezerwacyjnego,
                    zgodnie z
                    <a href="{{ route('privacy') }}" target="_blank"
                       class="text-amber-600 underline hover:text-amber-700">Polityką prywatności</a>.
                    Podanie danych jest dobrowolne, lecz niezbędne do realizacji rezerwacji.
                    Dane będą przechowywane przez okres niezbędny do realizacji usługi i wymagany przepisami prawa.
                    <span class="text-red-500">*</span>
                </span>
            </label>
            @error('rodo_consent')
                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-400 mt-3">
                Masz prawo dostępu do swoich danych, ich sprostowania, usunięcia, ograniczenia przetwarzania
                oraz przenoszenia. Aby skorzystać z tych praw, skontaktuj się z administratorem.
            </p>
        </div>

        {{-- Submit --}}
        <div class="text-center pt-2">
            <button type="submit"
                    class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-10 py-3.5 rounded-xl text-base transition-colors shadow-md">
                Wyślij zapytanie
            </button>
        </div>

    </form>
</div>

<script>
function reservationForm() {
    return {
        // Stan formularza
        eventTypeId: '{{ old('event_type_id', '') }}',
        roomId:      '{{ old('room_id', '') }}',
        guestCount:  {{ old('guest_count', 100) }},

        // Propozycje
        menus:          [],
        selectedMenuId: null,
        loadingMenus:   false,

        // Custom menu
        menuType:          null,   // 'proposal' | 'custom' | null
        productCategories: [],
        customItems:       [],     // [{product_id, name, percentage, has_percentage}]
        loadingProducts:   false,

        // Datepicker
        datepicker:   null,
        blockedDates: [],

        // ── Init ────────────────────────────────────────────────────────
        init() {
            this.datepicker = flatpickr('#event_date', {
                locale:    'pl',
                dateFormat:'Y-m-d',
                altInput:  true,
                altFormat: 'd.m.Y',
                minDate:   'today',
                disable:   [],
            });
            flatpickr('#event_time', {
                enableTime: true,
                noCalendar: true,
                dateFormat: 'H:i',
                time_24hr:  true,
                minuteIncrement: 30,
            });
            if (this.eventTypeId) this.onEventTypeChange();
            if (this.roomId)      this.onRoomChange();
        },

        // ── Handlers ────────────────────────────────────────────────────
        async onEventTypeChange() {
            this.menus          = [];
            this.selectedMenuId = null;
            this.menuType       = null;
            this.customItems    = [];
            if (!this.eventTypeId) return;

            this.loadingMenus = true;
            try {
                const res  = await fetch(`/api/menus?event_type_id=${this.eventTypeId}`);
                this.menus = await res.json();
            } finally {
                this.loadingMenus = false;
            }

            // Załaduj produkty od razu
            await this.loadProducts();
        },

        async loadProducts() {
            if (this.productCategories.length > 0) return; // już załadowane
            this.loadingProducts = true;
            try {
                const res = await fetch(`/api/menu-products`);
                this.productCategories = await res.json();
            } finally {
                this.loadingProducts = false;
            }
        },

        async onRoomChange() {
            if (!this.roomId) {
                this.datepicker.set('disable', []);
                return;
            }
            try {
                const res = await fetch(`/api/blocked-dates?room_id=${this.roomId}`);
                this.blockedDates = await res.json();
                this.datepicker.set('disable', this.blockedDates);
            } catch {}
        },

        // ── Propozycje ───────────────────────────────────────────────────
        selectProposal(id) {
            if (this.menuType === 'proposal' && this.selectedMenuId === id) {
                this.selectedMenuId = null;
                this.menuType       = null;
            } else {
                this.selectedMenuId = id;
                this.menuType       = 'proposal';
                this.customItems    = [];
            }
        },

        // ── Custom menu ─────────────────────────────────────────────────
        toggleCustomMenu() {
            if (this.menuType === 'custom') {
                this.menuType    = null;
                this.customItems = [];
            } else {
                this.menuType       = 'custom';
                this.selectedMenuId = null;
            }
        },

        isSelected(productId) {
            return this.customItems.some(i => i.product_id === productId);
        },

        getPercentage(productId) {
            const item = this.customItems.find(i => i.product_id === productId);
            return item ? item.percentage : 100;
        },

        setPercentage(productId, value) {
            const item = this.customItems.find(i => i.product_id === productId);
            if (item) item.percentage = parseInt(value);
        },

        toggleProduct(product) {
            if (this.isSelected(product.id)) {
                this.customItems = this.customItems.filter(i => i.product_id !== product.id);
            } else {
                this.customItems.push({
                    product_id:      product.id,
                    name:            product.name,
                    has_percentage:  product.has_percentage,
                    percentage:      product.min_percentage ?? 100,
                });
                // Przełącz na custom jeśli nie było
                if (this.menuType !== 'custom') {
                    this.menuType       = 'custom';
                    this.selectedMenuId = null;
                }
            }
        },
    };
}
</script>
</body>
</html>
