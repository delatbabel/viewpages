<?php

namespace Delatbabel\ViewPages\Models;

use Delatbabel\ViewPages\Helpers\ImageHelper;

/**
 * CarouselImage Model
 */
class CarouselImage extends GenericNode
{
    protected $table   = 'carouselimages';
    protected $guarded = ['id'];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];

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
     * Many:Many relationship with Carousel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function carousels()
    {
        return $this->belongsToMany('\Delatbabel\ViewPages\Models\Carousel', 'carousel_carouselimage', 'carouselimage_id', 'carousel_id');
    }

    /**
     * Accessor to get preview string
     *
     * @return string
     */
    public function getPreviewAttribute()
    {
        if ($this->use_html) {
            return 'HTML Content';
        } elseif ($this->path) {
            $row_image = ImageHelper::getImageUrl($this->path);
            return '<img src="' . $row_image . '" class="thumbnail" />';
        }
    }

    /**
     * Get raw image attribute
     *
     * @return string
     */
    public function getRawImageAttribute()
    {
        if ($this->path) {
            return ImageHelper::getImageUrl($this->path);
        }
    }
}
