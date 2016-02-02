<?php
/**
 * Factory
 */

namespace Delatbabel\ViewPages;

use Wpb\String_Blade_Compiler\StringView;
use Wpb\String_Blade_Compiler\Factory as BaseFactory;
use Delatbabel\ViewPages\Models\Vpage;

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
     * @param  array|string  $view
     * @param  array   $data
     * @param  array   $mergeData
     * @return StringView
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
        return parent::make([
            'template'      => $viewModel->content,
            'cache_key'     => $viewModel->id,
            'updated_at'    => $viewModel->updated_at->format('U'),
        ], $data, $mergeData);
    }
}
