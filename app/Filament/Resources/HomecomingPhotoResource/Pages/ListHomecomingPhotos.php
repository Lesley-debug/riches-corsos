<?php

namespace App\Filament\Resources\HomecomingPhotoResource\Pages;

use App\Filament\Resources\HomecomingPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHomecomingPhotos extends ListRecords
{
    protected static string $resource = HomecomingPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Add Photo')];
    }
}
