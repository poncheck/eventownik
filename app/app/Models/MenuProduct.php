<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuProduct extends Model
{
    protected $fillable = [
        'category', 'serving_type', 'name', 'description',
        'price_per_person', 'active', 'sort_order',
    ];

    // ── Etykiety ────────────────────────────────────────────────────────────

    public static function categoryLabel(string $category): string
    {
        return match ($category) {
            'soup'         => 'Zupa',
            'starter'      => 'Przystawka',
            'main'         => 'Danie główne',
            'side_starchy' => 'Dodatek skrobiowy',
            'salad'        => 'Surówka / Sałata',
            'sauce'        => 'Sos',
            default        => $category,
        };
    }

    public static function categoryOptions(): array
    {
        return [
            'soup'         => 'Zupa',
            'starter'      => 'Przystawka',
            'main'         => 'Danie główne',
            'side_starchy' => 'Dodatek skrobiowy',
            'salad'        => 'Surówka / Sałata',
            'sauce'        => 'Sos',
        ];
    }

    public static function servingTypeOptions(): array
    {
        return [
            'plate'   => 'Na talerzu (1 szt./os.)',
            'platter' => 'Na półmisku (%)',
        ];
    }

    // ── Reguły procentów ────────────────────────────────────────────────────

    /** Minimalne % dla danego produktu. */
    public function minPercentage(): int
    {
        return match (true) {
            $this->category === 'main' && $this->serving_type === 'platter' => 60,
            $this->category === 'salad'  => 50,
            $this->category === 'sauce'  => 50,
            default                      => 100,
        };
    }

    /** Czy produkt ma konfigurowalny %. */
    public function hasPercentage(): bool
    {
        return ($this->category === 'main' && $this->serving_type === 'platter')
            || in_array($this->category, ['salad', 'sauce']);
    }

    /** Cena za osobę przy danym %. */
    public function priceAtPercentage(float $percentage): float
    {
        return round($this->price_per_person * $percentage / 100, 2);
    }

    // ── Relacje ─────────────────────────────────────────────────────────────

    public function proposalItems(): HasMany
    {
        return $this->hasMany(MenuProposalItem::class);
    }

    public function reservationMenuItems(): HasMany
    {
        return $this->hasMany(ReservationMenuItem::class);
    }
}
