@props(['code', 'title', 'message'])

{{-- Standalone page for HTTP errors. It deliberately avoids the app layout (navigation, user and database
     lookups) so it still renders when the error itself comes from there. --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex">
        <title>{{ $title }} · {{ config('app.name') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        @fonts
        @vite(['resources/css/app.css'])
    </head>
    <body class="flex min-h-screen flex-col">
        <header class="bg-forest-800">
            <div class="mx-auto flex h-18 max-w-7xl items-center px-4 sm:px-6 lg:px-8">
                <a href="{{ url('/') }}"><x-logo inverted /></a>
            </div>
        </header>

        <main class="relative flex flex-1 items-center overflow-hidden px-4 py-20">
            <x-trail-line class="absolute -left-10 top-0 hidden h-full w-40 text-trail-400/40 md:block" />
            <div class="relative mx-auto max-w-lg text-center">
                <p class="font-heading text-7xl font-bold text-trail-500">{{ $code }}</p>
                <h1 class="mt-4 text-4xl font-bold">{{ $title }}</h1>
                <p class="mt-4 text-lg text-ink-muted">{{ $message }}</p>
                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <x-button :href="url('/')">{{ __('Back to home') }}</x-button>
                    {{ $slot }}
                </div>
            </div>
        </main>

        <x-treeline class="-mb-px text-forest-800" />
        <div class="h-10 bg-forest-800"></div>
    </body>
</html>
