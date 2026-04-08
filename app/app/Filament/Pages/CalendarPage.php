<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CalendarPage extends Page
{
    protected static string $view = 'filament.pages.calendar';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Kalendarz';
    protected static ?string $title = 'Kalendarz rezerwacji';
    protected static ?int $navigationSort = 0;
}
