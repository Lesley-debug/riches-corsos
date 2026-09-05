@php
    $puppy     = $field->getLivewire()->record ?? null;
    $documents = ($puppy instanceof \App\Models\Puppy) ? $puppy->documents()->latest()->get() : collect();
@endphp

@if(!$puppy || !$puppy->exists)
    <p class="text-sm text-gray-500">Save the puppy record first to manage documents.</p>
@elseif($documents->isEmpty())
    <p class="text-sm text-gray-500">No documents yet. Use <strong>Generate Document</strong> or <strong>Upload Document</strong> in the page header.</p>
@else
<div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-500 dark:text-gray-400">
            <tr>
                <th class="px-4 py-3">Document</th>
                <th class="px-4 py-3">Ref</th>
                <th class="px-4 py-3">Source</th>
                <th class="px-4 py-3">Status</th>
                <th class="px-4 py-3">Date</th>
                <th class="px-4 py-3">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach($documents as $doc)
            <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                    {{ $doc->type_label }}
                    @if($doc->notes)
                        <div class="text-xs text-gray-400 mt-0.5">{{ Str::limit($doc->notes, 60) }}</div>
                    @endif
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs font-mono">{{ $doc->document_number ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($doc->isUploaded())
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Uploaded</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Generated</span>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                        @if($doc->status === 'generated') bg-emerald-100 text-emerald-800
                        @elseif($doc->status === 'uploaded') bg-blue-100 text-blue-800
                        @elseif($doc->status === 'template') bg-yellow-100 text-yellow-800
                        @elseif($doc->status === 'archived') bg-gray-100 text-gray-600
                        @else bg-gray-100 text-gray-600 @endif">
                        {{ $doc->status_label }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 text-xs">
                    {{ ($doc->generated_at ?? $doc->uploaded_at ?? $doc->created_at)?->format('d M Y') ?? '—' }}
                </td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2 flex-wrap">
                        <a href="{{ route('admin.puppies.documents.preview', [$puppy->id, $doc->id]) }}"
                           target="_blank"
                           class="text-xs font-medium text-primary-600 hover:text-primary-800 hover:underline">
                            Preview
                        </a>
                        <a href="{{ route('admin.puppies.documents.download', [$puppy->id, $doc->id]) }}"
                           class="text-xs font-medium text-gray-600 hover:text-gray-900 hover:underline">
                            Download
                        </a>
                        @if($doc->isGenerated())
                        <form method="POST"
                              action="{{ route('admin.puppies.documents.regenerate', [$puppy->id, $doc->id]) }}"
                              onsubmit="return confirm('Regenerate this document?')"
                              class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-medium text-amber-600 hover:text-amber-800 hover:underline">
                                Regenerate
                            </button>
                        </form>
                        @endif
                        <form method="POST"
                              action="{{ route('admin.puppies.documents.destroy', [$puppy->id, $doc->id]) }}"
                              onsubmit="return confirm('Delete this document permanently?')"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800 hover:underline">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif
