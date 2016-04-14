<?php
/**
 * Class VpageLoader
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Loaders;

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Class VpageLoader
 *
 * Loads views from the file system.
 */
class VpageLoader implements LoaderInterface
{
    /**
     * Load a view based on the name of the view.
     *
     * @param string $name   view name.
     * @return string
     * @throws InvalidArgumentException
     */
    public function get($name)
    {
        // Fetch the view from the database.
        $viewModel = Vpage::make($name);

        // If it is found return its content.
        if (! empty($viewModel)) {
            return $viewModel->content;
        }

        throw new InvalidArgumentException('unable to locate page ' . $name);
    }

    /**
     * Return the last modified timestamp of a view.
     *
     * @param string $name
     * @return integer
     */
    public function lastModified($name)
    {
        // Fetch the view from the database.
        $viewModel = Vpage::make($name);

        // If it is found return its last modified time as a timestamp.
        if (! empty($viewModel)) {
            $dt = new \DateTime($viewModel->updated_at);
            return $dt->format('U');
        }

        throw new InvalidArgumentException('unable to locate page ' . $name);
    }
}
