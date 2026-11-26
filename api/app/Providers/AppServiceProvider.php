<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

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
        // Password policy shared by registration and password change.
        Password::defaults(fn () => Password::min(8)->max(72)->letters()->numbers());

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // Every API route: per user when authenticated, per IP otherwise.
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));

        // Brute force against one account, without locking out other people on the same IP.
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)
            ->by(mb_strtolower($request->string('email')->trim()).'|'.$request->ip()));

        // Mass creation of fake accounts.
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)
            ->by($request->ip()));
    }
}
