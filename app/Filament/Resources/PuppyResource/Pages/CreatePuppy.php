<?php

namespace App\Filament\Resources\PuppyResource\Pages;

use App\Filament\Resources\PuppyResource;
use App\Models\PuppyImage;
use Filament\Resources\Pages\CreateRecord;

class CreatePuppy extends CreateRecord
{
    protected static string $resource = PuppyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['uploaded_images']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncImages();
    }

    private function syncImages(): void
    {
        $paths = $this->data['uploaded_images'] ?? [];

        foreach (array_values($paths) as $order => $path) {
            PuppyImage::create([
                'puppy_id' => $this->record->id,
                'path' => $path,
                'sort_order' => $order,
            ]);
        }
    }
}
