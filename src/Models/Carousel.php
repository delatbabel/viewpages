<?php

namespace Delatbabel\ViewPages\Models;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * Carousel Model
 */
class Carousel extends Model
{

    protected $table   = 'carousels';
    protected $guarded = ['id'];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at', 'start_date', 'end_date'];

    /**
     * Make sure that status is always capitalized when retrieved from the database
     *
     * @return string
     */
    public function getDisplayStatusAttribute()
    {
        return ucfirst($this->status);
    }

    /**
     * Many:Many relationship with CarouselImage
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carouselimages()
    {
        return $this->belongsToMany('\Delatbabel\ViewPages\Models\CarouselImage', 'carousel_carouselimage', 'carousel_id', 'carouselimage_id');
    }

    /**
     * Many:Many relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }
    /**
     * Reformat integer value to text when retrieved from the database
     *
     * @return string
     */
    public function getDisplayForLoggedInAttribute()
    {
        switch ($this->for_logged_in) {
            case 1:
                return 'Members';
                break;
            case 0:
                return 'Non-Members';
                break;
        }
        // Null or 2 or any other value
        return 'All';
    }

    /**
     * Filter only active status
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopePublic($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Many:Many relationship with CarouselImage when displaying on public
     *
     * Filter only active status
     * Order by lft desc
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function publicCarouselImages()
    {
        return $this->belongsToMany('\Delatbabel\ViewPages\Models\CarouselImage', 'carousel_carouselimage', 'carousel_id', 'carouselimage_id')
            ->where('status', 'active')->orderBy('lft', 'desc');
    }
}
