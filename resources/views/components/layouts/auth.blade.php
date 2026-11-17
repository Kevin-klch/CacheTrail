@props(['title', 'subtitle' => null])

<x-layouts.app :title="$title">
    <div class="relative overflow-hidden px-4 py-12 sm:py-20">
        <x-trail-line class="absolute -left-10 top-0 hidden h-full w-40 text-trail-400/40 md:block" />
        <x-trail-line class="absolute -right-10 top-0 hidden h-full w-40 -scale-x-100 text-forest-300/40 md:block" />

        <div class="relative mx-auto w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="mt-2 text-ink-muted">{{ $subtitle }}</p>
                @endif
            </div>

            <x-card>
                {{ $slot }}
            </x-card>

            @isset($footer)
                <p class="mt-6 text-center text-sm text-ink-muted">{{ $footer }}</p>
            @endisset
        </div>
    </div>
</x-layouts.app>
