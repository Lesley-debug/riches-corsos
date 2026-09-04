<?php

namespace App\Filament\Resources\PuppyResource\Pages;

use App\Filament\Resources\PuppyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPuppies extends ListRecords
{
    protected static string $resource = PuppyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Add Puppy'),
        ];
    }
}
