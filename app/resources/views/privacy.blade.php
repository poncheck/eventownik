<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polityka prywatności – {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<div class="max-w-3xl mx-auto px-4 py-12">

    <div class="mb-8">
        <a href="{{ route('reservation.create') }}" class="text-amber-600 hover:text-amber-700 text-sm">
            ← Wróć do formularza rezerwacji
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6 text-gray-700 text-sm leading-relaxed">

        <h1 class="text-2xl font-bold text-gray-800">Polityka prywatności</h1>
        <p class="text-gray-500 text-xs">Ostatnia aktualizacja: {{ \Carbon\Carbon::now()->format('d.m.Y') }}</p>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">1. Administrator danych osobowych</h2>
            <p>
                Administratorem Twoich danych osobowych jest właściciel serwisu
                <strong>{{ config('app.name') }}</strong> dostępnego pod adresem
                <strong>{{ config('app.url') }}</strong>.
                W sprawach dotyczących ochrony danych osobowych możesz kontaktować się
                pod adresem e-mail: <strong>{{ config('mail.from.address') }}</strong>.
            </p>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">2. Jakie dane zbieramy</h2>
            <ul class="list-disc list-inside space-y-1">
                <li><strong>Imię i nazwisko</strong> – w celu identyfikacji zapytania</li>
                <li><strong>Adres e-mail</strong> – w celu kontaktu i przesłania potwierdzenia</li>
                <li><strong>Numer telefonu</strong> (opcjonalnie) – w celu kontaktu w sprawie rezerwacji</li>
                <li><strong>Szczegóły imprezy</strong> (data, typ, liczba gości, preferencje menu) – w celu obsługi rezerwacji</li>
                <li><strong>Adres IP</strong> – zapisywany automatycznie przez serwer w logach technicznych</li>
            </ul>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">3. Cel i podstawa prawna przetwarzania</h2>
            <p>Twoje dane przetwarzamy w następujących celach:</p>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li>
                    <strong>Obsługa zapytania rezerwacyjnego</strong> – na podstawie Twojej zgody (art. 6 ust. 1 lit. a RODO)
                    wyrażonej przez zaznaczenie checkboxa w formularzu.
                </li>
                <li>
                    <strong>Komunikacja w sprawie rezerwacji</strong> – przesyłanie potwierdzeń i informacji
                    związanych z Twoją rezerwacją.
                </li>
                <li>
                    <strong>Wymagania prawne</strong> – przechowywanie dokumentacji w zakresie wymaganym
                    przepisami rachunkowymi i podatkowymi (art. 6 ust. 1 lit. c RODO).
                </li>
            </ul>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">4. Okres przechowywania danych</h2>
            <p>
                Twoje dane osobowe przechowujemy przez okres niezbędny do realizacji rezerwacji
                oraz przez wymagany prawem okres po jej zakończeniu (co do zasady do 5 lat
                od końca roku kalendarzowego, w którym usługa została wykonana – zgodnie
                z przepisami o rachunkowości). Dane mogą być usunięte wcześniej na Twoje żądanie,
                o ile nie stoją temu na przeszkodzie inne przepisy prawa.
            </p>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">5. Odbiorcy danych</h2>
            <p>
                Twoje dane mogą być przekazywane dostawcom usług technicznych niezbędnych
                do działania serwisu (hosting, dostarczanie poczty elektronicznej).
                Nie sprzedajemy danych osobowych osobom trzecim i nie przekazujemy ich
                poza obszar EOG bez odpowiednich zabezpieczeń.
            </p>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">6. Twoje prawa</h2>
            <p>Przysługują Ci następujące prawa w związku z przetwarzaniem Twoich danych:</p>
            <ul class="list-disc list-inside mt-2 space-y-1">
                <li><strong>Prawo dostępu</strong> – możesz uzyskać informację o przetwarzanych danych</li>
                <li><strong>Prawo do sprostowania</strong> – możesz poprawić nieprawidłowe dane</li>
                <li><strong>Prawo do usunięcia</strong> – możesz żądać usunięcia danych („prawo do bycia zapomnianym")</li>
                <li><strong>Prawo do ograniczenia przetwarzania</strong> – możesz żądać wstrzymania przetwarzania danych</li>
                <li><strong>Prawo do przenoszenia danych</strong> – możesz otrzymać dane w ustrukturyzowanym formacie</li>
                <li><strong>Prawo do cofnięcia zgody</strong> – możesz w każdej chwili wycofać udzieloną zgodę
                    (cofnięcie zgody nie wpływa na zgodność z prawem przetwarzania, które miało miejsce
                    przed jej cofnięciem)</li>
                <li><strong>Prawo do wniesienia skargi</strong> – możesz wnieść skargę do Prezesa Urzędu
                    Ochrony Danych Osobowych (ul. Stawki 2, 00-193 Warszawa, <a href="https://uodo.gov.pl" class="text-amber-600 underline">uodo.gov.pl</a>)</li>
            </ul>
            <p class="mt-3">
                Aby skorzystać z powyższych praw, skontaktuj się z nami pod adresem:
                <strong>{{ config('mail.from.address') }}</strong>.
            </p>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">7. Pliki cookie i dane techniczne</h2>
            <p>
                Serwis używa sesyjnych plików cookie wyłącznie w celu obsługi formularza rezerwacji
                (zabezpieczenie CSRF) i uwierzytelnienia administratora. Nie używamy plików cookie
                do celów analitycznych, marketingowych ani śledzenia użytkowników.
            </p>
        </section>

        <section>
            <h2 class="text-base font-semibold text-gray-800 mb-2">8. Bezpieczeństwo danych</h2>
            <p>
                Stosujemy odpowiednie środki techniczne i organizacyjne chroniące Twoje dane
                przed nieuprawnionym dostępem, utratą lub zniszczeniem, w tym szyfrowanie
                transmisji danych (HTTPS/TLS), kontrolę dostępu do systemu oraz
                regularne kopie zapasowe bazy danych.
            </p>
        </section>

    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        {{ config('app.name') }} · Serwis rezerwacji imprez
    </p>
</div>
</body>
</html>
