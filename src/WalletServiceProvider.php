<?php

namespace Iransh\Wallet;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\EloquentUserProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //Config::set('auth.providers.users.model', \Iransh\Wallet\Models\User::class);

        // Publish the migrations
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');



    }

    public function register()
    {
        // Bind any classes or services as needed
    }
}
