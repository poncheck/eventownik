<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReservationMenuItem extends Model
{
    protected $fillable = ['reservation_id', 'menu_product_id', 'percentage'];

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(MenuProduct::class, 'menu_product_id');
    }
}
