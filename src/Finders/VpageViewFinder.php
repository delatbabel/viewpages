<?php
/**
 * Class VpageViewFinder
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Finders;

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;

/**
 * Class VpageViewFinder
 *
 * This allows a view to be found in the database.
 */
class VpageViewFinder implements ViewFinderInterface
{
    /**
     * Get the fully qualified location of the view.
     *
     * Just returns $view if $view is in the database, otherwise throws an
     * exception.
     *
     * @param  string  $view
     * @return string
     */
    public function find($view)
    {
        // Check to see if the page exists in the database
        $vpage = Vpage::make($view);
        if (! empty($vpage)) {
            return $view . Vpage::EXTENSION_SEPARATOR . $vpage->pagetype;
        }

        throw new InvalidArgumentException("View [$view] not found in vpage table.");
    }

    /**
     * Add a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function addLocation($location)
    {
        // Ignore locations
    }

    /**
     * Add a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function addNamespace($namespace, $hints)
    {
        // Ignore namespaces
    }

    /**
     * Prepend a namespace hint to the finder.
     *
     * @param  string  $namespace
     * @param  string|array  $hints
     * @return void
     */
    public function prependNamespace($namespace, $hints)
    {
        // Ignore namespaces
    }

    /**
     * Add a valid view extension to the finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension)
    {
        // Ignore extensions
    }
}
