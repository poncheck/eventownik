<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; color: #333; margin: 0; padding: 0; background: #f9f9f9; }
  .wrapper { max-width: 580px; margin: 30px auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb; }
  .header { background: #1e293b; padding: 24px 32px; }
  .header h1 { color: #fff; margin: 0; font-size: 20px; }
  .header p { color: #94a3b8; margin: 4px 0 0; font-size: 13px; }
  .body { padding: 28px 32px; }
  .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 14px; }
  .info-row:last-child { border-bottom: none; }
  .label { color: #6b7280; }
  .value { font-weight: 600; text-align: right; }
  .badge { display: inline-block; background: #fef3c7; color: #d97706; border-radius: 6px; padding: 2px 10px; font-size: 12px; font-weight: 600; }
  .footer { background: #f9fafb; padding: 16px 32px; font-size: 12px; color: #9ca3af; text-align: center; }
</style>
</head>
<body>
<div class="wrapper">
  <div class="header">
    <h1>🔔 Nowe zapytanie o rezerwację</h1>
    <p>#{{ $reservation->reference }}</p>
  </div>
  <div class="body">
    <h3 style="margin-bottom:12px;font-size:15px;">Dane klienta</h3>
    <div class="info-row">
      <span class="label">Imię i nazwisko</span>
      <span class="value">{{ $reservation->full_name }}</span>
    </div>
    <div class="info-row">
      <span class="label">E-mail</span>
      <span class="value">{{ $reservation->email }}</span>
    </div>
    <div class="info-row">
      <span class="label">Telefon</span>
      <span class="value">{{ $reservation->phone ?? '—' }}</span>
    </div>

    <h3 style="margin:20px 0 12px;font-size:15px;">Szczegóły imprezy</h3>
    <div class="info-row">
      <span class="label">Rodzaj</span>
      <span class="value"><span class="badge">{{ $reservation->eventType->name }}</span></span>
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
    @if($reservation->notes)
    <div style="margin-top:12px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;">
      <strong>Uwagi:</strong><br>{{ $reservation->notes }}
    </div>
    @endif
  </div>
  <div class="footer">
    Eventownik — Panel administracyjny
  </div>
</div>
</body>
</html>
