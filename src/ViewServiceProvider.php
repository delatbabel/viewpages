<?php
/**
 * ViewServiceProvider
 */
namespace Delatbabel\ViewPages;

use Wpb\String_Blade_Compiler\ViewServiceProvider as BaseViewServiceProvider;

/**
 * Class ViewServiceProvider
 *
 * Service providers are the central place of all Laravel application bootstrapping.
 * Your own application, as well as all of Laravel's core services are bootstrapped
 * via service providers.
 *
 * ### Functionality
 *
 * This service provider extends the Wpb\String_Blade_Compiler\ViewServiceProvider
 * class to substitute in our own Factory class instead of the original Wpb
 * Factory class.
 *
 * @see  Illuminate\Support\ServiceProvider
 * @link http://laravel.com/docs/5.1/providers
 */
class ViewServiceProvider extends BaseViewServiceProvider
{
    /**
     * Register the factory.
     *
     * @return void
     */
    public function registerFactory()
    {
        $this->app->singleton('view', function ($app) {
            // Next we need to grab the engine resolver instance that will be used by the
            // environment. The resolver will be used by an environment to get each of
            // the various engine implementations such as plain PHP or Blade engine.
            $resolver = $app['view.engine.resolver'];

            $finder = $app['view.finder'];

            $env = new Factory($resolver, $finder, $app['events']);

            // We will also set the container instance on this view environment since the
            // view composers may be classes registered in the container, which allows
            // for great testable, flexible composers for the application developer.
            $env->setContainer($app);

            $env->share('app', $app);

            return $env;
        });
    }
}
