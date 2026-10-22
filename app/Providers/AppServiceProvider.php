<?php

namespace App\Providers;

use App\Enums\Feature;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        foreach (Feature::cases() as $feature) {
            Gate::define($feature->value, fn (User $user): bool => $user->allows($feature));
        }
    }
}
