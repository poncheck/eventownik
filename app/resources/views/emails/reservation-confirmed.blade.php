<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f9f9f9; }
  .wrapper { max-width: 580px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
  .header { background: #16a34a; padding: 28px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; }
  .body { padding: 28px 32px; }
  .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
  .info-row:last-child { border-bottom: none; }
  .label { color: #6b7280; }
  .value { font-weight: 600; text-align: right; }
  .payment-box { background: #f0fdf4; border: 1px solid #86efac; border-radius: 10px; padding: 16px 20px; margin: 20px 0; font-size: 14px; }
  .footer { background: #f9fafb; padding: 16px 32px; font-size: 12px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>Rezerwacja potwierdzona! 🎉</h1>
  </div>
  <div class="body">
    <p>Dzień dobry <strong>{{ $reservation->first_name }}</strong>,</p>
    <p>Z przyjemnością informujemy, że Twoja rezerwacja została <strong>potwierdzona</strong>. Poniżej znajdziesz wszystkie szczegóły.</p>

    <h3 style="margin-bottom:12px;font-size:15px;">Podsumowanie rezerwacji</h3>
    <div class="info-row">
      <span class="label">Nr referencyjny</span>
      <span class="value">#{{ $reservation->reference }}</span>
    </div>
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
      <span class="label">Sala</span>
      <span class="value">{{ $reservation->room->name }}</span>
    </div>
    @endif
    @if($reservation->menu)
    <div class="info-row">
      <span class="label">Menu</span>
      <span class="value">{{ $reservation->menu->name }}</span>
    </div>
    @endif
    @if($reservation->total_price)
    <div class="info-row">
      <span class="label">Całkowita kwota</span>
      <span class="value" style="color:#16a34a;">{{ number_format($reservation->total_price, 2, ',', ' ') }} PLN</span>
    </div>
    @endif

    <div class="payment-box">
      <strong>Informacje o płatności</strong><br><br>
      Prosimy o wpłatę zaliczki na rachunek bankowy:<br>
      <strong>{{ config('mail.payment_account', 'XX 0000 0000 0000 0000 0000 0000') }}</strong><br>
      Tytułem: <strong>Rezerwacja #{{ $reservation->reference }}</strong>
    </div>

    <p style="font-size:13px;color:#6b7280;">
      W razie pytań prosimy o kontakt pod adresem
      <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
      lub telefonicznie.
    </p>
  </div>
  <div class="footer">
    © {{ date('Y') }} Eventownik — Wiadomość generowana automatycznie.
  </div>
</div>
</body>
</html>
