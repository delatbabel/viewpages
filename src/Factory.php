<?php
/**
 * Factory
 */

namespace Delatbabel\ViewPages;

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\View\Factory as BaseFactory;
use Illuminate\View\View;

/**
 * Class Factory
 *
 * This extends the Wpb\String_Blade_Compiler\Factory class so that
 * the make function, instead of looking for a view on disk, looks
 * for it in the database.
 *
 * @link https://github.com/delatbabel/StringBladeCompiler/tree/3.0
 */
class Factory extends BaseFactory
{
    /**
     * Get the evaluated view contents for the given view.
     *
     * This function will try to find the view by doing the following
     * steps in order until a hit is found:
     *
     * * Look in the vpages table for a vpage with pagekey = dashboard.sysadmin.
     * * Look in the vpages table for a vpage with url = dashboard.sysadmin.
     * * Look on disk for a view called resources/views/sysadmin/dashboard.blade.php
     * * Look in the vpages table for a vpage with pagekey = errors.410
     * * Look in the vpages table for a vpage with pagekey = errors.404
     *
     * @param  array|string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return View
     */
    public function make($view, $data = [], $mergeData = [])
    {
        if (is_array($view)) {
            // If the view is an array we want to pass it to the parent
            // class which will be Wpb\String_Blade_Compiler\Factory
            return parent::make($view, $data, $mergeData);
        }

        // If the view is a string we want to do a database lookup
        // to fetch the view contents.
        $viewModel = Vpage::make($view);

        // Now tell the parent to render the view.
        if (! empty($viewModel)) {
            return parent::make([
                'template'      => $viewModel->content,
                'cache_key'     => sha1($viewModel->id),
                'updated_at'    => $viewModel->updated_at->format('U'),
                'pagetype'      => $viewModel->pagetype,
            ], $data, $mergeData);
        }

        // If we have no view so far, fall back to an on disk
        // view.
        try {
            if (isset($this->aliases[$view])) {
                $view = $this->aliases[$view];
            }

            $view = $this->normalizeName($view);
            $path = $this->finder->find($view);
            $data = array_merge($mergeData, $this->parseData($data));
            $this->callCreator($view = new View($this, $this->getEngineFromPath($path), $view, $path, $data));
            return $view;
        } catch (\Exception $e) {
            // Failing to find a view on disk will throw an exception.
            // Let this fall through to a 410 or 404 page.
        }

        // If we have no page so far, fetch the 410 page
        $viewModel = Vpage::make('errors.410');
        if (! empty($viewModel)) {
            return parent::make([
                'template'      => $viewModel->content,
                'cache_key'     => sha1($viewModel->id),
                'updated_at'    => $viewModel->updated_at->format('U'),
                'pagetype'      => $viewModel->pagetype,
            ], $data, $mergeData);
        }

        // If we have no page so far, fetch the 404 page
        $viewModel = Vpage::make('errors.404');
        if (! empty($viewModel)) {
            return parent::make([
                'template'      => $viewModel->content,
                'cache_key'     => sha1($viewModel->id),
                'updated_at'    => $viewModel->updated_at->format('U'),
                'pagetype'      => $viewModel->pagetype,
            ], $data, $mergeData);
        }

        // We are hosed at this point.
        throw new \InvalidArgumentException("View [$view] not found.");
    }
}
