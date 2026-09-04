<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterSave(): void
    {
        // Keep the puppy's own status in sync with what the admin decides on the order,
        // so the public site never shows a puppy as "available" once someone's buying it.
        $puppy = $this->record->puppy;

        $puppy->status = match ($this->record->status) {
            'confirmed' => 'sold',
            'deposit_received' => 'reserved',
            'contacted' => 'pending',
            default => $puppy->status,
        };

        $puppy->save();
    }
}
