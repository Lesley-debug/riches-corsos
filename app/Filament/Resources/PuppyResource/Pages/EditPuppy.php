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
use Illuminate\Support\Facades\Storage;

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

                    Forms\Components\Textarea::make('notes')
                        ->label('Internal Notes (optional)')
                        ->rows(2)
                        ->placeholder('Any notes about this document…'),
                ])
                ->action(function (array $data): void {
                    $service = app(PuppyDocumentService::class);
                    $doc     = $service->generate($this->record, $data['document_type'], $data);

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
                    Forms\Components\TextInput::make('title')
                        ->label('Document Title')
                        ->required()
                        ->placeholder('e.g. Vet Health Certificate'),

                    Forms\Components\Select::make('document_type')
                        ->label('Document Type')
                        ->options(array_merge(
                            PuppyDocument::$generatableTypes,
                            ['other' => 'Other']
                        ))
                        ->required(),

                    Forms\Components\FileUpload::make('file')
                        ->label('File')
                        ->required()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->disk('public')
                        ->directory('puppy-documents/uploaded')
                        ->storeFileNamesIn('original_filename'),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notes (optional)')
                        ->rows(2),
                ])
                ->action(function (array $data): void {
                    $filePath = $data['file'];
                    $mime     = Storage::disk('public')->mimeType($filePath) ?? 'application/pdf';
                    $service  = app(PuppyDocumentService::class);
                    $docNum   = $service->generateDocumentNumber('other');

                    PuppyDocument::create([
                        'puppy_id'        => $this->record->id,
                        'document_type'   => $data['document_type'],
                        'title'           => $data['title'],
                        'document_number' => $docNum,
                        'status'          => PuppyDocument::STATUS_UPLOADED,
                        'source'          => PuppyDocument::SOURCE_UPLOADED,
                        'file_path'       => $filePath,
                        'mime_type'       => $mime,
                        'visibility'      => 'admin_only',
                        'uploaded_at'     => now(),
                        'created_by'      => auth()->id(),
                        'notes'           => $data['notes'] ?? null,
                    ]);

                    Notification::make()
                        ->title('Document uploaded: '.$data['title'])
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
