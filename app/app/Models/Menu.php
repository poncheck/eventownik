<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'event_type_id', 'name', 'description',
        'price_per_person', 'active', 'sort_order',
    ];

    public function eventType(): BelongsTo
    {
        return $this->belongsTo(EventType::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(MenuCourse::class)->orderBy('sort_order');
    }

    public function proposalItems(): HasMany
    {
        return $this->hasMany(MenuProposalItem::class)->orderBy('sort_order');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /** Cena za osobę wyliczona z produktów. */
    public function calculatedPricePerPerson(): float
    {
        return $this->proposalItems->sum(fn (MenuProposalItem $item) => $item->pricePerPerson());
    }
}
