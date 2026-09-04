<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Orders';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Buyer')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('puppy_id')
                        ->relationship('puppy', 'name')
                        ->disabled(),
                    Forms\Components\TextInput::make('buyer_name')->disabled(),
                    Forms\Components\TextInput::make('buyer_email')->disabled(),
                    Forms\Components\TextInput::make('buyer_phone')->disabled(),
                    Forms\Components\Textarea::make('buyer_address')->disabled()->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Manage this order')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'new' => 'New',
                            'contacted' => 'Contacted buyer',
                            'deposit_received' => 'Deposit received',
                            'confirmed' => 'Confirmed / sold',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->rows(3)
                        ->helperText('Private notes — not visible to the buyer.'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('puppy.name')
                    ->label('Puppy')
                    ->searchable(),

                Tables\Columns\TextColumn::make('buyer_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('buyer_phone'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'danger',
                        'contacted' => 'warning',
                        'deposit_received' => 'info',
                        'confirmed' => 'success',
                        'cancelled' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, g:ia')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted buyer',
                        'deposit_received' => 'Deposit received',
                        'confirmed' => 'Confirmed / sold',
                        'cancelled' => 'Cancelled',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
