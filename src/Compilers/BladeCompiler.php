<?php
/**
 * Class BladeCompiler
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Compilers;

use Delatbabel\ViewPages\Loaders\LoaderInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\View\Compilers\BladeCompiler as BaseBladeCompiler;

/**
 * Class BladeCompiler
 *
 * The compile() function in Laravel's BladeCompiler loads the file content
 * using the Filesystem object.  This extends the compile() function to load
 * the view from the database before attempting to load it from the Filesystem.
 */
class BladeCompiler extends BaseBladeCompiler
{
    /** @var  LoaderInterface */
    protected $loader;

    /**
     * Create a new compiler instance.
     *
     * @param  Filesystem  $files
     * @param  string  $cachePath
     * @param  LoaderInterface $loader
     */
    public function __construct(Filesystem $files, $cachePath, LoaderInterface $loader)
    {
        $this->loader = $loader;
        parent::__construct($files, $cachePath);
    }

    /**
     * Compile the view at the given path.
     *
     * @param  string  $path
     * @return void
     */
    public function compile($path = null)
    {
        if ($path) {
            $this->setPath($path);
        }

        $contents = $this->compileString($this->loader->get($this->getPath()));

        if (! is_null($this->cachePath)) {
            $this->files->put($this->getCompiledPath($this->getPath()), $contents);
        }
    }

    /**
     * Determine if the view at the given path is expired.
     *
     * @param  string  $path
     * @return bool
     */
    public function isExpired($path)
    {
        // TODO: Fix all of this.
        $compiled = $this->getCompiledPath($path);

        // If the compiled file doesn't exist we will indicate that the view is expired
        // so that it can be re-compiled. Else, we will verify the last modification
        // of the views is less than the modification times of the compiled views.
        if (! $this->cachePath || ! $this->files->exists($compiled)) {
            return true;
        }

        $viewLastModified = $this->loader->lastModified($path);
        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Checking isExpired for ' . $path . ' which is ' . $viewLastModified
        #);

        // For the time being just return true. It's just as fast to reload the
        // view from the database as it is to load the view from the database
        // to check its updated time.
        $compiledLastModified = $this->files->lastModified($compiled);
        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Checking against compiled path ' . $compiled . ' which has last modified timestamp ' .
        #    $compiledLastModified
        #);

        return $viewLastModified >= $compiledLastModified;
    }
}
