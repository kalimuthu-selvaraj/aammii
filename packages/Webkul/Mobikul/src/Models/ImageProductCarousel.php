<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Mobikul\Contracts\ImageProductCarousel as ImageProductCarouselContract;

class ImageProductCarousel extends Model implements ImageProductCarouselContract
{
    public $timestamps = false;

    protected $table = 'mobikul_carousel_images_products_pivot';

    protected $fillable = ['carousel_id', 'carousel_image_id', 'products_id'];

}
