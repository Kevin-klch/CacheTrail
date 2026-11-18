<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('users:make-owner {email : Email address of the account}')]
#[Description('Give an account the owner role (only owners can do this in the admin area)')]
class MakeOwner extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->error(__('No account with this email address exists.'));

            return self::FAILURE;
        }

        $user->forceFill(['role' => UserRole::Owner])->save();
        $this->info(__(':name is now the owner.', ['name' => $user->name]));

        return self::SUCCESS;
    }
}
