<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuCourse extends Model
{
    protected $fillable = ['menu_id', 'type', 'name', 'description', 'sort_order'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            'starter' => 'Przystawka',
            'soup'    => 'Zupa',
            'main'    => 'Danie główne',
            'dessert' => 'Deser',
            'other'   => 'Inne',
            default   => $type,
        };
    }
}
