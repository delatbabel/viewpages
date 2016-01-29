<?php
/**
 * Vppage model
 */
namespace Delatbabel\ViewPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Delatbabel\SiteConfig\Models\Website;

/**
 * Class Vppage
 *
 * This model class is for the database backed website templates table.
 *
 * ### Example
 *
 * <code>
 * $page = Vppage::make('index');
 * </code>
 *
 * ### TODO
 *
 * Extract the logic to find a page for a specific website from the make
 * function and put it into a customised BelongsToMany class.
 *
 * Be able to handle all of the various directives in a normal Blade template
 * such as @extends, @section / @endsection, etc.  @extends should pull in
 * the template from the Vptemplate model class.
 */
class Vppage extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'name', 'url', 'description',
        'vptemplate_id', 'is_secure', 'is_homepage', 'content'];

    protected $dates = ['deleted_at'];

    /**
     * 1:Many relationship with Vppage model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function configs()
    {
        return $this->hasMany('Delatbabel\ViewPages\Models\Vppage');
    }

    /**
     * Many:Many relationship with Website model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function websites()
    {
        return $this->belongsToMany('Delatbabel\SiteConfig\Models\Website');
    }

    /**
     * Make page
     *
     * Returns a Vppage object for a specific URL for the current website.
     *
     * A Vppage object can either be for a specific website or websites,
     * in which case there will be a join table entry in vppage_website
     * containing (vppage_id, website_id), or the Vppage can be for all
     * websites, which means that there will be no join table entry in
     * vppage_website for that vppage_id at all (for any website).
     *
     * Multiple pages can exist in the vppages table for any given URL.
     *
     * This function finds the correct page in vppages that matches the
     * given URL and has a join to the current website, or if that fails
     * then it will find the correct page in vppages for the given URL
     * that has no joins to any website.
     *
     * @param string $url
     * @return Vppage
     */
    public static function make($url = 'index')
    {
        $url = filter_var($url, FILTER_SANITIZE_STRING);

        if (empty($url)) {
            // An empty URL indicates that the home page is being fetched.
            $url = 'index';
        }

        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // Try to find a page that is joined to the current website
        $page = static::where('url', '=', $url)
            ->join('vppage_website', 'vppage.id', '=', 'vppage_website.vppage_id')
            ->where('vppage_website.website_id', '=', $website_id)
            ->first();
        if (! empty($page)) {
            return $page;
        }

        // If there is no such page, try to find a page that is not joined
        // to any website
        $page = static::where('url', '=', $url)
            ->leftJoin('vppage_website', 'vppages.id', '=', 'vppage_website.vppage_id')
            ->whereNull('vppage_website.website_id')
            ->first();
        if (! empty($page)) {
            return $page;
        }

        // If we have no page so far, fetch the 410 page
        $page = static::make('410');
        if (! empty($page)) {
            return $page;
        }

        // If we have no page so far, fetch the 404 page
        return static::make('410');
    }

    /**
     * Return the template for the current page and website.
     *
     * @return Vptemplate
     */
    public function fetchTemplate()
    {
        return Vptemplate::make($this->vptemplate_key);
    }
}
