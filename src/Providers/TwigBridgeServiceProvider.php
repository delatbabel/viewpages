<?php
/**
 * Class TwigBridgeServiceProvider
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Providers;

use Delatbabel\ViewPages\Loaders\VpageTwigLoader;
use Twig_Loader_Array;
use Twig_Loader_Chain;
use TwigBridge\ServiceProvider as BaseServiceProvider;
use TwigBridge\Twig\Loader;

/**
 * Class TwigBridgeServiceProvider
 *
 * This replaces the ServiceProvider class in the TwigBridge package so
 * that the appropriate finders are loaded.
 */
class TwigBridgeServiceProvider extends BaseServiceProvider
{
    /**
     * Register Twig loader bindings.
     *
     * @return void
     */
    protected function registerLoaders()
    {
        // The array used in the ArrayLoader
        $this->app->bindIf('twig.templates', function () {
            return [];
        });

        $this->app->bindIf('twig.loader.array', function ($app) {
            return new Twig_Loader_Array($app['twig.templates']);
        });

        $this->app->bindIf('twig.loader.viewfinder', function () {
            return new Loader(
                $this->app['files'],
                // app['view']->getFinder() comes from Factory which is created in the
                // IlluminateViewServiceProvider class.
                $this->app['view']->getFinder(),
                $this->app['twig.extension']
            );
        });

        $this->app->bindIf('twig.loader.vpage', function () {
            return new VpageTwigLoader();
        });

        $this->app->bindIf(
            'twig.loader',
            function () {
                return new Twig_Loader_Chain([
                    $this->app['twig.loader.array'],
                    $this->app['twig.loader.vpage'],
                    $this->app['twig.loader.viewfinder'],
                ]);
            },
            true
        );
    }
}

/*
 * Original function, just for comparison
 *
protected function registerLoaders()
{
    // The array used in the ArrayLoader
    $this->app->bindIf('twig.templates', function () {
        return [];
    });

    $this->app->bindIf('twig.loader.array', function ($app) {
        return new Twig_Loader_Array($app['twig.templates']);
    });

    $this->app->bindIf('twig.loader.viewfinder', function () {
        return new Twig\Loader(
            $this->app['files'],
            $this->app['view']->getFinder(),
            $this->app['twig.extension']
        );
    });

    $this->app->bindIf(
        'twig.loader',
        function () {
            return new Twig_Loader_Chain([
                $this->app['twig.loader.array'],
                $this->app['twig.loader.viewfinder'],
            ]);
        },
        true
    );
}
*/
