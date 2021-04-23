<?php

namespace App\Providers;

use App\Setting;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
       view()->share('appUrl', env('APP_URL'));
       view()->share('settings', Setting::with('country', 'state', 'city', 'zone')->first());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
