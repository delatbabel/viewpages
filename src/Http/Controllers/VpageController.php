<?php
/**
 * Class VpageController
 */

namespace Delatbabel\ViewPages\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

/**
 * Class VpageController
 *
 * Handles requests for application pages.
 */
class VpageController extends BaseController
{
    /**
     * Make and return a view relating to the current URL.
     *
     * This is an example controller that can be used or copied or extended in your applications
     * to provide CMS-like functionality.  It returns the view based on the URL in the
     * current request.
     *
     * ### Example
     *
     * <code>
     * // If request contains the URL 1/2/3 then this will return the view with the URL 1/2/3
     * return $this->make($request);
     * </code>
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function make(Request $request)
    {
        $url = $request->path();
        if ($url == '/') {
            $url = 'index';
        }

        # Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #     'Make page for URL ' . $url);

        return view($url);

        // Don't do this because the Laravel facade uses the wrong factory class.
        // return View::make($url);
    }
}
