<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Mobikul\Contracts\CarouselTranslation as CarouselTranslationContract;

/**
 * Class CarouselTranslation
 *
 * @package Webkul\Mobikul\Models
 *
 */
class CarouselTranslation extends Model implements CarouselTranslationContract
{
    public $timestamps = false;

    protected $table = 'mobikul_carousel_translations';
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the notification that owns the attribute value.
     */
    public function carousel()
    {
        return $this->belongsTo(CarouselProxy::modelClass());
    }
}