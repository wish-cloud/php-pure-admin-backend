<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (Authorizable $user, string $ability, array &$args = []) {
            $guard = null;
            if (is_string($args[0] ?? null) && ! class_exists($args[0])) {
                $guard = array_shift($args);
            }
            if (method_exists($user, 'isSuperManager')) {
                if ($user->isSuperManager()) {
                    return true;
                }
            }
            if (method_exists($user, 'hasPermission')) {
                return $user->hasPermission($ability);
            }

            return true;
        });
    }
}
