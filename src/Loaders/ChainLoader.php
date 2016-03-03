<?php
/**
 * Class ChainLoader
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Loaders;

use InvalidArgumentException;

/**
 * Class ChainLoader
 *
 * This class chains together multiple loaders so that they can work in series.
 */
class ChainLoader implements LoaderInterface
{
    /** @var  array of LoaderInterface */
    protected $loaders = array();

    /**
     * Add a loader to the list.
     *
     * @param LoaderInterface $loader
     * @return void
     */
    public function addLoader(LoaderInterface $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * Load a view based on the name of the view.
     *
     * @param string $name   view name.
     * @return string
     */
    public function get($name)
    {
        /** @var LoaderInterface $loader */
        foreach ($this->loaders as $loader) {
            try {
                return $loader->get($name);
            } catch (\Exception $e) {
            }
        }

        throw new InvalidArgumentException("View [$name] not found in any registered loader.");
    }
}
