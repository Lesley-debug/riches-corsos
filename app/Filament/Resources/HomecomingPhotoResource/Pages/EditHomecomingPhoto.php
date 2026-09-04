<?php

namespace App\Filament\Resources\HomecomingPhotoResource\Pages;

use App\Filament\Resources\HomecomingPhotoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHomecomingPhoto extends EditRecord
{
    protected static string $resource = HomecomingPhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
