<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuppyResource\Pages;
use App\Models\Puppy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PuppyResource extends Resource
{
    protected static ?string $model = Puppy::class;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationLabel = 'Puppies';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Puppy details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('breed')
                        ->default('Cane Corso')
                        ->required(),

                    Forms\Components\DatePicker::make('date_of_birth')
                        ->required()
                        ->maxDate(now()),

                    Forms\Components\Select::make('sex')
                        ->options([
                            'male' => 'Male',
                            'female' => 'Female',
                        ])
                        ->required(),

                    Forms\Components\TextInput::make('price')
                        ->numeric()
                        ->prefix('$')
                        ->required(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'available' => 'Available',
                            'pending' => 'Pending',
                            'reserved' => 'Reserved',
                            'sold' => 'Sold',
                        ])
                        ->default('available')
                        ->required()
                        ->helperText('Flips to "Pending" automatically when an order comes in.'),

                    Forms\Components\Textarea::make('description')
                        ->columnSpanFull()
                        ->rows(4),
                ]),

            Forms\Components\Section::make('Photos')
                ->schema([
                    Forms\Components\FileUpload::make('uploaded_images')
                        ->label('Puppy photos')
                        ->multiple()
                        ->image()
                        ->imageEditor()
                        ->reorderable()
                        ->appendFiles()
                        ->directory('puppies')
                        ->helperText('Drag to reorder. First photo is used as the main listing image.'),
                ]),

            Forms\Components\Section::make('SEO (optional)')
                ->collapsed()
                ->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(60),
                    Forms\Components\Textarea::make('meta_description')->maxLength(160)->rows(2),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('images.0.path')
                    ->label('Photo')
                    ->square(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sex')
                    ->badge(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Age')
                    ->formatStateUsing(fn ($state) => $state->diffInWeeks(now()).' weeks')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'pending' => 'warning',
                        'reserved' => 'info',
                        'sold' => 'gray',
                    }),

                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Inquiries'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'pending' => 'Pending',
                        'reserved' => 'Reserved',
                        'sold' => 'Sold',
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
            'index' => Pages\ListPuppies::route('/'),
            'create' => Pages\CreatePuppy::route('/create'),
            'edit' => Pages\EditPuppy::route('/{record}/edit'),
        ];
    }
}
