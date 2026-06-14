<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        //
    ];

    public function boot(): void
    {
        Gate::define('access-dashboard', fn (User $user): bool => $user->active && $user->canAccessPanel());

        Gate::define('operate', fn (User $user): bool => $user->active && $user->canOperate());

        Gate::define('manage-inventory', fn (User $user): bool => $user->active && $user->canManageInventory());

        Gate::define('view-reports', fn (User $user): bool => $user->active && $user->isAdmin());

        Gate::define('manage-finance', fn (User $user): bool => $user->active && $user->isAdmin());

        Gate::define('manage-public-page', fn (User $user): bool => $user->active && $user->isAdmin());

        Gate::define('manage-users', fn (User $user): bool => $user->active && $user->isAdmin());
    }
}
