<?php
/**
 * Class VpageController
 */

namespace Delatbabel\ViewPages\Http\Controllers;

use Delatbabel\ViewPages\Models\Vpage;
use Illuminate\Http\Request;
use Wpb\String_Blade_Compiler\StringView;

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
        return Vpage::make($url, 'url');
    }
}
