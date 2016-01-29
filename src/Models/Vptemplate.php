<?php
/**
 * Vptemplate model
 */
namespace Delatbabel\ViewPages\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Delatbabel\SiteConfig\Models\Website;
use Wpb\String_Blade_Compiler\StringView;
use Wpb\String_Blade_Compiler\Facades\StringBlade;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Vptemplate
 *
 * This model class is for the database backed website templates table.
 *
 * ### Example
 *
 * <code>
 * $page = Vptemplate::make('main');
 * </code>
 */
class Vptemplate extends Model
{
    use SoftDeletes;

    protected $fillable = ['key', 'name', 'description', 'is_default', 'content'];

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
     * Render template
     *
     * TODO: Be able to handle all of the various directives in a normal
     * Blade template such as @extends, @section / @endsection etc. This
     * may require extending the StringView class.
     *
     * @param Arrayable|array $data
     * @param Arrayable|array $mergeData
     * @return StringView
     */
    public function render($data = array(), $mergeData = array())
    {
        $data = $this->parseData($data);
        $mergeData = $this->parseData($mergeData);

        return StringBlade::make([
            'template'      => $this->content,
            'cache_key'     => $this->id,
            'updated_at'    => $this->updated_at->format('U'),
        ], $data, $mergeData);
    }

    /**
     * Parse the given data into a raw array.
     *
     * @param  Arrayable|array  $data
     * @return array
     */
    protected function parseData($data)
    {
        return $data instanceof Arrayable ? $data->toArray() : $data;
    }

    /**
     * Make template
     *
     * Returns a Vptemplate object for a specific URL.
     *
     * A Vptemplate object can either be for a specific website or websites,
     * in which case there will be a join table entry in vptemplate_website
     * containing (vptemplate_id, website_id), or the Vptemplate can be for all
     * websites, which means that there will be no join table entry in
     * vptemplate_website for that vptemplate_id at all (for any website).
     *
     * Multiple templates can exist in the vptemplates table for any given URL.
     *
     * This function finds the correct template in vptemplates that matches the
     * given URL and has a join to the current website, or if that fails
     * then it will find the correct template in vptemplates for the given URL
     * that has no joins to any website.
     *
     * @param string $key
     * @return Vptemplate
     */
    public static function make($key = 'main')
    {
        $key = filter_var($key, FILTER_SANITIZE_STRING);

        // Find the current website ID
        $website_id = Website::currentWebsiteId();

        // Try to find a template that is joined to the current website
        $template = static::where('key', '=', $key)
            ->join('vptemplate_website', 'vptemplate.id', '=', 'vptemplate_website.vptemplate_id')
            ->where('vptemplate_website.website_id', '=', $website_id)
            ->first();
        if (! empty($template)) {
            return $template;
        }

        // If there is no such template, try to find a template that is not joined
        // to any website
        $template = static::where('key', '=', $key)
            ->leftJoin('vptemplate_website', 'vptemplates.id', '=', 'vptemplate_website.vptemplate_id')
            ->whereNull('vptemplate_website.website_id')
            ->first();
        return $template;
    }
}
