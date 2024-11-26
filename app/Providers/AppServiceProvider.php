<?php

namespace App\Providers;

use App\Models\User;
use Gate;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::before(function (User $user, string $ability) {
            return $user->isAdministrator() ? true : null;
        });

        Gate::after(function (User $user, string $ability, ?bool $result, mixed $arguments) {
            return $user->isAdministrator() ? true : null;
        });

        AnonymousResourceCollection::macro('paginationInformation', function ($request, $paginated, $default) {
            unset($default['meta']['links']);

            return $default;
        });
    }
}
