<?php

namespace App\Filament\Resources\MenuProductResource\Pages;

use App\Filament\Resources\MenuProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMenuProduct extends EditRecord
{
    protected static string $resource = MenuProductResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
