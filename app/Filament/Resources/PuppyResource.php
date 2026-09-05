<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PuppyResource\Pages;
use App\Models\ParentDog;
use App\Models\Puppy;
use App\Models\PuppyDocument;
use App\Models\PuppyImage;
use App\Models\PuppyVideo;
use App\Services\PuppyDocumentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PuppyResource extends Resource
{
    protected static ?string $model = Puppy::class;

    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static ?string $navigationLabel = 'Puppies';
    protected static ?string $navigationGroup = 'Kennel';
    protected static ?int    $navigationSort  = 1;

    // ─────────────────────────────────────────────────────────────────────────
    // FORM
    // ─────────────────────────────────────────────────────────────────────────
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Puppy')
                ->columnSpanFull()
                ->tabs([

                    // ── TAB 1: BASIC INFORMATION ──────────────────────────
                    Forms\Components\Tabs\Tab::make('Basic Information')
                        ->icon('heroicon-o-identification')
                        ->schema([
                            Forms\Components\Section::make('Core Details')
                                ->description('Name, breed, date of birth, sex, price and availability status.')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('name')
                                        ->required()
                                        ->maxLength(255)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state) {
                                            if (blank($get('slug'))) {
                                                $set('slug', Str::slug($state ?? ''));
                                            }
                                        }),

                                    Forms\Components\TextInput::make('breed')
                                        ->default('Cane Corso')
                                        ->required(),

                                    Forms\Components\TextInput::make('slug')
                                        ->required()
                                        ->unique(Puppy::class, 'slug', ignoreRecord: true)
                                        ->helperText('Auto-generated from name. Edit to customise — must be unique.')
                                        ->maxLength(255),

                                    Forms\Components\DatePicker::make('date_of_birth')
                                        ->required()
                                        ->maxDate(now()),

                                    Forms\Components\Select::make('sex')
                                        ->options(['male' => 'Male', 'female' => 'Female'])
                                        ->required(),

                                    Forms\Components\TextInput::make('price')
                                        ->numeric()
                                        ->prefix('$')
                                        ->required(),

                                    Forms\Components\Select::make('status')
                                        ->options([
                                            'available'     => 'Available',
                                            'reserved'      => 'Reserved',
                                            'pending'       => 'Pending',
                                            'sold'          => 'Sold',
                                            'not_available' => 'Not Available',
                                        ])
                                        ->default('available')
                                        ->required()
                                        ->helperText('Flips to "Pending" automatically when an order comes in.'),

                                    Forms\Components\Select::make('visibility')
                                        ->options([
                                            'published' => 'Published',
                                            'draft'     => 'Draft',
                                            'private'   => 'Private',
                                        ])
                                        ->default('published')
                                        ->required(),
                                ]),

                            Forms\Components\Section::make('Availability & Pricing')
                                ->description('Deposit, featured status, and when the puppy will be ready.')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\DatePicker::make('available_date')
                                        ->label('Available From')
                                        ->helperText('Leave blank if available now.'),

                                    Forms\Components\Toggle::make('featured')
                                        ->label('Featured Puppy')
                                        ->helperText('Highlighted on homepage and listings.'),

                                    Forms\Components\Toggle::make('deposit_required')
                                        ->label('Deposit Required')
                                        ->live(),

                                    Forms\Components\TextInput::make('deposit_amount')
                                        ->numeric()
                                        ->prefix('$')
                                        ->visible(fn (Forms\Get $get) => $get('deposit_required')),
                                ]),

                            Forms\Components\Section::make('Badges')
                                ->description('Optional labels shown on puppy cards.')
                                ->schema([
                                    Forms\Components\CheckboxList::make('badges')
                                        ->options([
                                            'available_now'       => 'Available Now',
                                            'new_arrival'         => 'New Arrival',
                                            'featured'            => 'Featured',
                                            'champion_bloodline'  => 'Champion Bloodline',
                                            'health_tested'       => 'Health Tested Parents',
                                            'family_raised'       => 'Family Raised',
                                            'ready_soon'          => 'Ready Soon',
                                        ])
                                        ->columns(3),
                                ]),
                        ]),

                    // ── TAB 2: PUPPY DETAILS & PERSONALITY ───────────────
                    Forms\Components\Tabs\Tab::make('Details & Personality')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            Forms\Components\Section::make('Physical Characteristics')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Select::make('color')
                                        ->options([
                                            'Black'         => 'Black',
                                            'Gray'          => 'Gray',
                                            'Fawn'          => 'Fawn',
                                            'Brindle'       => 'Brindle',
                                            'Black Brindle' => 'Black Brindle',
                                            'Formentino'    => 'Formentino',
                                            'Chestnut'      => 'Chestnut',
                                        ])
                                        ->searchable()
                                        ->createOptionForm([
                                            Forms\Components\TextInput::make('color')->required(),
                                        ]),

                                    Forms\Components\TextInput::make('markings')
                                        ->placeholder('e.g. White chest marking, black mask'),

                                    Forms\Components\TextInput::make('weight')
                                        ->placeholder('e.g. 12 lbs'),

                                    Forms\Components\TextInput::make('expected_adult_weight')
                                        ->placeholder('e.g. 90–110 lbs'),
                                ]),

                            Forms\Components\Section::make('Description')
                                ->schema([
                                    Forms\Components\Textarea::make('description')
                                        ->label('Puppy Description')
                                        ->rows(6)
                                        ->placeholder('Describe personality, temperament, energy level, training progress, family suitability…')
                                        ->columnSpanFull(),
                                ]),

                            Forms\Components\Section::make('Temperament & Personality')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\CheckboxList::make('temperament')
                                        ->options([
                                            'Affectionate' => 'Affectionate',
                                            'Confident'    => 'Confident',
                                            'Calm'         => 'Calm',
                                            'Playful'      => 'Playful',
                                            'Protective'   => 'Protective',
                                            'Intelligent'  => 'Intelligent',
                                            'Loyal'        => 'Loyal',
                                            'Gentle'       => 'Gentle',
                                            'Energetic'    => 'Energetic',
                                            'Curious'      => 'Curious',
                                            'Friendly'     => 'Friendly',
                                        ])
                                        ->columns(2),

                                    Forms\Components\Select::make('energy_level')
                                        ->options([
                                            'low'      => 'Low',
                                            'moderate' => 'Moderate',
                                            'high'     => 'High',
                                        ]),
                                ]),

                            Forms\Components\Section::make('Family Compatibility')
                                ->schema([
                                    Forms\Components\CheckboxList::make('compatibility')
                                        ->options([
                                            'Good with Children'         => 'Good with Children',
                                            'Good with Other Dogs'       => 'Good with Other Dogs',
                                            'Good with Cats'             => 'Good with Cats',
                                            'Family Friendly'            => 'Family Friendly',
                                            'First-Time Owner Friendly'  => 'First-Time Owner Friendly',
                                        ])
                                        ->columns(3),
                                ]),

                            Forms\Components\Section::make('Training Progress')
                                ->schema([
                                    Forms\Components\CheckboxList::make('training_progress')
                                        ->options([
                                            'Basic Handling'         => 'Basic Handling',
                                            'Crate Introduced'       => 'Crate Introduced',
                                            'Potty Training Started' => 'Potty Training Started',
                                            'Leash Introduced'       => 'Leash Introduced',
                                            'Basic Commands Started' => 'Basic Commands Started',
                                            'Socialization Started'  => 'Socialization Started',
                                        ])
                                        ->columns(3),
                                ]),
                        ]),

                    // ── TAB 3: PARENTS / PEDIGREE ─────────────────────────
                    Forms\Components\Tabs\Tab::make('Parents & Pedigree')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Forms\Components\Section::make('Father (Sire)')
                                ->description('Select an existing sire or create a new one.')
                                ->schema([
                                    Forms\Components\Select::make('sire_id')
                                        ->label('Father (Sire)')
                                        ->options(fn () => ParentDog::where('parent_type', 'sire')->pluck('name', 'id'))
                                        ->searchable()
                                        ->createOptionForm(static::parentForm('sire'))
                                        ->createOptionUsing(function (array $data) {
                                            $data['parent_type'] = 'sire';
                                            return ParentDog::create($data)->id;
                                        })
                                        ->editOptionForm(static::parentForm('sire'))
                                        ->dehydrated(false),
                                ]),

                            Forms\Components\Section::make('Mother (Dam)')
                                ->description('Select an existing dam or create a new one.')
                                ->schema([
                                    Forms\Components\Select::make('dam_id')
                                        ->label('Mother (Dam)')
                                        ->options(fn () => ParentDog::where('parent_type', 'dam')->pluck('name', 'id'))
                                        ->searchable()
                                        ->createOptionForm(static::parentForm('dam'))
                                        ->createOptionUsing(function (array $data) {
                                            $data['parent_type'] = 'dam';
                                            return ParentDog::create($data)->id;
                                        })
                                        ->editOptionForm(static::parentForm('dam'))
                                        ->dehydrated(false),
                                ]),
                        ]),

                    // ── TAB 4: PHOTOS & VIDEOS ────────────────────────────
                    Forms\Components\Tabs\Tab::make('Photos & Videos')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\Section::make('Photo Gallery')
                                ->description('Drag to reorder. The first photo is used as the main listing image.')
                                ->schema([
                                    Forms\Components\FileUpload::make('uploaded_images')
                                        ->label('Puppy Photos')
                                        ->multiple()
                                        ->image()
                                        ->imageEditor()
                                        ->reorderable()
                                        ->appendFiles()
                                        ->directory('puppies')
                                        ->helperText('Upload multiple photos. Drag to reorder — first photo becomes the featured image.'),
                                ]),

                            Forms\Components\Section::make('Videos')
                                ->description('Add YouTube or Vimeo URLs for this puppy.')
                                ->schema([
                                    Forms\Components\Repeater::make('videos')
                                        ->relationship()
                                        ->schema([
                                            Forms\Components\TextInput::make('title')
                                                ->placeholder('e.g. Rocky\'s First Outdoor Playtime'),
                                            Forms\Components\TextInput::make('video_url')
                                                ->label('YouTube / Vimeo URL')
                                                ->url()
                                                ->required()
                                                ->placeholder('https://youtube.com/watch?v=…'),
                                            Forms\Components\TextInput::make('sort_order')
                                                ->numeric()
                                                ->default(0)
                                                ->hidden(),
                                        ])
                                        ->columns(2)
                                        ->orderColumn('sort_order')
                                        ->reorderable()
                                        ->addActionLabel('Add Video')
                                        ->collapsible(),
                                ]),
                        ]),

                    // ── TAB 5: HEALTH & CARE ──────────────────────────────
                    Forms\Components\Tabs\Tab::make('Health & Care')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Forms\Components\Section::make('Vaccinations & Deworming')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Select::make('vaccination_status')
                                        ->options([
                                            'not_started'         => 'Not Started',
                                            'first_vaccination'   => 'First Vaccination',
                                            'second_vaccination'  => 'Second Vaccination',
                                            'fully_vaccinated'    => 'Fully Vaccinated',
                                        ]),

                                    Forms\Components\Toggle::make('dewormed')
                                        ->label('Dewormed'),

                                    Forms\Components\Textarea::make('vaccination_notes')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('e.g. First round completed. Next vaccination to be scheduled by new owner\'s vet.'),
                                ]),

                            Forms\Components\Section::make('Vet Check')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Toggle::make('vet_checked')
                                        ->label('Vet Checked'),

                                    Forms\Components\DatePicker::make('vet_check_date')
                                        ->label('Vet Check Date'),
                                ]),

                            Forms\Components\Section::make('Microchip')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Toggle::make('microchipped')
                                        ->label('Microchipped')
                                        ->live(),

                                    Forms\Components\TextInput::make('microchip_number')
                                        ->label('Microchip Number (admin only)')
                                        ->visible(fn (Forms\Get $get) => $get('microchipped')),
                                ]),

                            Forms\Components\Section::make('Health Guarantee')
                                ->columns(1)
                                ->schema([
                                    Forms\Components\Toggle::make('health_guarantee')
                                        ->label('Health Guarantee Included')
                                        ->live(),

                                    Forms\Components\Textarea::make('health_guarantee_notes')
                                        ->rows(3)
                                        ->visible(fn (Forms\Get $get) => $get('health_guarantee'))
                                        ->placeholder('Describe the health guarantee terms…'),
                                ]),
                        ]),

                    // ── TAB 6: DOCUMENTS ──────────────────────────────────
                    Forms\Components\Tabs\Tab::make('Documents')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Forms\Components\Section::make('Document Management')
                                ->description('Generate, upload, preview and download puppy documents. Save the puppy record first before managing documents.')
                                ->schema([
                                    Forms\Components\Placeholder::make('documents_info')
                                        ->label('')
                                        ->content('Use the Generate Document and Upload Document actions in the page header to manage documents for this puppy. All documents are listed below.'),

                                    Forms\Components\ViewField::make('documents_table')
                                        ->label('Documents')
                                        ->view('filament.puppy-documents-table')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ── TAB 7: SEO & VISIBILITY ───────────────────────────
                    Forms\Components\Tabs\Tab::make('SEO & Visibility')
                        ->icon('heroicon-o-magnifying-glass')
                        ->schema([
                            Forms\Components\Section::make('Search Engine Optimisation')
                                ->description('Optional. Leave blank to use the puppy name and description as defaults.')
                                ->schema([
                                    Forms\Components\TextInput::make('seo_title')
                                        ->label('SEO Title')
                                        ->maxLength(60)
                                        ->helperText('Max 60 characters.'),

                                    Forms\Components\Textarea::make('meta_description')
                                        ->label('Meta Description')
                                        ->maxLength(160)
                                        ->rows(3)
                                        ->helperText('Max 160 characters.'),
                                ]),
                        ]),
                ]),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PARENT FORM (reused for sire & dam create/edit modals)
    // ─────────────────────────────────────────────────────────────────────────
    protected static function parentForm(string $type): array
    {
        return [
            Forms\Components\Tabs::make('Parent')
                ->columnSpanFull()
                ->tabs([
                    Forms\Components\Tabs\Tab::make('Basic')
                        ->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\TextInput::make('breed')->default('Cane Corso'),
                            Forms\Components\DatePicker::make('date_of_birth'),
                            Forms\Components\Textarea::make('description')->rows(4),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Physical')
                        ->schema([
                            Forms\Components\TextInput::make('color')->placeholder('e.g. Black Brindle'),
                            Forms\Components\TextInput::make('weight')->placeholder('e.g. 120 lbs'),
                            Forms\Components\TextInput::make('height')->placeholder('e.g. 27 inches'),
                        ])->columns(3),

                    Forms\Components\Tabs\Tab::make('Health & Registration')
                        ->schema([
                            Forms\Components\KeyValue::make('health_tests')
                                ->label('Health Tests')
                                ->keyLabel('Test')
                                ->valueLabel('Result')
                                ->addActionLabel('Add Test')
                                ->helperText('e.g. Hip Test → Passed'),
                            Forms\Components\Textarea::make('health_notes')->rows(3),
                            Forms\Components\TextInput::make('registration_organization')
                                ->placeholder('e.g. AKC, ICCF, FCI'),
                            Forms\Components\TextInput::make('registration_number'),
                        ])->columns(2),

                    Forms\Components\Tabs\Tab::make('Titles')
                        ->schema([
                            Forms\Components\TagsInput::make('titles')
                                ->label('Titles / Achievements')
                                ->placeholder('Add a title and press Enter')
                                ->helperText('e.g. AKC Champion, Working Title, Best in Show'),
                        ]),

                    Forms\Components\Tabs\Tab::make('Photos')
                        ->schema([
                            Forms\Components\FileUpload::make('_images_upload')
                                ->label('Parent Photos')
                                ->multiple()
                                ->image()
                                ->imageEditor()
                                ->reorderable()
                                ->directory('parents')
                                ->helperText('First photo becomes the primary profile image.')
                                ->dehydrated(false),
                        ]),
                ]),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // LIFECYCLE HOOKS — sync parents pivot after save
    // ─────────────────────────────────────────────────────────────────────────
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPuppies::route('/'),
            'create' => Pages\CreatePuppy::route('/create'),
            'edit'   => Pages\EditPuppy::route('/{record}/edit'),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // TABLE
    // ─────────────────────────────────────────────────────────────────────────
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

                Tables\Columns\TextColumn::make('color')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('date_of_birth')
                    ->label('Age')
                    ->formatStateUsing(fn ($state) => $state ? $state->diffInWeeks(now()).' weeks' : '—')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->money('usd')
                    ->sortable(),

                Tables\Columns\IconColumn::make('featured')
                    ->boolean()
                    ->label('Featured'),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available'     => 'success',
                        'pending'       => 'warning',
                        'reserved'      => 'info',
                        'sold'          => 'gray',
                        'not_available' => 'danger',
                        default         => 'gray',
                    }),

                Tables\Columns\TextColumn::make('orders_count')
                    ->counts('orders')
                    ->label('Inquiries'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available'     => 'Available',
                        'pending'       => 'Pending',
                        'reserved'      => 'Reserved',
                        'sold'          => 'Sold',
                        'not_available' => 'Not Available',
                    ]),
                Tables\Filters\TernaryFilter::make('featured')
                    ->label('Featured'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
