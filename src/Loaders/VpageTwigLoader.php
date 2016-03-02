<?php
/**
 * Class VpageTwigLoader
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Loaders;

use Delatbabel\ViewPages\Models\Vpage;
use Twig_LoaderInterface;
use Twig_Error_Loader;

/**
 * Class VpageTwigLoader
 *
 * This implements the Twig_LoaderInterface for loading Twig templates
 * from the database (vpages table).
 */
class VpageTwigLoader implements Twig_LoaderInterface
{
    /**
     * Gets the source code of a template, given its name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The template source code
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getSource($name)
    {
        // Fetch the view from the database.
        $viewModel = Vpage::make($name);

        // If it is found return its content.
        if (! empty($viewModel)) {
            return $viewModel->content;
        }

        throw new Twig_Error_Loader('unable to locate page ' . $name);
    }

    /**
     * Gets the cache key to use for the cache for a given template name.
     *
     * @param string $name The name of the template to load
     *
     * @return string The cache key
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function getCacheKey($name)
    {
        return __CLASS__ . $name;
    }

    /**
     * Returns true if the template is still fresh.
     *
     * @param string $name The template name
     * @param int    $time Timestamp of the last modification time of the
     *                     cached template
     *
     * @return bool true if the template is fresh, false otherwise
     *
     * @throws Twig_Error_Loader When $name is not found
     */
    public function isFresh($name, $time)
    {
        // May as well return false here.  Fetching the page from the database
        // to determine whether the page is fresh or not is no more or less work
        // than fetching the page from the database due to it not being fresh.
        return false;
    }
}
