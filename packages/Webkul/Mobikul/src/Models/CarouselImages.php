<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Mobikul\Contracts\CarouselImages as CarouselImagesContract;

class CarouselImages extends Model implements CarouselImagesContract
{
    public $timestamps = true;

    protected $table = 'mobikul_carousel_images';

    protected $guarded = ['_token'];

    protected $fillable = [
        'title',
        'type',
        'product_category_id',
        'status'
    ];

    /**
     * Get image url for the carouselImage image.
     */
    public function image_url()
    {
        if (! $this->image)
            return;

        return Storage::url($this->image);
    }

    /**
     * Get image url for the carouselImage image.
     */
    public function getImageUrlAttribute()
    {
        return $this->image_url();
    }
}
