<?php
/**
 * Class VobjectService
 *
 * @author del
 */

namespace Delatbabel\ViewPages\Services;

use Delatbabel\SiteConfig\Models\Website;
use Delatbabel\ViewPages\Models\Vobject;
use Illuminate\Support\Facades\Cache;


/**
 * Class VobjectService
 *
 * Provides in-view rendering of view objects
 *
 * ### Example
 *
 * <code>
 * \@inject('objects', 'Delatbabel\ViewPages\Services\VobjectService')
 *
 * <!-- Using the regular make method -->
 * <title> {{ $objects->make('page_title') }} </title>
 *
 * <!-- Using a magic getter -->
 * <title> {{ $objects->page_title }} </title>
 * </code>
 *
 * @see  Delatbabel\ViewPages\Models\Vobject
 */
class VobjectService
{
    /**
     * Return an object content from the cache or the database
     *
     * @param string $objectkey
     * @return string|null
     */
    public function make($objectkey)
    {
        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // Check for a cached copy
        $cache_key = 'objects.' . $website_id . $objectkey;
        if (Cache::has($cache_key)) {
            $vobject = Cache::get($cache_key);
        } else {
            $vobject = Vobject::make($objectkey);
            Cache::put($cache_key, $vobject, 60);
        }

        // Return null if the object is not found
        if (empty($vobject)) {
            return null;
        }

        // Return the object content
        return $vobject->content;
    }

    /**
     * Magic getter method
     *
     * @param string $objectkey
     * @return null|string
     */
    public function __get($objectkey)
    {
        return $this->make($objectkey);
    }
}
