<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuProductResource\Pages;
use App\Models\MenuProduct;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MenuProductResource extends Resource
{
    protected static ?string $model = MenuProduct::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationLabel = 'Katalog dań';
    protected static ?string $modelLabel = 'Danie';
    protected static ?string $pluralModelLabel = 'Katalog dań';
    protected static string|\BackedEnum|null $navigationGroup = 'Menu';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('category')
                    ->label('Kategoria')
                    ->options(MenuProduct::categoryOptions())
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($state, \Filament\Schemas\Components\Utilities\Set $set) =>
                        $set('serving_type', $state !== 'main' ? null : 'plate')
                    ),

                Select::make('serving_type')
                    ->label('Sposób podania')
                    ->options(MenuProduct::servingTypeOptions())
                    ->visible(fn (Get $get) => $get('category') === 'main')
                    ->required(fn (Get $get) => $get('category') === 'main')
                    ->helperText('Na półmisku — konfigurowalny %, min. 60%. Na talerzu — zawsze 1 szt./os.'),

                TextInput::make('name')
                    ->label('Nazwa')
                    ->required()
                    ->columnSpanFull(),

                Textarea::make('description')
                    ->label('Opis / składniki')
                    ->rows(2)
                    ->columnSpanFull(),

                TextInput::make('price_per_person')
                    ->label('Cena za osobę przy 100% (zł)')
                    ->numeric()
                    ->required()
                    ->prefix('PLN')
                    ->minValue(0)
                    ->helperText('Dla półmisków i produktów % — cena przy pełnej porcji (100%).'),

                TextInput::make('sort_order')
                    ->label('Kolejność')
                    ->numeric()
                    ->default(0),

                Toggle::make('active')
                    ->label('Aktywne')
                    ->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('category')
            ->groups([
                Tables\Grouping\Group::make('category')
                    ->label('Kategoria')
                    ->getTitleFromRecordUsing(fn (MenuProduct $r) => MenuProduct::categoryLabel($r->category))
                    ->collapsible(),
            ])
            ->groupingSettingsHidden()
            ->defaultGroup('category')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategoria')
                    ->formatStateUsing(fn ($state) => MenuProduct::categoryLabel($state))
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('serving_type')
                    ->label('Podanie')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'plate'   => 'Na talerzu',
                        'platter' => 'Na półmisku',
                        default   => '—',
                    })
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Cena/os. (100%)')
                    ->money('PLN'),
                Tables\Columns\IconColumn::make('active')
                    ->label('Aktywne')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategoria')
                    ->options(MenuProduct::categoryOptions()),
                Tables\Filters\TernaryFilter::make('active')->label('Aktywne'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenuProducts::route('/'),
            'create' => Pages\CreateMenuProduct::route('/create'),
            'edit'   => Pages\EditMenuProduct::route('/{record}/edit'),
        ];
    }
}
