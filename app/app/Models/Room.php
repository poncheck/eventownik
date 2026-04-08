<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'name', 'description', 'capacity_min', 'capacity_max',
        'price_per_hour', 'active',
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function blockouts(): HasMany
    {
        return $this->hasMany(RoomBlockout::class);
    }

    public function getBookedDates(): array
    {
        $reservationDates = $this->reservations()
            ->whereIn('status', ['confirmed', 'awaiting_payment', 'contacted', 'new'])
            ->pluck('event_date')
            ->map(fn ($d) => $d->format('Y-m-d'))
            ->toArray();

        $blockoutDates = [];
        foreach ($this->blockouts as $blockout) {
            $period = new \DatePeriod(
                new \DateTime($blockout->date_from),
                new \DateInterval('P1D'),
                (new \DateTime($blockout->date_to))->modify('+1 day')
            );
            foreach ($period as $date) {
                $blockoutDates[] = $date->format('Y-m-d');
            }
        }

        return array_unique(array_merge($reservationDates, $blockoutDates));
    }
}
