<?php
/**
 * Vobject model
 */
namespace Delatbabel\ViewPages\Models;

use Delatbabel\Fluents\Fluents;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Delatbabel\SiteConfig\Models\Website;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Fluent;

/**
 * Class Vobject
 *
 * This model class is for the database backed website objects table.
 *
 * ### Example
 *
 * <code>
 * $object = Vobject::make('homepage_title');
 * </code>
 *
 * ### TODO
 *
 * Extract the logic to find a object for a specific website from the make
 * function and put it into a customised BelongsTo class.
 */
class Vobject extends Model
{
    use SoftDeletes, Fluents;

    protected $fillable = ['website_id', 'objectkey', 'name', 'description',
        'content'];

    protected $dates = ['deleted_at'];

    /**
     * 1:Many relationship with Website model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo('\Delatbabel\SiteConfig\Models\Website');
    }

    /**
     * Many:1 relationship with Category model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('\Delatbabel\NestedCategories\Models\Category');
    }

    /**
     * Fetch a object by objectkey or url.
     *
     * Returns a Vobject object for a specific objectkey for the current website.
     *
     * Multiple objects can exist in the vobjects table for any given objectkey.
     *
     * This function finds the correct object in vobjects that matches the
     * given objectkey and belongs to the current website, or if that fails
     * then it will find the correct object in vobjects for the given objectkey
     * that has no relations to any website.
     *
     * @param string $objectkey
     * @return Fluent
     */
    public static function make($objectkey = 'index')
    {
        // Sanitise the key
        $objectkey = filter_var($objectkey, FILTER_SANITIZE_STRING);

        if (empty($objectkey)) {
            // An empty URL indicates that the home object is being fetched.
            $objectkey = 'index';
        }

        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // Check for a cached copy
        $cache_key = 'vobject__' . $website_id . '__' . $objectkey;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        // Try to find a object that is joined to the current website
        /** @var Vobject $object */
        $object = static::where('objectkey', '=', $objectkey)
            ->where('website_id', '=', $website_id)
            ->select(['id', 'name', 'content', 'updated_at'])
            ->first();
        if (! empty($object)) {
            #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            #    'Found vobject on first look,  ID == ' . $object->id);
            $fluent = $object->toFluent();
            Cache::put($cache_key, $fluent, 60);
            return $fluent;
        }

        // If there is no such object, try to find a object that is not joined
        // to any website
        /** @var Vobject $object */
        $object = static::where('objectkey', '=', $objectkey)
            ->whereNull('website_id')
            ->select(['id', 'name', 'content', 'updated_at'])
            ->first();
        if (empty($object)) {
            return null;
        }

        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Found vobject on second look,  ID == ' . $object->id);
        $fluent = $object->toFluent();
        Cache::put($cache_key, $fluent, 60);
        return $fluent;
    }
}
