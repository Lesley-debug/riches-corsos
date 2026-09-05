<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomecomingPhotoResource\Pages;
use App\Models\HomecomingPhoto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomecomingPhotoResource extends Resource
{
    protected static ?string $model = HomecomingPhoto::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Homecoming Photos';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image')
                ->image()
                ->required()
                ->directory('homecomings'),
            Forms\Components\TextInput::make('caption')
                ->placeholder('e.g. "The Thompson family, 2025"'),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0)
                ->helperText('Lower numbers show first.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('caption'),
                Tables\Columns\TextColumn::make('sort_order'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('sort_order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomecomingPhotos::route('/'),
            'create' => Pages\CreateHomecomingPhoto::route('/create'),
            'edit' => Pages\EditHomecomingPhoto::route('/{record}/edit'),
        ];
    }
}
