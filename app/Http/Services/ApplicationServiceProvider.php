<?php

namespace App\Http\Services;

use Illuminate\Support\ServiceProvider;

class ApplicationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ApplicationServiceInterface::class, ApplicationService::class);
    }
}
