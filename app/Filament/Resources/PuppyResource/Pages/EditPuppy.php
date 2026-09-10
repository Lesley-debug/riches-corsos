<?php

namespace App\Filament\Resources\PuppyResource\Pages;

use App\Filament\Resources\PuppyResource;
use App\Models\PuppyDocument;
use App\Models\PuppyImage;
use App\Services\PuppyDocumentService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPuppy extends EditRecord
{
    protected static string $resource = PuppyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // ── Generate Document ─────────────────────────────────────────
            Actions\Action::make('generateDocument')
                ->label('Generate Document')
                ->icon('heroicon-o-document-plus')
                ->color('primary')
                ->form([
                    Forms\Components\Select::make('document_type')
                        ->label('Document Type')
                        ->options(PuppyDocument::$generatableTypes)
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('visibility')
                        ->label('Visibility')
                        ->options(['public' => 'Public (visible on website)', 'admin_only' => 'Admin Only (private)'])
                        ->default('admin_only')
                        ->required()
                        ->helperText('Public documents appear on the puppy\'s public page for download.'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Internal Notes (optional)')
                        ->rows(2)
                        ->placeholder('Any notes about this document…'),
                ])
                ->action(function (array $data): void {
                    $service = app(PuppyDocumentService::class);
                    $doc     = $service->generate($this->record, $data['document_type'], $data);
                    if (isset($data['visibility'])) {
                        $doc->update(['visibility' => $data['visibility']]);
                    }

                    Notification::make()
                        ->title('Document generated: '.$doc->title)
                        ->success()
                        ->send();
                }),

            // ── Upload External Document ──────────────────────────────────
            Actions\Action::make('uploadDocument')
                ->label('Upload Document')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    Forms\Components\Select::make('document_id')
                        ->label('Select Generated Document')
                        ->options(
                            fn () => PuppyDocument::where('puppy_id', $this->record->id)
                                ->get()
                                ->mapWithKeys(fn ($d) => [$d->id => $d->document_number.' — '.$d->title])
                        )
                        ->required()
                        ->searchable()
                        ->helperText('Choose from documents already generated for this puppy.'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notes (optional)')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $doc = PuppyDocument::find($data['document_id']);
                    if ($data['notes'] ?? null) {
                        $doc->update(['notes' => $data['notes']]);
                    }
                    Notification::make()
                        ->title($doc->title.' is ready')
                        ->body('Use Preview or Download in the Documents tab.')
                        ->success()
                        ->send();
                }),

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

        $sire = $this->record->parents()->wherePivot('role', 'sire')->first();
        $dam  = $this->record->parents()->wherePivot('role', 'dam')->first();

        $this->data['sire_id'] = $sire?->id;
        $this->data['dam_id']  = $dam?->id;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['uploaded_images'], $data['sire_id'], $data['dam_id'], $data['documents_table']);
        return $data;
    }

    protected function afterSave(): void
    {
        $this->syncImages();
        $this->syncParents();
    }

    private function syncImages(): void
    {
        $this->record->images()->delete();

        foreach (array_values($this->data['uploaded_images'] ?? []) as $order => $path) {
            PuppyImage::create([
                'puppy_id'    => $this->record->id,
                'path'        => $path,
                'sort_order'  => $order,
                'is_featured' => $order === 0,
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
        $this->record->parents()->sync($sync);
    }
}
