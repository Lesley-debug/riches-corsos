<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;

class ManageSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Site Settings';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.manage-site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->toArray());
    }

    public function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Homepage')
                ->schema([
                    Forms\Components\FileUpload::make('hero_image')
                        ->image()
                        ->directory('site')
                        ->helperText('The large background photo shown behind the headline on the homepage. Use a high-quality landscape photo of your dogs or kennel — not the logo.'),
                ]),

            Forms\Components\Section::make('Company Information')
                ->description('Used in PDF documents, emails, and the footer.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('company_name')
                        ->default('Riches Corsos')
                        ->required(),

                    Forms\Components\TextInput::make('tagline')
                        ->placeholder('e.g. Premium Cane Corso Puppies'),

                    Forms\Components\TextInput::make('phone')
                        ->placeholder('+1 (214) 212-3023'),

                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->placeholder('info@richescorsos.com'),

                    Forms\Components\TextInput::make('website')
                        ->url()
                        ->placeholder('https://richescorsos.com'),

                    Forms\Components\TextInput::make('address')
                        ->placeholder('Dallas, Texas'),

                    Forms\Components\TextInput::make('whatsapp')
                        ->placeholder('+12142123023'),
                ]),

            Forms\Components\Section::make('Social Media')
                ->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('facebook')
                        ->url()
                        ->placeholder('https://facebook.com/richescorsos'),

                    Forms\Components\TextInput::make('instagram')
                        ->url()
                        ->placeholder('https://instagram.com/richescorsos'),

                    Forms\Components\TextInput::make('tiktok')
                        ->url()
                        ->placeholder('https://tiktok.com/@richescorsos'),
                ]),

            Forms\Components\Section::make('Authorized Representative')
                ->description('Name and title that appear on company-issued PDF documents.')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('representative_name')
                        ->placeholder('e.g. Jane Smith'),

                    Forms\Components\TextInput::make('representative_title')
                        ->placeholder('e.g. Owner & Head Breeder'),

                    Forms\Components\FileUpload::make('signature_image')
                        ->image()
                        ->directory('site/signatures')
                        ->helperText('Upload a signature image (PNG with transparent background recommended). This will appear on company-issued PDF documents.')
                        ->columnSpanFull(),
                ]),

        ])->statePath('data');
    }

    public function save(): void
    {
        SiteSetting::current()->update($this->form->getState());
        Cache::forget(SiteSetting::CACHE_KEY);
        Notification::make()->title('Settings saved')->success()->send();
    }
}
