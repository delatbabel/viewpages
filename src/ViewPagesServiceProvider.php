<?php
/**
 * ViewPages Service Provider
 */

namespace Delatbabel\ViewPages;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Support\ServiceProvider;

/**
 * ViewPages Service Provider
 *
 * Service providers are the central place of all Laravel application bootstrapping.
 * Your own application, as well as all of Laravel's core services are bootstrapped
 * via service providers.
 *
 * ### Functionality
 *
 * * Adds database tables.
 *
 * @see  Illuminate\Support\ServiceProvider
 * @link http://laravel.com/docs/5.1/providers
 */
class ViewPagesServiceProvider extends ServiceProvider
{

    /**
     * Boot the service provider.
     *
     * This method is called after all other service providers have
     * been registered, meaning you have access to all other services
     * that have been registered by the framework.
     *
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        if (method_exists('Illuminate\\Support\\ServiceProvider', 'boot')) {
            parent::boot($events);
        }

        // Publish the database migrations and seeders
        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->databasePath() . '/migrations'
        ], 'migrations');
        $this->publishes([
            __DIR__ . '/../database/seeds' => $this->app->databasePath() . '/seeds'
        ], 'seeds');
        $this->publishes([
            __DIR__ . '/../config' => config_path()
        ], 'config');
        // TODO: Instead of publishing the views, load them up into the database using a seeder.
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path('resources/views')
        ], 'views');

        // Register other providers required by this provider, which saves the caller
        // from having to register them each individually.
        $this->app->register(\Delatbabel\SiteConfig\SiteConfigServiceProvider::class);
        $this->app->register(\Delatbabel\ViewPages\Providers\TwigBridgeServiceProvider::class);
        $this->app->register(\Delatbabel\ViewPages\Providers\IlluminateViewServiceProvider::class);
    }

    /**
     * Register the service provider.
     *
     * Within the register method, you should only bind things into the
     * service container. You should never attempt to register any event
     * listeners, routes, or any other piece of functionality within the
     * register method.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
