<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Menu';
    protected static ?string $modelLabel = 'Menu';
    protected static ?string $pluralModelLabel = 'Menu';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->columns(2)->schema([
                Select::make('event_type_id')
                    ->label('Rodzaj imprezy')
                    ->relationship('eventType', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Nazwa menu')
                    ->required(),
                Textarea::make('description')
                    ->label('Opis')
                    ->columnSpanFull(),
                TextInput::make('price_per_person')
                    ->label('Cena per osoba (zł)')
                    ->numeric()
                    ->prefix('PLN')
                    ->default(0),
                TextInput::make('sort_order')
                    ->label('Kolejność')
                    ->numeric()
                    ->default(0),
                Toggle::make('active')
                    ->label('Aktywne')
                    ->default(true),
            ]),

            Section::make('Dania')->schema([
                Repeater::make('courses')
                    ->relationship()
                    ->label(false)
                    ->columns(3)
                    ->defaultItems(0)
                    ->schema([
                        Select::make('type')
                            ->label('Rodzaj')
                            ->options([
                                'starter' => 'Przystawka',
                                'soup'    => 'Zupa',
                                'main'    => 'Danie główne',
                                'dessert' => 'Deser',
                                'other'   => 'Inne',
                            ])
                            ->required(),
                        TextInput::make('name')->label('Nazwa dania')->required(),
                        TextInput::make('description')->label('Opis'),
                        Hidden::make('sort_order')->default(0),
                    ])
                    ->addActionLabel('Dodaj danie')
                    ->reorderable('sort_order'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('eventType.name')
                    ->label('Rodzaj imprezy')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nazwa menu')
                    ->searchable(),
                Tables\Columns\TextColumn::make('price_per_person')
                    ->label('Cena/os.')
                    ->money('PLN'),
                Tables\Columns\TextColumn::make('courses_count')
                    ->label('Dań')
                    ->counts('courses'),
                Tables\Columns\IconColumn::make('active')->label('Aktywne')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Kolejność')->sortable(),
            ])
            ->defaultSort('event_type_id')
            ->filters([
                Tables\Filters\SelectFilter::make('event_type_id')
                    ->label('Rodzaj imprezy')
                    ->relationship('eventType', 'name'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit'   => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}
