<?php
/**
 * Class ChainViewFinder
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Finders;

use Illuminate\View\ViewFinderInterface;
use InvalidArgumentException;

/**
 * Class ChainViewFinder
 *
 * This class chains together multiple ViewFinders so that they can work in series.
 */
class ChainViewFinder implements ViewFinderInterface
{
    /** @var  array of ViewFinderInterface */
    protected $viewFinders = [];

    /**
     * Add a viewfinder to the list.
     *
     * @param ViewFinderInterface $viewFinder
     * @return void
     */
    public function addViewFinder(ViewFinderInterface $viewFinder)
    {
        $this->viewFinders[] = $viewFinder;
    }

    /**
     * Get the fully qualified location of the view.
     *
     * @param  string  $view
     * @return string
     */
    public function find($view)
    {
        /** @var ViewFinderInterface $viewFinder */
        foreach ($this->viewFinders as $viewFinder) {
            try {
                return $viewFinder->find($view);
            } catch (\Exception $e) {
            }
        }

        throw new InvalidArgumentException("View [$view] not found in any registered viewfinder.");
    }

    /**
     * Add a location to the finder.
     *
     * @param  string  $location
     * @return void
     */
    public function addLocation($location)
    {
        /** @var ViewFinderInterface $viewFinder */
        foreach ($this->viewFinders as $viewFinder) {
            try {
                $viewFinder->addLocation($location);
            } catch (\Exception $e) {
            }
        }
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
        /** @var ViewFinderInterface $viewFinder */
        foreach ($this->viewFinders as $viewFinder) {
            try {
                $viewFinder->addNamespace($namespace, $hints);
            } catch (\Exception $e) {
            }
        }
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
        /** @var ViewFinderInterface $viewFinder */
        foreach ($this->viewFinders as $viewFinder) {
            try {
                $viewFinder->prependNamespace($namespace, $hints);
            } catch (\Exception $e) {
            }
        }
    }

    /**
     * Add a valid view extension to the finder.
     *
     * @param  string  $extension
     * @return void
     */
    public function addExtension($extension)
    {
        /** @var ViewFinderInterface $viewFinder */
        foreach ($this->viewFinders as $viewFinder) {
            try {
                $viewFinder->addExtension($extension);
            } catch (\Exception $e) {
            }
        }
    }
}
