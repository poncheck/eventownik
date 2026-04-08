<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomBlockout extends Model
{
    protected $fillable = ['room_id', 'date_from', 'date_to', 'reason'];

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
