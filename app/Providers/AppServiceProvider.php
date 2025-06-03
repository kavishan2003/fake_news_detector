<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
        RateLimiter::for('daily-ip-limit', function (Request $request) {
            return Limit::perDay(5)->by($request->ip());
        });
        if(app()->environment("production")) {
            URL::forceScheme("https");
        }  
    }
}
