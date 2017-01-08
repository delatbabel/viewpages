<?php
/**
 * Vpage model
 */
namespace Delatbabel\ViewPages\Models;

use Delatbabel\Fluents\Fluents;
use Delatbabel\SiteConfig\Models\Website;
use Delatbabel\ViewPages\Finders\VpageViewFinder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Fluent;
use Log;

/**
 * Class Vpage
 *
 * This model class is for the database backed website templates table.
 *
 * ### Example
 *
 * <code>
 * $page = Vpage::make('index');
 * </code>
 *
 * ### TODO
 *
 * Extract the logic to find a page for a specific website from the make
 * function and put it into a customised BelongsToMany class.
 */
class Vpage extends Model
{
    use SoftDeletes, Fluents;

    protected $fillable = ['pagekey', 'name', 'url', 'description', 'pagetype',
        'is_secure', 'content'];

    protected $dates = ['deleted_at'];

    /**
     * Used to separate the page name from the page type
     *
     * @var string
     */
    const EXTENSION_SEPARATOR = '||';

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
     * Fetch a page by pagekey or url.
     *
     * Returns a Vpage object for a specific pagekey or URL for the
     * current website.
     *
     * A Vpage object can either be for a specific website or websites,
     * in which case there will be a join table entry in vpage_website
     * containing (vpage_id, website_id), or the Vpage can be for all
     * websites, which means that there will be no join table entry in
     * vpage_website for that vpage_id at all (for any website).
     *
     * Multiple pages can exist in the vpages table for any given URL.
     *
     * This function finds the correct page in vpages that matches the
     * given URL and has a join to the current website, or if that fails
     * then it will find the correct page in vpages for the given URL
     * that has no joins to any website.
     *
     * Finding by URL is common in CMS front ends, otherwise to emulate
     * the functionality of Laravel's View::make() function use the
     * 'pagekey' option.
     *
     * @param string $url
     * @param string $field 'pagekey' or 'url'
     * @param string $namespace
     * @return Fluent|null
     */
    public static function fetch($url = 'index', $field = 'pagekey', $namespace = '')
    {
        // Sanitise the URL
        $url = filter_var($url, FILTER_SANITIZE_STRING);

        if (empty($url)) {
            // An empty URL indicates that the home page is being fetched.
            $url = 'index';
        }

        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Looking for vpage where ' . $field . ' = ' . $url);

        // Determine whether there is an extension separator on the page
        // key or not, and strip it off if there is one present.
        $url_parts = explode(static::EXTENSION_SEPARATOR, $url, 2);
        $url       = $url_parts[0];

        // TODO: The extension for the pagetype is in $url_parts[1] if it
        // is not empty. This could be "blade.php", "php" or "twig".  Restrict
        // the query to pages where the pagetype matches the extension.

        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // We seem to do a lot of page fetching twice here, often to
        // get the page type, then to get the content and also the updated_at
        // time.  Cache the results after one fetch.
        $cache_key = 'vpage__' . $website_id . '__' . $url;
        if (Cache::has($cache_key)) {
            #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            #    'Found in cache');
            return Cache::get($cache_key);
        }

        // Try to find a page that is joined to the current website
        /** @var Vpage $page */
        $page = static::where($field, '=', $url)
            ->where('vpages.namespace', '=', $namespace)
            ->join('vpage_website', 'vpages.id', '=', 'vpage_website.vpage_id')
            ->where('vpage_website.website_id', '=', $website_id)
            ->select('vpages.id AS id',
                'vpages.content AS content',
                'vpages.updated_at AS updated_at',
                'vpages.pagetype AS pagetype')
            ->first();
        if (! empty($page)) {
            #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
            #    'Found vpage on first look,  ID == ' . $page->id);
            $fluent = $page->toFluent();
            Cache::put($cache_key, $fluent, 60);
            return $fluent;
        }

        // If there is no such page, try to find a page that is not joined
        // to any website
        /** @var Vpage $page */
        $page = static::where($field, '=', $url)
            ->where('vpages.namespace', '=', $namespace)
            ->leftJoin('vpage_website', 'vpages.id', '=', 'vpage_website.vpage_id')
            ->whereNull('vpage_website.website_id')
            ->select('vpages.id AS id',
                'vpages.content AS content',
                'vpages.updated_at AS updated_at',
                'vpages.pagetype AS pagetype')
            ->first();
        if (empty($page)) {
            return null;
        }

        #Log::debug(__CLASS__ . ':' . __TRAIT__ . ':' . __FILE__ . ':' . __LINE__ . ':' . __FUNCTION__ . ':' .
        #    'Found vpage on second look,  ID == ' . $page->id);
        $fluent = $page->toFluent();
        Cache::put($cache_key, $fluent, 60);
        return $fluent;
    }

    /**
     * Make page
     *
     * Returns a Vpage object for a specific URL for the
     * current website.
     *
     * A Vpage object can either be for a specific website or websites,
     * in which case there will be a join table entry in vpage_website
     * containing (vpage_id, website_id), or the Vpage can be for all
     * websites, which means that there will be no join table entry in
     * vpage_website for that vpage_id at all (for any website).
     *
     * Multiple pages can exist in the vpages table for any given URL.
     *
     * This function finds the correct page in vpages that matches the
     * given URL and has a join to the current website, or if that fails
     * then it will find the correct page in vpages for the given URL
     * that has no joins to any website.
     *
     * Finding by URL is common in CMS front ends, otherwise to emulate
     * the functionality of Laravel's View::make() function use the
     * 'pagekey' option.
     *
     * This function looks for a page by pagekey first and if it does not
     * find it then it looks by URL.
     *
     * @param string $url
     * @return Fluent
     */
    public static function make($url = 'index')
    {
        // Find by pagekey first.  Convert any '/' characters injected
        // into the search back to '.' characters and allow for namespaced
        // views.
        $pagekey = strtr($url, '/', '.');
        $segments = explode(VpageViewFinder::HINT_PATH_DELIMITER, $pagekey);
        if (count($segments) == 2) {
            // Namespaced view
            $page    = static::fetch($segments[1], 'pagekey', $segments[0]);
        } else {
            // Not namespaced view
            $page    = static::fetch($pagekey, 'pagekey');
        }


        // Then find by URL if pagekey is not found
        if (empty($page)) {
            $page = static::fetch($url, 'url');
        }

        return $page;
    }
}
