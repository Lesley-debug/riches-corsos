<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\PuppyDocument;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('documents:migrate-private {--dry-run}', function () {
    $dryRun = (bool) $this->option('dry-run');
    $moved = 0;
    $alreadyPrivate = 0;
    $missing = 0;

    PuppyDocument::query()
        ->whereNotNull('file_path')
        ->where('file_path', '!=', '')
        ->eachById(function (PuppyDocument $document) use (
            $dryRun,
            &$moved,
            &$alreadyPrivate,
            &$missing
        ) {
            $path = $document->file_path;

            if (Storage::disk('local')->exists($path)) {
                $alreadyPrivate++;
                return;
            }

            if (! Storage::disk('public')->exists($path)) {
                $missing++;
                $this->warn("Missing file for document {$document->id}: {$path}");
                return;
            }

            if ($dryRun) {
                $moved++;
                $this->line("Would move document {$document->id}: {$path}");
                return;
            }

            Storage::disk('local')->put($path, Storage::disk('public')->get($path));

            if (! Storage::disk('local')->exists($path)) {
                $missing++;
                $this->error("Private copy failed for document {$document->id}: {$path}");
                return;
            }

            Storage::disk('public')->delete($path);
            $moved++;
            $this->info("Moved document {$document->id}: {$path}");
        });

    $this->newLine();
    $this->info(
        "Moved/planned: {$moved}; already private: {$alreadyPrivate}; missing/failed: {$missing}"
    );

    return $missing > 0 ? 1 : 0;
})->purpose('Move puppy documents from public storage to private storage');
