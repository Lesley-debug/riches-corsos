<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon  = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Blog';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),

            Forms\Components\TextInput::make('slug')->required(),

            Forms\Components\TextInput::make('category')
                ->placeholder('Health, Training, Nutrition...'),

            Forms\Components\FileUpload::make('cover_image')
                ->image()
                ->directory('blog'),

            Forms\Components\Textarea::make('excerpt')
                ->rows(2)
                ->maxLength(200),

            Forms\Components\RichEditor::make('body')
                ->required()
                ->columnSpanFull(),

            Forms\Components\DateTimePicker::make('published_at')
                ->default(now())
                ->helperText('Leave empty to save as a draft. Defaults to current time for immediate publication.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\IconColumn::make('published_at')
                    ->label('Published')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->published_at !== null && $record->published_at <= now()->addHours(12)),
                Tables\Columns\TextColumn::make('created_at')->dateTime('M j, Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
