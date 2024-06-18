<?php

namespace Ibnisnar\Authentication;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Laravel\Fortify\Events\PasswordUpdatedViaController;
use Laravel\Fortify\Fortify;
use Ibnisnar\Authentication\Http\Livewire\ApiTokenManager;
use Ibnisnar\Authentication\Http\Livewire\LogoutOtherBrowserSessionsForm;
use Ibnisnar\Authentication\Http\Livewire\NavigationMenu;
use Ibnisnar\Authentication\Http\Livewire\TwoFactorAuthenticationForm;
use Ibnisnar\Authentication\Http\Livewire\UpdatePasswordForm;
use Ibnisnar\Authentication\Http\Livewire\UpdateProfileInformationForm;
use Livewire\Livewire;

class AuthenticationServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/authentication.php', 'authentication');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::viewPrefix('auth.');

        $this->configurePublishing();
        $this->configureRoutes();
        $this->configureCommands();

        RedirectResponse::macro('banner', function ($message) {
            /** @var \Illuminate\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'success',
                'banner' => $message,
            ]);
        });

        RedirectResponse::macro('warningBanner', function ($message) {
            /** @var \Illuminate\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'warning',
                'banner' => $message,
            ]);
        });

        RedirectResponse::macro('dangerBanner', function ($message) {
            /** @var \Illuminate\Http\RedirectResponse $this */
            return $this->with('flash', [
                'bannerStyle' => 'danger',
                'banner' => $message,
            ]);
        });

        if (config('authentication.stack') === 'livewire' && class_exists(Livewire::class)) {
            Livewire::component('navigation-menu', NavigationMenu::class);
            Livewire::component('profile.update-profile-information-form', UpdateProfileInformationForm::class);
            Livewire::component('profile.update-password-form', UpdatePasswordForm::class);
            Livewire::component('profile.two-factor-authentication-form', TwoFactorAuthenticationForm::class);
            Livewire::component('profile.logout-other-browser-sessions-form', LogoutOtherBrowserSessionsForm::class);
            Livewire::component('profile.delete-user-form', DeleteUserForm::class);

            if (Features::hasApiFeatures()) {
                Livewire::component('api.api-token-manager', ApiTokenManager::class);
            }        }
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../stubs/config/authentication.php' => config_path('authentication.php'),
        ], 'authentication-config');

        $this->publishes([
            __DIR__.'/../database/migrations/0001_01_01_000000_create_users_table.php' => database_path('migrations/0001_01_01_000000_create_users_table.php'),
        ], 'authentication-migrations');

        $this->publishes([
            __DIR__.'/../routes/'.config('authentication.stack').'.php' => base_path('routes/authentication.php'),
        ], 'authentication-routes');
    }

    /**
     * Configure the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        if (authentication::$registersRoutes) {
            Route::group([
                'namespace' => 'Ibnisnar\Authentication\Http\Controllers',
                'domain' => config('authentication.domain', null),
                'prefix' => config('authentication.prefix', config('authentication.path')),
            ], function () {
                $this->loadRoutesFrom(__DIR__.'/../routes/'.config('authentication.stack').'.php');
            });
        }
    }

    /**
     * Configure the commands offered by the application.
     *
     * @return void
     */
    protected function configureCommands()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            Console\InstallCommand::class,
        ]);
    }
}
