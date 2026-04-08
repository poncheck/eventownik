<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CalendarPage extends Page
{
    protected string $view = 'filament.pages.calendar';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationLabel = 'Kalendarz';
    protected static ?string $title = 'Kalendarz rezerwacji';
    protected static ?int $navigationSort = 0;
}
