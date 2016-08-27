<?php
/**
 * Class IlluminateViewServiceProvider
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Providers;

use Delatbabel\ViewPages\Compilers\BladeCompiler;
use Delatbabel\ViewPages\Factory;
use Delatbabel\ViewPages\Finders\ChainViewFinder;
use Delatbabel\ViewPages\Finders\VpageViewFinder;
use Delatbabel\ViewPages\Loaders\ChainLoader;
use Delatbabel\ViewPages\Loaders\FilesystemLoader;
use Delatbabel\ViewPages\Loaders\VpageLoader;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\FileViewFinder;
use Illuminate\View\ViewServiceProvider as BaseViewServiceProvider;

/**
 * Class IlluminateViewServiceProvider
 *
 * Provide all of our alternative implementations to Laravel's View
 * service.
 */
class IlluminateViewServiceProvider extends BaseViewServiceProvider
{
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
        $this->registerEngineResolver();
        $this->registerViewFinder();
        $this->registerViewLoader();
        $this->registerFactory();
    }

    /**
     * Register the Blade engine implementation.
     *
     * @param  \Illuminate\View\Engines\EngineResolver  $resolver
     * @return void
     */
    public function registerBladeEngine($resolver)
    {
        $app = $this->app;

        // The Compiler engine requires an instance of the CompilerInterface, which in
        // this case will be the Blade compiler, so we'll first create the compiler
        // instance to pass into the engine so it can compile the views properly.
        $app->singleton('blade.compiler', function ($app) {
            $cache = $app['config']['view.compiled'];

            // Fetch the view.loader instnace that we created in registerViewLoader()
            $loader = $app['view.loader'];

            // Create our own BladeCompiler that can use the loader that we created.
            return new BladeCompiler($app['files'], $cache, $loader);
        });

        $resolver->register('blade', function () use ($app) {
            return new CompilerEngine($app['blade.compiler']);
        });
    }

    /**
     * Register the view finder implementation.
     *
     * @return void
     */
    public function registerViewFinder()
    {
        $this->app->bind('view.finder', function ($app) {

            // Create the FileViewFinder
            $paths = $app['config']['view.paths'];
            $fileViewfinder = new FileViewFinder($app['files'], $paths);

            // Create the VpageViewFinder
            $vpageViewFinder = new VpageViewFinder();

            // Create the ChainViewFinder
            $chainViewFinder = new ChainViewFinder();
            $chainViewFinder->addViewFinder($vpageViewFinder);
            $chainViewFinder->addViewFinder($fileViewfinder);

            return $chainViewFinder;
        });
    }

    /**
     * Register the view loader implementation.
     *
     * @return void
     */
    public function registerViewLoader()
    {
        $this->app->bind('view.loader', function ($app) {

            // Create the FilesystemLoader
            $filesystemLoader = new FilesystemLoader($app['files']);

            // Create the VpageLoader
            $vpageLoader = new VpageLoader();

            // Create the ChainLoader
            $chainLoader = new ChainLoader();
            $chainLoader->addLoader($vpageLoader);
            $chainLoader->addLoader($filesystemLoader);

            return $chainLoader;
        });
    }

    /**
     * Register the view environment.
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
