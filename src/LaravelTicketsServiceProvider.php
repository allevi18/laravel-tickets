<?php

namespace RexlManu\LaravelTickets;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use RexlManu\LaravelTickets\Commands\AutoCloseCommand;
use RexlManu\LaravelTickets\Models\Ticket;
use RexlManu\LaravelTickets\Models\TicketMessage;
use RexlManu\LaravelTickets\Models\TicketUpload;
use RexlManu\LaravelTickets\Observers\TicketMessageObserver;
use RexlManu\LaravelTickets\Observers\TicketObserver;
use RexlManu\LaravelTickets\Observers\TicketUploadObserver;

class LaravelTicketsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'laravel-tickets');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laravel-tickets');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->routes();
        $this->observers();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('laravel-tickets.php'),
            ], 'config');

            // Publishing the views.
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/laravel-tickets'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'migrations');

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-tickets'),
            ], 'assets');*/

            // Publishing the translation files.
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/laravel-tickets'),
            ], 'lang');

            if (config('laravel-tickets.autoclose-days') > 0) {
                // Registering package commands.
                $this->commands([ AutoCloseCommand::class ]);
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'laravel-tickets');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-tickets', function () {
            return new LaravelTickets;
        });
    }

    public function routes()
    {
        // Macro routing
        foreach ([ 'support', 'tickets' ] as $routeMacroName) {
            Router::macro($routeMacroName, function ($controller) use($routeMacroName) {
                Route::middleware(config('laravel-tickets.guard'))->name('laravel-tickets.')->group(function () use ($controller, $routeMacroName) {
                    Route::prefix($routeMacroName)->group(function () use ($controller, $routeMacroName) {
                        Route::get('/', [ $controller, 'index' ])->name($routeMacroName.'.index');
                        Route::post('/', [ $controller, 'store' ])->name($routeMacroName.'.store');
                        Route::get('/create', [ $controller, 'create' ])->name($routeMacroName.'.create');
                        Route::prefix('{ticket}')->group(function () use ($controller, $routeMacroName) {
                            Route::get('/', [ $controller, 'show' ])->name($routeMacroName.'.show');
                            Route::post('/', [ $controller, 'close' ])->name($routeMacroName.'.close');
                            Route::post('/message', [ $controller, 'message' ])->name($routeMacroName.'.message');
                            Route::prefix('{ticketUpload}')->group(function () use ($controller, $routeMacroName) {
                                Route::get('/download', [ $controller, 'download' ])->name($routeMacroName.'.download');
                            });
                        });
                    });
                });
            });
        }
    }

    private function observers()
    {
        Ticket::observe(TicketObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);
        TicketUpload::observe(TicketUploadObserver::class);
    }
}
