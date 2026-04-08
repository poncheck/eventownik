<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Models\Room;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Sale';
    protected static ?string $modelLabel = 'Sala';
    protected static ?string $pluralModelLabel = 'Sale';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                TextInput::make('name')
                    ->label('Nazwa sali')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Opis')
                    ->columnSpanFull(),
                TextInput::make('capacity_min')
                    ->label('Min. liczba gości')
                    ->numeric()
                    ->default(1)
                    ->required(),
                TextInput::make('capacity_max')
                    ->label('Max. liczba gości')
                    ->numeric()
                    ->required(),
                TextInput::make('price_per_hour')
                    ->label('Cena za godzinę (zł)')
                    ->numeric()
                    ->default(0)
                    ->prefix('PLN')
                    ->required(),
                Toggle::make('active')
                    ->label('Aktywna')
                    ->default(true),
            ]),

            Section::make('Blokady terminów')->schema([
                Repeater::make('blockouts')
                    ->relationship()
                    ->label(false)
                    ->columns(3)
                    ->schema([
                        DatePicker::make('date_from')
                            ->label('Od')
                            ->required()
                            ->native(false),
                        DatePicker::make('date_to')
                            ->label('Do')
                            ->required()
                            ->native(false),
                        TextInput::make('reason')->label('Powód'),
                    ])
                    ->addActionLabel('Dodaj blokadę'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nazwa')->searchable(),
                Tables\Columns\TextColumn::make('capacity_min')->label('Min gości'),
                Tables\Columns\TextColumn::make('capacity_max')->label('Max gości'),
                Tables\Columns\TextColumn::make('price_per_hour')->label('Cena/h')->money('PLN'),
                Tables\Columns\IconColumn::make('active')->label('Aktywna')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit'   => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
