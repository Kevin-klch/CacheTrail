@props(['title' => null, 'description' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ? $title.' · '.config('app.name') : config('app.name').' · '.__('Discover routes. Find adventures.') }}</title>
        <meta name="description" content="{{ $description ?? __('Discover ready-made geocaching routes that fit your day, or share your favorite tour with other geocachers.') }}">
        <meta name="theme-color" content="#213f30">
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="flex min-h-screen flex-col">
        <a href="#main" class="sr-only focus:not-sr-only focus:absolute focus:left-4 focus:top-4 focus:z-50 focus:rounded-full focus:bg-paper focus:px-4 focus:py-2 focus:text-forest-900">
            {{ __('Skip to content') }}
        </a>

        <x-layouts.navigation />

        <main id="main" class="flex-1">
            {{ $slot }}
        </main>

        <footer class="mt-24 text-forest-800">
            <x-treeline class="-mb-px" />
            <div class="bg-forest-800 text-forest-100">
                <div class="mx-auto grid grid-cols-1 max-w-7xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3 lg:px-8">
                    <div>
                        <x-logo inverted />
                        <p class="mt-4 max-w-xs font-heading text-lg text-forest-100/90">{{ __('Discover routes. Find adventures.') }}</p>
                    </div>
                    <nav aria-label="{{ __('Footer navigation') }}" class="flex flex-col gap-2 text-sm">
                        <a href="{{ route('home') }}" class="hover:text-white">{{ __('Home') }}</a>
                        <a href="{{ route('trails.index') }}" class="hover:text-white">{{ __('Discover routes') }}</a>
                        <a href="{{ route('trails.create') }}" class="hover:text-white">{{ __('Create route') }}</a>
                    </nav>
                    <p class="text-sm leading-relaxed text-forest-100/80 md:text-right">
                        {{ __('CacheTrail is a community project by geocachers for geocachers.') }}
                    </p>
                </div>
                <div class="border-t border-paper/10">
                    <p class="mx-auto max-w-7xl px-4 py-5 text-xs text-forest-200/80 sm:px-6 lg:px-8">
                        &copy; {{ date('Y') }} {{ config('app.name') }}
                    </p>
                </div>
            </div>
        </footer>

        @livewireScriptConfig
    </body>
</html>
