<?php

namespace App\Livewire;

use App\Enums\FeedbackCategory;
use App\Models\Feedback;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * Floating "Feedback" button on every page with a small form. Guests can send feedback too.
 */
class FeedbackButton extends Component
{
    public const int MESSAGE_MAX_LENGTH = 3000;

    /** Messages one visitor may send within ten minutes. */
    public const int MAX_ATTEMPTS = 20;

    #[Locked]
    public ?string $pageUrl = null;

    public bool $open = false;

    public bool $sent = false;

    public string $category = '';

    public string $message = '';

    public string $contact = '';

    /** Honeypot: invisible for people, filled in by spam bots. */
    public string $website = '';

    public function mount(): void
    {
        $this->pageUrl = Str::limit(request()->fullUrl(), 490, '');
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;
        $this->sent = false;
        $this->resetValidation();
    }

    public function submit(): void
    {
        $validated = $this->validate([
            'category' => ['required', Rule::enum(FeedbackCategory::class)],
            'message' => ['required', 'string', 'min:5', 'max:'.self::MESSAGE_MAX_LENGTH],
            'contact' => ['nullable', 'string', 'email', 'max:255'],
        ], attributes: [
            'category' => __('Topic'),
            'message' => __('Message'),
            'contact' => __('Email address'),
        ]);

        // Testers sharing the feedback account each get their own allowance.
        $key = 'feedback:'.(auth()->check() && ! auth()->user()->isFeedbackAccount() ? auth()->id() : request()->ip());

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'message' => __('You have sent a lot of feedback in a short time. Please try again in a few minutes.'),
            ]);
        }

        RateLimiter::hit($key, 600);

        // Spam bots fill in the invisible field; they get the same answer, but nothing is stored.
        if ($this->website === '') {
            $feedback = new Feedback([
                'category' => $validated['category'],
                'message' => trim($validated['message']),
                'contact' => $this->asksForContact() && filled($validated['contact']) ? trim($validated['contact']) : null,
                'page_url' => $this->pageUrl,
            ]);
            $feedback->user()->associate(auth()->user());
            $feedback->save();
        }

        $this->reset(['category', 'message', 'contact', 'website']);
        $this->sent = true;
    }

    /**
     * Guests and the shared feedback account can leave an email address; for everybody else we know who wrote.
     */
    public function asksForContact(): bool
    {
        $user = auth()->user();

        return $user === null || $user->isFeedbackAccount();
    }

    public function render(): View
    {
        return view('livewire.feedback-button');
    }
}
