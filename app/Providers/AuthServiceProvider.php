<?php

namespace App\Providers;

use App\Models\Task;
use App\Models\TaskHistory;
use App\Models\TaskShare;
use App\Policies\TaskPolicy;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Task::class => TaskPolicy::class,
        TaskHistory::class => TaskPolicy::class,
        TaskShare::class => TaskPolicy::class,
    ];
    
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
