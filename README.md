# Eventownik

System rezerwacji imprez z panelem administracyjnym. Klient wypełnia formularz online, administrator zarządza rezerwacjami, salami i menu z poziomu panelu.

## Stack

- **Laravel 13** + **Filament 4** (panel admina)
- **MySQL 8**
- **Nginx** + **PHP 8.4-FPM**
- **Mailpit** (przechwytywanie maili w środowisku dev)
- **Docker Compose**

## Funkcje

### Formularz klienta (`/`)
- Dane osobowe: imię, nazwisko, e-mail, telefon
- Wybór rodzaju imprezy (stypa, chrzciny, komunia, wesele, obiad weselny, szkolenie, konferencja i inne)
- Dynamiczne ładowanie propozycji menu per typ imprezy (AJAX)
- Wybór sali z blokowaniem zajętych terminów w datepickerze
- Po wysłaniu — e-mail z potwierdzeniem i numerem referencyjnym

### Panel admina (`/admin`)
- **Rezerwacje** — lista zapytań z filtrami (status, sala, typ imprezy), zmiana statusu, notatki wewnętrzne, ręczne potwierdzenie z wysyłką maila
- **Sale** — tworzenie i edycja sal (pojemność, cena za godzinę, opis), blokady terminów
- **Menu** — 5 propozycji menu per typ imprezy, edycja dań (przystawki, zupy, dania główne, desery)
- **Typy imprez** — konfigurowalna lista rodzajów imprez
- **Dashboard** — widżet ze statystykami (nowe zapytania, oczekujące płatności, potwierdzone)

### E-maile
| Zdarzenie | Odbiorca |
|---|---|
| Nowe zapytanie | Klient (potwierdzenie przyjęcia) |
| Nowe zapytanie | Admin (powiadomienie z danymi klienta) |
| Potwierdzenie rezerwacji | Klient (dane + informacje o płatności) |

## Uruchomienie

### Wymagania
- Docker + Docker Compose

### Instalacja

```bash
git clone https://github.com/poncheck/eventownik.git
cd eventownik
docker-compose up -d --build
```

Przy pierwszym starcie kontener automatycznie:
1. Tworzy `.env` z `.env.example` (jeśli nie istnieje)
2. Generuje `APP_KEY`
3. Uruchamia migracje
4. Seeduje bazę (typy imprez, przykładowe menu, sala, konto admina)

### Dostęp

| Adres | Opis |
|---|---|
| `http://localhost` | Formularz klienta |
| `http://localhost/admin` | Panel administracyjny |
| `http://localhost:8025` | Mailpit (podgląd maili) |

### Dane logowania (domyślne)

```
E-mail:  admin@eventownik.pl
Hasło:   changeme123!
```

> Zmień hasło i e-mail admina w pliku `app/.env` przed wdrożeniem produkcyjnym.

## Konfiguracja

Po pierwszym starcie edytuj `app/.env`:

```env
APP_URL=https://twojadomena.pl

# Mail
MAIL_FROM_ADDRESS="rezerwacje@twojadomena.pl"
ADMIN_EMAIL=admin@twojadomena.pl
ADMIN_PASSWORD=mocnehaslo

# Numer konta wyświetlany w mailach do klientów
PAYMENT_ACCOUNT="00 0000 0000 0000 0000 0000 0000"

# Produkcyjny SMTP (zamiast Mailpit)
MAIL_MAILER=smtp
MAIL_HOST=smtp.twojdostawca.pl
MAIL_PORT=587
MAIL_USERNAME=login
MAIL_PASSWORD=haslo
MAIL_ENCRYPTION=tls
```

Po zmianie `.env` zrestartuj kontener:

```bash
docker-compose restart app
```

## Struktura projektu

```
eventownik/
├── docker-compose.yml
├── docker/
│   ├── nginx/default.conf
│   └── php/
│       ├── Dockerfile
│       └── entrypoint.sh
└── app/                          # Aplikacja Laravel
    ├── app/
    │   ├── Filament/
    │   │   ├── Resources/        # Panel admina (Rezerwacje, Sale, Menu, Typy)
    │   │   └── Widgets/          # Widget statystyk na dashboardzie
    │   ├── Http/Controllers/
    │   │   └── ReservationController.php
    │   ├── Mail/                 # 3 klasy e-mail
    │   └── Models/               # Reservation, Room, Menu, EventType...
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    └── resources/views/
        ├── reservation/          # Formularz klienta + strona sukcesu
        └── emails/               # Szablony HTML maili
```

## Statusy rezerwacji

```
Nowe → W kontakcie → Oczekuje na płatność → Potwierdzone → Zrealizowane
                                                          ↘ Anulowane
```
