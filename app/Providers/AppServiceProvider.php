<?php

namespace App\Providers;

use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(TaskController::class)
        ->needs(GoogleCalendarController::class)
        ->give(function () {
            return new GoogleCalendarController();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
