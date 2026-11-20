<?php

namespace App\Listeners;

use App\Models\LoginRecord;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

/**
 * Remembers each successful login for the administrators' login overview.
 */
class RecordLogin
{
    public function __construct(private Request $request) {}

    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $record = new LoginRecord([
            'ip_address' => $this->request->ip(),
            'user_agent' => mb_substr((string) $this->request->userAgent(), 0, 500),
        ]);
        $record->user()->associate($event->user);
        $record->save();
    }
}
