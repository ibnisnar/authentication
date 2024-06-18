<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Ibnisnar\Authentication\Authentication;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Authentication::deleteUsersUsing(DeleteUser::class);
    }
}
