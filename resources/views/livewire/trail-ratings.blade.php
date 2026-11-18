<section class="mt-14" aria-labelledby="ratings-heading">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h2 id="ratings-heading" class="text-3xl font-bold">{{ __('Ratings') }}</h2>
            @if ($trail->rating_count > 0)
                <p class="mt-2 flex items-center gap-2 text-ink-muted">
                    <x-stars :value="$trail->average_rating" size="size-5" />
                    <span class="font-semibold text-forest-900">{{ $trail->averageRatingLabel() }}</span>
                    <span>{{ trans_choice('(:count rating)|(:count ratings)', $trail->rating_count) }}</span>
                </p>
            @endif
        </div>
    </div>

    @if (session('rating-status'))
        <x-alert class="mt-5">{{ session('rating-status') }}</x-alert>
    @endif

    {{-- Own rating form --}}
    @if ($canRate || $editing)
        <form wire:submit="save" class="mt-6 rounded-3xl border border-sand-200 bg-white p-5 sm:p-6">
            <h3 class="text-xl font-bold">{{ $editing ? __('Edit your rating') : __('Rate this route') }}</h3>

            <fieldset class="mt-3">
                <legend class="sr-only">{{ __('Stars') }}</legend>
                <div class="flex items-center gap-1" x-data="{ hover: 0 }" x-on:mouseleave="hover = 0">
                    @for ($star = 1; $star <= 5; $star++)
                        <label class="cursor-pointer rounded-md p-0.5 has-focus-visible:ring-2 has-focus-visible:ring-forest-500" x-on:mouseenter="hover = {{ $star }}">
                            <input type="radio" wire:model="rating" value="{{ $star }}" class="sr-only">
                            <x-icon name="star" class="size-8 transition"
                                x-bind:class="(hover || $wire.rating || 0) >= {{ $star }} ? 'fill-current text-trail-500' : 'text-sand-300'" />
                            <span class="sr-only">{{ trans_choice(':count star|:count stars', $star) }}</span>
                        </label>
                    @endfor
                </div>
                @error('rating')
                    <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </fieldset>

            <div class="mt-4">
                <label for="rating-comment" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Comment (optional)') }}</label>
                <textarea id="rating-comment" wire:model="comment" rows="3" maxlength="2000"
                    placeholder="{{ __('How did you like the route? Any tips for other geocachers?') }}"
                    class="block w-full rounded-xl border border-sand-300 bg-white px-4 py-3 text-ink shadow-xs placeholder:text-ink-muted/60 focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15"></textarea>
                @error('comment')
                    <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-4 flex flex-wrap gap-3">
                <x-button>{{ $editing ? __('Save changes') : __('Submit rating') }}</x-button>
                @if ($editing)
                    <x-button type="button" variant="ghost" wire:click="cancelEdit">{{ __('Cancel') }}</x-button>
                @endif
            </div>
        </form>
    @elseif ($trail->isOwnedBy(auth()->user()))
        <p class="mt-4 text-sm text-ink-muted">{{ __('You cannot rate your own route, but you can reply to ratings.') }}</p>
    @endif

    {{-- List --}}
    @if ($ratings->isEmpty())
        <x-empty-state class="mt-6" icon="star" :title="__('No ratings yet')">
            {{ __('Be the first to rate this route.') }}
        </x-empty-state>
    @else
        <ul class="mt-6 space-y-4">
            @foreach ($ratings as $item)
                <li wire:key="rating-{{ $item->id }}" class="rounded-3xl border border-sand-200 bg-white p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <a href="{{ $item->user->profileUrl() }}" class="font-semibold text-forest-800 hover:underline">{{ $item->user->name }}</a>
                            <x-role-badge :user="$item->user" size="icon" />
                            <x-stars :value="$item->rating" />
                            <span class="text-xs text-ink-muted">{{ App\Support\Format::date($item->created_at) }}</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            <livewire:report-button :reportable="$item" :key="'report-rating-'.$item->id" />
                            @if ($ownRating?->is($item) && ! $editing)
                                <button type="button" wire:click="edit" class="font-medium text-forest-700 hover:underline">{{ __('Edit') }}</button>
                            @endif
                            @can('delete', $item)
                                <button type="button" wire:click="delete({{ $item->id }})" wire:confirm="{{ __('Delete this rating?') }}" class="font-medium text-red-700 hover:underline">{{ __('Delete') }}</button>
                            @endcan
                        </div>
                    </div>

                    @if ($item->comment)
                        <p class="mt-3 whitespace-pre-line break-words leading-relaxed text-ink">{{ $item->comment }}</p>
                    @endif

                    {{-- Reply of the route creator --}}
                    @if ($replyingTo === $item->id)
                        <form wire:submit="saveReply" class="mt-4 border-l-2 border-trail-400 pl-4">
                            <label for="reply-{{ $item->id }}" class="mb-1.5 block text-sm font-medium text-forest-900">{{ __('Your reply as the creator') }}</label>
                            <textarea id="reply-{{ $item->id }}" wire:model="reply" rows="2" maxlength="2000"
                                class="block w-full rounded-xl border border-sand-300 bg-white px-4 py-2.5 text-ink shadow-xs focus:border-forest-500 focus:outline-none focus:ring-4 focus:ring-forest-500/15"></textarea>
                            @error('reply')
                                <p class="mt-1.5 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-ink-muted">{{ __('Leave empty and save to remove your reply.') }}</p>
                            <div class="mt-2 flex gap-2">
                                <x-button class="px-4 py-2">{{ __('Save reply') }}</x-button>
                                <x-button type="button" variant="ghost" class="px-4 py-2" wire:click="cancelReply">{{ __('Cancel') }}</x-button>
                            </div>
                        </form>
                    @elseif ($item->creator_reply)
                        <div class="mt-4 rounded-2xl bg-forest-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-forest-700">
                                {{ __('Reply from the creator') }}
                                @can('reply', $item)
                                    · <button type="button" wire:click="startReply({{ $item->id }})" class="normal-case tracking-normal hover:underline">{{ __('Edit') }}</button>
                                @endcan
                            </p>
                            <p class="mt-1 whitespace-pre-line break-words text-sm leading-relaxed text-ink">{{ $item->creator_reply }}</p>
                        </div>
                    @else
                        @can('reply', $item)
                            <button type="button" wire:click="startReply({{ $item->id }})" class="mt-3 text-sm font-semibold text-forest-700 hover:underline">{{ __('Reply') }}</button>
                        @endcan
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="mt-6">{{ $ratings->links() }}</div>
    @endif
</section>
