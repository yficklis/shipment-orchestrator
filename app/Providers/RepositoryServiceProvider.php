<?php

namespace App\Providers;

use App\Repositories\Contracts\ShipmentRepositoryInterface;
use App\Repositories\EloquentShipmentRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            ShipmentRepositoryInterface::class,
            EloquentShipmentRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
