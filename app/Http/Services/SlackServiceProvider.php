<?php

namespace App\Http\Services;

use Illuminate\Support\ServiceProvider;

class SlackServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(SlackServiceInterface::class, SlackService::class);
    }
}
