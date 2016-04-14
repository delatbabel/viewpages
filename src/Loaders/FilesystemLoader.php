<?php
/**
 * Class FilesystemLoader
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Loaders;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * Class FilesystemLoader
 *
 * Loads views from the file system.
 */
class FilesystemLoader implements LoaderInterface
{
    /**
     * The Filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new loader instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Load a view based on the name of the view.
     *
     * @param string $name   view name.
     * @return string
     * @throws FileNotFoundException
     */
    public function get($name)
    {
        return $this->files->get($name);
    }

    /**
     * Return the last modified timestamp of a view.
     *
     * @param string $name
     * @return integer
     * @throws FileNotFoundException
     */
    public function lastModified($name)
    {
        if (! $this->files->exists($name)) {
            throw new FileNotFoundException("$name does not exist");
        }
        return $this->files->lastModified($name);
    }
}
