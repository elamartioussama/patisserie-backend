<?php

namespace App\Providers;
use Illuminate\Support\Facades\Schema;
use App\Providers\RouteServiceProvider;


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
    public function boot()
{
    $this->app->register(RouteServiceProvider::class);
    Schema::defaultStringLength(191);
}

    /**
     * Bootstrap any application services.
     */
    
}
