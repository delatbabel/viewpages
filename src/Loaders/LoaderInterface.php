<?php
/**
 * Class LoaderInterface
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Loaders;

/**
 * Interface LoaderInterface
 *
 * This defines the interface for a view loader.
 *
 * This is to allow the replacing of the implicit loader in the BladeCompiler
 * compile() function, which always uses the Filesystem object to load views,
 * with a generic loader that can load from files, databases, etc.
 *
 * Example implementations:
 *
 * * Load from a file.
 * * Load from a database table.
 */
interface LoaderInterface
{
    /**
     * Load a view based on the name of the view.
     *
     * @param string $name   view name.
     * @return string
     */
    public function get($name);
}
