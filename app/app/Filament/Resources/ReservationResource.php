<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReservationResource\Pages;
use App\Models\Menu;
use App\Models\Reservation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ReservationResource extends Resource
{
    protected static ?string $model = Reservation::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Rezerwacje';
    protected static ?string $modelLabel = 'Rezerwacja';
    protected static ?string $pluralModelLabel = 'Rezerwacje';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Dane klienta')
                ->columns(2)
                ->schema([
                    TextInput::make('first_name')->label('Imię')->required(),
                    TextInput::make('last_name')->label('Nazwisko')->required(),
                    TextInput::make('email')->label('E-mail')->email()->required(),
                    TextInput::make('phone')->label('Telefon')->tel(),
                ]),

            Section::make('Szczegóły imprezy')
                ->columns(2)
                ->schema([
                    Select::make('event_type_id')
                        ->label('Rodzaj imprezy')
                        ->relationship('eventType', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('menu_id', null)),
                    Select::make('menu_id')
                        ->label('Menu')
                        ->options(fn (Get $get) =>
                            Menu::where('event_type_id', $get('event_type_id'))
                                ->where('active', true)
                                ->pluck('name', 'id')
                        )
                        ->nullable(),
                    Select::make('room_id')
                        ->label('Sala')
                        ->relationship('room', 'name')
                        ->nullable(),
                    DatePicker::make('event_date')
                        ->label('Data imprezy')
                        ->required()
                        ->native(false)
                        ->minDate(now()),
                    TimePicker::make('event_time')
                        ->label('Godzina rozpoczęcia')
                        ->required()
                        ->seconds(false),
                    Select::make('duration_hours')
                        ->label('Czas trwania')
                        ->options([
                            2  => '2 godziny',
                            3  => '3 godziny',
                            4  => '4 godziny',
                            5  => '5 godzin',
                            6  => '6 godzin',
                            8  => '8 godzin',
                            10 => '10 godzin',
                            12 => 'Cały dzień (12h)',
                        ])
                        ->required(),
                    TextInput::make('guest_count')
                        ->label('Liczba gości')
                        ->numeric()
                        ->required()
                        ->minValue(1),
                    TextInput::make('total_price')
                        ->label('Całkowita cena (zł)')
                        ->numeric()
                        ->nullable()
                        ->prefix('PLN'),
                ]),

            Section::make('Status i notatki')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'new'              => 'Nowe',
                            'contacted'        => 'W kontakcie',
                            'awaiting_payment' => 'Oczekuje na płatność',
                            'confirmed'        => 'Potwierdzone',
                            'completed'        => 'Zrealizowane',
                            'cancelled'        => 'Anulowane',
                        ])
                        ->required(),
                    TextInput::make('reference')->label('Numer ref.')->disabled(),
                    Textarea::make('notes')
                        ->label('Uwagi klienta')
                        ->disabled()
                        ->columnSpanFull(),
                    Textarea::make('internal_notes')
                        ->label('Notatki wewnętrzne')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('reference')
                    ->label('Nr ref.')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Klient')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('eventType.name')
                    ->label('Rodzaj imprezy')
                    ->badge(),
                Tables\Columns\TextColumn::make('room.name')
                    ->label('Sala')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('event_date')
                    ->label('Data')
                    ->date('d.m.Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('guest_count')
                    ->label('Gości'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'              => 'gray',
                        'contacted'        => 'warning',
                        'awaiting_payment' => 'info',
                        'confirmed'        => 'success',
                        'completed'        => 'primary',
                        'cancelled'        => 'danger',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new'              => 'Nowe',
                        'contacted'        => 'W kontakcie',
                        'awaiting_payment' => 'Oczekuje na płatność',
                        'confirmed'        => 'Potwierdzone',
                        'completed'        => 'Zrealizowane',
                        'cancelled'        => 'Anulowane',
                        default            => $state,
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Zgłoszono')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'new'              => 'Nowe',
                        'contacted'        => 'W kontakcie',
                        'awaiting_payment' => 'Oczekuje na płatność',
                        'confirmed'        => 'Potwierdzone',
                        'completed'        => 'Zrealizowane',
                        'cancelled'        => 'Anulowane',
                    ]),
                Tables\Filters\SelectFilter::make('event_type_id')
                    ->label('Rodzaj imprezy')
                    ->relationship('eventType', 'name'),
                Tables\Filters\SelectFilter::make('room_id')
                    ->label('Sala')
                    ->relationship('room', 'name'),
            ])
            ->actions([
                Action::make('confirm')
                    ->label('Potwierdź')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (Reservation $record) => $record->status === 'awaiting_payment')
                    ->requiresConfirmation()
                    ->action(function (Reservation $record) {
                        $record->update(['status' => 'confirmed']);
                        \App\Mail\ReservationConfirmedMail::sendTo($record);
                        Notification::make()->title('Rezerwacja potwierdzona, mail wysłany.')->success()->send();
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListReservations::route('/'),
            'create' => Pages\CreateReservation::route('/create'),
            'edit'   => Pages\EditReservation::route('/{record}/edit'),
        ];
    }
}
