<?php
/**
 * Class VpageController
 */

namespace Delatbabel\ViewPages\Http\Controllers;

use Illuminate\Http\Request;
use Wpb\String_Blade_Compiler\StringView;
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
     * @return StringView
     */
    public function make(Request $request)
    {
        $url = $request->path();
        if ($url == '/') {
            $url = 'index';
        }

        # Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #     'Make page for URL ' . $url);

        return View::make($url);
    }
}
