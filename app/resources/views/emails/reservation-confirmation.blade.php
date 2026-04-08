<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f9f9f9; }
  .wrapper { max-width: 580px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
  .header { background: #f59e0b; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; }
  .body { padding: 28px 32px; }
  .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
  .info-row:last-child { border-bottom: none; }
  .label { color: #6b7280; }
  .value { font-weight: 600; text-align: right; }
  .footer { background: #f9fafb; padding: 16px 32px; font-size: 12px; color: #9ca3af; text-align: center; }
  .ref { background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; }
  .ref strong { color: #d97706; font-size: 16px; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>Zapytanie przyjęte ✓</h1>
  </div>
  <div class="body">
    <p>Dzień dobry <strong>{{ $reservation->first_name }}</strong>,</p>
    <p>Dziękujemy za przesłanie zapytania. Skontaktujemy się z Tobą w ciągu <strong>24 godzin roboczych</strong>.</p>

    <div class="ref">
      Numer referencyjny: <strong>#{{ $reservation->reference }}</strong><br>
      <span style="color:#6b7280;font-size:12px;">Zachowaj ten numer — będzie potrzebny przy kontakcie.</span>
    </div>

    <h3 style="margin-bottom:12px;font-size:15px;">Szczegóły Twojego zapytania</h3>
    <div class="info-row">
      <span class="label">Rodzaj imprezy</span>
      <span class="value">{{ $reservation->eventType->name }}</span>
    </div>
    <div class="info-row">
      <span class="label">Data</span>
      <span class="value">{{ $reservation->event_date->format('d.m.Y') }}</span>
    </div>
    <div class="info-row">
      <span class="label">Godzina</span>
      <span class="value">{{ $reservation->event_time }}</span>
    </div>
    <div class="info-row">
      <span class="label">Czas trwania</span>
      <span class="value">{{ $reservation->duration_hours }}h</span>
    </div>
    <div class="info-row">
      <span class="label">Liczba gości</span>
      <span class="value">{{ $reservation->guest_count }} os.</span>
    </div>
    @if($reservation->room)
    <div class="info-row">
      <span class="label">Preferowana sala</span>
      <span class="value">{{ $reservation->room->name }}</span>
    </div>
    @endif
    @if($reservation->menu)
    <div class="info-row">
      <span class="label">Wybrane menu</span>
      <span class="value">{{ $reservation->menu->name }}</span>
    </div>
    @endif

    <p style="margin-top:24px;font-size:13px;color:#6b7280;">
      Jeśli masz pytania, odpowiedz na tę wiadomość lub skontaktuj się z nami bezpośrednio.
    </p>
  </div>
  <div class="footer">
    © {{ date('Y') }} Eventownik — Wiadomość generowana automatycznie.
  </div>
</div>
</body>
</html>
