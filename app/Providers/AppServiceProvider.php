<?php

namespace App\Providers;

use App\Http\Services\SlackService;
use App\Http\Services\SlackServiceInterface;
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
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(SlackServiceInterface::class, SlackService::class);
    }
}
