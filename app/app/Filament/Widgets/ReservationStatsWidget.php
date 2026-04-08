<?php

namespace App\Filament\Widgets;

use App\Models\Reservation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ReservationStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Nowe zapytania', Reservation::where('status', 'new')->count())
                ->icon('heroicon-o-inbox')
                ->color('warning'),
            Stat::make('Oczekuje na płatność', Reservation::where('status', 'awaiting_payment')->count())
                ->icon('heroicon-o-credit-card')
                ->color('info'),
            Stat::make('Potwierdzone (ten miesiąc)', Reservation::where('status', 'confirmed')
                ->whereMonth('event_date', now()->month)
                ->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }
}
