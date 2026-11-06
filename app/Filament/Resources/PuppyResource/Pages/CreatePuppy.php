<?php

namespace App\Filament\Resources\PuppyResource\Pages;

use App\Filament\Resources\PuppyResource;
use App\Models\PuppyImage;
use App\Models\PuppyVideo;
use Filament\Resources\Pages\CreateRecord;

class CreatePuppy extends CreateRecord
{
    protected static string $resource = PuppyResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['uploaded_images'], $data['uploaded_videos'], $data['url_videos'], $data['sire_id'], $data['dam_id']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->syncImages();
        $this->syncVideos();
        $this->syncParents();
    }

    private function syncImages(): void
    {
        foreach (array_values($this->data['uploaded_images'] ?? []) as $order => $path) {
            PuppyImage::create([
                'puppy_id'    => $this->record->id,
                'path'        => $path,
                'sort_order'  => $order,
                'is_featured' => $order === 0,
            ]);
        }
    }

    private function syncVideos(): void
    {
        $order = 0;

        foreach (array_values($this->data['uploaded_videos'] ?? []) as $path) {
            PuppyVideo::create([
                'puppy_id'   => $this->record->id,
                'file_path'  => $path,
                'source'     => 'upload',
                'sort_order' => $order++,
            ]);
        }

        foreach (array_values($this->data['url_videos'] ?? []) as $video) {
            if (empty($video['video_url'])) continue;
            PuppyVideo::create([
                'puppy_id'   => $this->record->id,
                'title'      => $video['title'] ?? null,
                'video_url'  => $video['video_url'],
                'source'     => 'url',
                'sort_order' => $order++,
            ]);
        }
    }

    private function syncParents(): void
    {
        $sync = [];
        if ($sireId = $this->data['sire_id'] ?? null) {
            $sync[$sireId] = ['role' => 'sire'];
        }
        if ($damId = $this->data['dam_id'] ?? null) {
            $sync[$damId] = ['role' => 'dam'];
        }
        if ($sync) {
            $this->record->parents()->sync($sync);
        }
    }
}
