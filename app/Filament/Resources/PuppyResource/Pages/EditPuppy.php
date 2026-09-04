<?php

namespace App\Filament\Resources\PuppyResource\Pages;

use App\Filament\Resources\PuppyResource;
use App\Models\PuppyImage;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPuppy extends EditRecord
{
    protected static string $resource = PuppyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterFill(): void
    {
        $this->data['uploaded_images'] = $this->record
            ->images()
            ->orderBy('sort_order')
            ->pluck('path')
            ->all();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['uploaded_images']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncImages();
    }

    private function syncImages(): void
    {
        $paths = $this->data['uploaded_images'] ?? [];

        $this->record->images()->delete();

        foreach (array_values($paths) as $order => $path) {
            PuppyImage::create([
                'puppy_id' => $this->record->id,
                'path' => $path,
                'sort_order' => $order,
            ]);
        }
    }
}
