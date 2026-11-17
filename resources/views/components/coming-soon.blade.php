@props(['title', 'eyebrow' => null, 'description' => null, 'icon' => 'route'])

<x-layouts.app :title="$title">
    <x-page-header :title="$title" :eyebrow="$eyebrow">{{ $description }}</x-page-header>

    <div class="mx-auto -mt-20 max-w-5xl px-4 sm:px-6 lg:px-8">
        <x-card>
            <x-empty-state :icon="$icon" :title="__('Coming soon')">
                {{ $slot }}
            </x-empty-state>
        </x-card>
    </div>
</x-layouts.app>
