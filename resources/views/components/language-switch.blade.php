@props(['tone' => 'light'])

{{-- Switches the language of the site (see LocaleController). "light" for white backgrounds such as the
     account menu, "dark" for the green header. --}}
<form method="POST" action="{{ route('locale.update') }}" {{ $attributes->class(['flex items-center gap-1']) }}>
    @csrf
    <x-icon name="globe" @class(['mr-1 size-4', 'text-forest-700' => $tone === 'light', 'text-forest-100' => $tone === 'dark']) />
    <span class="sr-only">{{ __('Language') }}</span>
    @foreach (config('cachetrail.locales') as $code => $language)
        <button type="submit" name="locale" value="{{ $code }}" lang="{{ $code }}" title="{{ $language }}" @class([
            'rounded-full px-2.5 py-1 text-xs font-semibold uppercase tracking-wide transition',
            'bg-forest-700 text-paper' => $tone === 'light' && app()->getLocale() === $code,
            'text-ink-muted hover:bg-sand-100 hover:text-ink' => $tone === 'light' && app()->getLocale() !== $code,
            'bg-paper/15 text-white' => $tone === 'dark' && app()->getLocale() === $code,
            'text-forest-100 hover:bg-paper/10 hover:text-white' => $tone === 'dark' && app()->getLocale() !== $code,
        ]) @if (app()->getLocale() === $code) aria-current="true" @endif>{{ $code }}<span class="sr-only"> ({{ $language }})</span></button>
    @endforeach
</form>
