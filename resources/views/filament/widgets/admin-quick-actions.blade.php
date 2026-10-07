<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Quick Actions</x-slot>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('filament.admin.resources.puppies.create') }}"
               class="fi-btn fi-btn-size-md fi-btn-color-primary fi-color-primary fi-ac-btn-action inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-primary-600 text-white hover:bg-primary-500">
                <x-heroicon-o-plus class="h-4 w-4" />
                Add Puppy
            </a>

            <a href="{{ route('filament.admin.resources.orders.index') }}"
               class="fi-btn fi-btn-size-md inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                <x-heroicon-o-shopping-bag class="h-4 w-4" />
                View Orders
            </a>

            <a href="{{ route('filament.admin.resources.contact-messages.index') }}"
               class="fi-btn fi-btn-size-md inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                <x-heroicon-o-envelope class="h-4 w-4" />
                View Messages
            </a>

            <a href="{{ route('filament.admin.resources.puppies.index') }}"
               class="fi-btn fi-btn-size-md inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200">
                <x-heroicon-o-heart class="h-4 w-4" />
                Manage Puppies
            </a>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
