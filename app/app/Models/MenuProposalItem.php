<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuProposalItem extends Model
{
    protected $fillable = ['menu_id', 'menu_product_id', 'percentage', 'sort_order'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(MenuProduct::class, 'menu_product_id');
    }

    /** Cena za osobę dla tej pozycji. */
    public function pricePerPerson(): float
    {
        return $this->product->priceAtPercentage($this->percentage);
    }
}
