<?php

namespace App\Providers;

use App\Http\Services\ApplicationService;
use App\Http\Services\ApplicationServiceInterface;
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
        $this->app->bind(ApplicationServiceInterface::class, ApplicationService::class);
    }
}
