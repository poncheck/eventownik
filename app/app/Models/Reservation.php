<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reservation extends Model
{
    protected $fillable = [
        'reference', 'first_name', 'last_name', 'email', 'phone',
        'event_type_id', 'room_id', 'menu_id',
        'event_date', 'event_time', 'duration_hours', 'guest_count',
        'notes', 'status', 'internal_notes', 'total_price',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Reservation $reservation) {
            $reservation->reference = strtoupper(Str::random(3)) . '-' . date('ymd') . '-' . strtoupper(Str::random(3));
        });
    }

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'new'               => 'Nowe',
            'contacted'         => 'W kontakcie',
            'awaiting_payment'  => 'Oczekuje na płatność',
            'confirmed'         => 'Potwierdzone',
            'completed'         => 'Zrealizowane',
            'cancelled'         => 'Anulowane',
            default             => $this->status,
        };
    }
}
