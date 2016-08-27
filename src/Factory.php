<?php
/**
 * Factory
 */

namespace Delatbabel\ViewPages;

use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\View\Factory as BaseFactory;
use Illuminate\View\View;

/**
 * Class Factory
 *
 * This extends the Illuminate\View\Factory class so that
 * the make function, instead of looking for a view on disk, looks
 * for it in the database.
 */
class Factory extends BaseFactory implements FactoryContract
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return \Illuminate\Contracts\View\View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        if (isset($this->aliases[$view])) {
            $view = $this->aliases[$view];
        }

        try {
            $view = $this->normalizeName($view);
            $path = $this->finder->find($view);
            $data = array_merge($mergeData, $this->parseData($data));
            $this->callCreator($view = new View($this, $this->getEngineFromPath($path), $view, $path, $data));
            return $view;
        } catch (\Exception $e) {
            // Failing to find a view on disk will throw an exception.
            // Let this fall through to a 410 or 404 page.
        }

        // If no view is found then return the 410 page.
        try {
            $view = 'errors.410';
            $path = $this->finder->find($view);
            $data = array_merge($mergeData, $this->parseData($data));
            $this->callCreator($view = new View($this, $this->getEngineFromPath($path), $view, $path, $data));
            return $view;
        } catch (\Exception $e) {
        }

        // If we still have no page then return the 404 page.
        try {
            $view = 'errors.404';
            $path = $this->finder->find($view);
            $data = array_merge($mergeData, $this->parseData($data));
            $this->callCreator($view = new View($this, $this->getEngineFromPath($path), $view, $path, $data));
            return $view;
        } catch (\Exception $e) {
        }

        // We are hosed at this point.
        throw new \InvalidArgumentException("View [$view] not found.");
    }
}
