{{-- "New badge" notices: from the session after a redirect, or from the "badges-earned" browser event
     that Livewire components dispatch. Each notice disappears after a few seconds. --}}
<div
    x-data="{
        items: [],
        nextId: 0,
        add(badges) {
            badges.forEach((badge) => {
                const id = this.nextId++;
                this.items.push({ id, ...badge });
                setTimeout(() => this.remove(id), 8000);
            });
        },
        remove(id) {
            this.items = this.items.filter((item) => item.id !== id);
        },
    }"
    x-init="add(@js(session(App\Services\Badges\BadgeAwarder::SESSION_KEY, [])))"
    x-on:badges-earned.window="add($event.detail.badges ?? [])"
    class="pointer-events-none fixed inset-x-4 bottom-4 z-50 flex flex-col items-end gap-3 sm:inset-x-auto sm:right-6 sm:bottom-6"
    aria-live="polite"
>
    <template x-for="item in items" :key="item.id">
        <div
            x-transition:enter="transition duration-300 ease-out"
            x-transition:enter-start="translate-y-4 opacity-0"
            x-transition:enter-end="translate-y-0 opacity-100"
            class="pointer-events-auto flex w-full max-w-sm items-center gap-4 rounded-2xl bg-forest-800 p-4 pr-3 text-paper shadow-2xl ring-1 ring-paper/10"
            role="status"
        >
            <span class="grid size-12 shrink-0 place-items-center rounded-full bg-[#c9971c] text-white ring-4 ring-[#f1d98f]">
                <x-icon name="award" class="size-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-semibold uppercase tracking-widest text-trail-400">{{ __('New badge') }}</p>
                <p class="font-heading text-lg font-bold leading-tight" x-text="item.name"></p>
                <p class="text-sm text-forest-100/90" x-text="item.description"></p>
            </div>
            <button type="button" x-on:click="remove(item.id)" class="grid size-8 shrink-0 place-items-center self-start rounded-full text-forest-100 hover:bg-paper/10" title="{{ __('Close') }}">
                <x-icon name="close" class="size-4" />
                <span class="sr-only">{{ __('Close') }}</span>
            </button>
        </div>
    </template>
</div>
