<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use App\Models\MenuProduct;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Propozycje menu';
    protected static ?string $modelLabel = 'Propozycja menu';
    protected static ?string $pluralModelLabel = 'Propozycje menu';
    protected static \UnitEnum|string|null $navigationGroup = 'Menu';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Podstawowe informacje')->columns(2)->schema([
                Select::make('event_type_id')
                    ->label('Rodzaj imprezy')
                    ->relationship('eventType', 'name')
                    ->required(),
                TextInput::make('name')
                    ->label('Nazwa propozycji')
                    ->required(),
                Textarea::make('description')
                    ->label('Opis')
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->label('Kolejność')
                    ->numeric()
                    ->default(0),
                Toggle::make('active')
                    ->label('Aktywna')
                    ->default(true),
            ]),

            Section::make('Skład menu')->schema([
                Repeater::make('proposalItems')
                    ->relationship()
                    ->label(false)
                    ->columns(4)
                    ->defaultItems(0)
                    ->schema([
                        Select::make('menu_product_id')
                            ->label('Danie')
                            ->options(
                                MenuProduct::where('active', true)
                                    ->orderBy('category')
                                    ->orderBy('sort_order')
                                    ->get()
                                    ->groupBy('category')
                                    ->mapWithKeys(fn ($items, $cat) => [
                                        MenuProduct::categoryLabel($cat) => $items->pluck('name', 'id'),
                                    ])
                            )
                            ->required()
                            ->live()
                            ->columnSpan(2)
                            ->afterStateUpdated(function ($state, \Filament\Schemas\Components\Utilities\Set $set) {
                                if (! $state) return;
                                $product = MenuProduct::find($state);
                                if ($product) {
                                    $set('percentage', $product->minPercentage());
                                }
                            }),

                        TextInput::make('percentage')
                            ->label('Ilość (%)')
                            ->numeric()
                            ->default(100)
                            ->minValue(1)
                            ->maxValue(200)
                            ->suffix('%')
                            ->live()
                            ->helperText(fn (Get $get) => self::percentageHelperText($get('menu_product_id'))),

                        Placeholder::make('price_preview')
                            ->label('Cena/os.')
                            ->content(fn (Get $get) => self::pricePreview(
                                $get('menu_product_id'),
                                $get('percentage')
                            )),
                    ])
                    ->reorderable('sort_order')
                    ->addActionLabel('Dodaj danie'),

                Placeholder::make('total_price_preview')
                    ->label('Łączna cena / os.')
                    ->content(fn (Get $get) => self::totalPricePreview($get('proposalItems') ?? [])),
            ]),
        ]);
    }

    private static function percentageHelperText(?int $productId): string
    {
        if (! $productId) return '';
        $product = MenuProduct::find($productId);
        if (! $product) return '';
        if (! $product->hasPercentage()) return 'Stała porcja (1 szt./os.)';
        return 'Min. ' . $product->minPercentage() . '% | 100% = pełna porcja';
    }

    private static function pricePreview(?int $productId, mixed $percentage): string
    {
        if (! $productId) return '—';
        $product = MenuProduct::find($productId);
        if (! $product) return '—';
        $pct = max((float)($percentage ?? 100), $product->minPercentage());
        return number_format($product->priceAtPercentage($pct), 2, ',', ' ') . ' PLN';
    }

    private static function totalPricePreview(array $items): string
    {
        $total = 0;
        foreach ($items as $item) {
            $productId  = $item['menu_product_id'] ?? null;
            $percentage = (float)($item['percentage'] ?? 100);
            if (! $productId) continue;
            $product = MenuProduct::find($productId);
            if ($product) {
                $total += $product->priceAtPercentage(max($percentage, $product->minPercentage()));
            }
        }
        return $total > 0
            ? number_format($total, 2, ',', ' ') . ' PLN / os.'
            : '—';
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
                    ->label('Nazwa propozycji')
                    ->searchable(),
                Tables\Columns\TextColumn::make('proposalItems_count')
                    ->label('Dań')
                    ->counts('proposalItems'),
                Tables\Columns\IconColumn::make('active')->label('Aktywna')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Kolejność')->sortable(),
            ])
            ->defaultSort('event_type_id')
            ->filters([
                Tables\Filters\SelectFilter::make('event_type_id')
                    ->label('Rodzaj imprezy')
                    ->relationship('eventType', 'name'),
            ])
            ->actions([EditAction::make(), DeleteAction::make()]);
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
