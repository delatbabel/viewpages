<?php
/**
 * Vobject model
 */
namespace Delatbabel\ViewPages\Models;

use Delatbabel\Fluents\Fluents;
use Delatbabel\SiteConfig\Models\Website;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
 * $object = Vobject::make('homeobject_title');
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

    protected $fillable = ['category_id', 'objectkey', 'name', 'description',
        'content'];

    protected $dates = ['deleted_at'];

    /**
     * Many:Many relationship with Website model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function websites()
    {
        return $this->belongsToMany('\Delatbabel\SiteConfig\Models\Website');
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
     * Returns a Vobject object for a specific objectkey or URL for the
     * current website.
     *
     * A Vobject object can either be for a specific website or websites,
     * in which case there will be a join table entry in vobject_website
     * containing (vobject_id, website_id), or the Vobject can be for all
     * websites, which means that there will be no join table entry in
     * vobject_website for that vobject_id at all (for any website).
     *
     * Multiple objects can exist in the vobjects table for any given URL.
     *
     * This function finds the correct object in vobjects that matches the
     * given URL and has a join to the current website, or if that fails
     * then it will find the correct object in vobjects for the given URL
     * that has no joins to any website.
     *
     * @param string $objectkey
     * @return Fluent
     */
    public static function make($objectkey = 'index')
    {
        // Sanitise the URL
        $objectkey = filter_var($objectkey, FILTER_SANITIZE_SPECIAL_CHARS);

        Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            'Looking for vobject where objectkey = ' . $objectkey);

        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // We seem to do a lot of object fetching twice here, often to
        // get the object type, then to get the content and also the updated_at
        // time.  Cache the results after one fetch.
        $cache_key = 'vobject__' . $website_id . '__' . $objectkey;
        if (Cache::has($cache_key)) {
            Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                'Found in cache');
            return Cache::get($cache_key);
        }

        // Try to find a object that is joined to the current website
        /** @var Vobject $object */
        $object = static::where('objectkey', '=', $objectkey)
            ->join('vobject_website', 'vobjects.id', '=', 'vobject_website.vobject_id')
            ->where('vobject_website.website_id', '=', $website_id)
            ->select('vobjects.id AS id',
                'vobjects.content AS content',
                'vobjects.updated_at AS updated_at')
            ->first();
        if (! empty($object)) {
            Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
                'Found vobject on first look,  ID == ' . $object->id);
            $fluent = $object->toFluent();
            Cache::put($cache_key, $fluent, 60);
            return $fluent;
        }

        // If there is no such object, try to find a object that is not joined
        // to any website
        /** @var Vobject $object */
        $object = static::where('objectkey', '=', $objectkey)
            ->leftJoin('vobject_website', 'vobjects.id', '=', 'vobject_website.vobject_id')
            ->whereNull('vobject_website.website_id')
            ->select('vobjects.id AS id',
                'vobjects.content AS content',
                'vobjects.updated_at AS updated_at')
            ->first();
        if (empty($object)) {
            return null;
        }

        Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            'Found vobject on second look,  ID == ' . $object->id);
        $fluent = $object->toFluent();
        Cache::put($cache_key, $fluent, 60);
        return $fluent;
    }
}
