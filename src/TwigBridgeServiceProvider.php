<?php
/**
 * Class TwigBridgeServiceProvider
 *
 * @author del
 */

namespace Delatbabel\ViewPages;

use Delatbabel\ViewPages\Loaders\VpageTwigLoader;
use TwigBridge\ServiceProvider as BaseServiceProvider;
use Twig_Loader_Array;
use TwigBridge\Twig\Loader;
use Twig_Loader_Chain;

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
                $this->app['view']->getFinder(),
                $this->app['twig.extension']
            );
        });

        $this->app->bindIf('twig.loader.vpage', function() {
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
