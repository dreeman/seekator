<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public $bindings = [
        \App\Services\Contracts\CommandService::class => \App\Services\CommandService::class,
//        \App\Services\Contracts\DataRepository::class => \App\Services\DataRepository::class,
        \App\Services\Contracts\CreativeRepository::class => \App\Services\CreativeRepository::class,
        \App\Services\Contracts\VideoService::class => \App\Services\VideoService::class,
        \App\Services\Contracts\ProcessService::class => \App\Services\ProcessService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
