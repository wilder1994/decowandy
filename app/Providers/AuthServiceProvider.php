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
        Gate::define('view-reports', function (User $user): bool {
            return $user->isAdmin() && ($user->active ?? true);
        });

        Gate::define('manage-finance', function (User $user): bool {
            return $user->isAdmin() && ($user->active ?? true);
        });

        Gate::define('manage-public-page', function (User $user): bool {
            return $user->isAdmin() && ($user->active ?? true);
        });

        Gate::define('manage-users', function (User $user): bool {
            return $user->isAdmin() && ($user->active ?? true);
        });
    }
}
