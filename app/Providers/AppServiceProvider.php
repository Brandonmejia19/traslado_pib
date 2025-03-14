<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Observers\TrasladosPropios;
use App\Models\TrasladoSecundarioPropios;
use App\Observers\TrasladosPropiosObserver;

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
        TrasladoSecundarioPropios::observe(TrasladosPropiosObserver::class);
    }
}
