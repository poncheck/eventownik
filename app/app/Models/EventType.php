<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventType extends Model
{
    protected $fillable = ['name', 'slug', 'active', 'sort_order'];

    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeMenus(): HasMany
    {
        return $this->hasMany(Menu::class)->where('active', true)->orderBy('sort_order');
    }
}
