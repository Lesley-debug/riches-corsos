<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reply')
                ->label('Reply via Email')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->url(function () {
                    $record  = $this->getRecord();
                    $subject = urlencode('Re: ' . $record->subject);
                    $body    = urlencode("\n\n---\nOriginal message from {$record->name}:\n{$record->message}");
                    return "mailto:{$record->email}?subject={$subject}&body={$body}";
                })
                ->openUrlInNewTab(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $this->getRecord()->update(['is_read' => true]);
        return $data;
    }
}
