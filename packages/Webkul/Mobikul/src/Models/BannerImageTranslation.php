<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Mobikul\Contracts\BannerImageTranslation as BannerImageTranslationContract;

/**
 * Class BannerImageTranslation
 *
 * @package Webkul\Mobikul\Models
 *
 */
class BannerImageTranslation extends Model implements BannerImageTranslationContract
{
    public $timestamps = false;

    protected $table = 'mobikul_banner_translations';

    protected $fillable = [
        'name',
        'channel',
        'locale',
        'mobikul_banner_id'
    ];

    /**
     * Get the banner image that owns the attribute value.
     */
    public function banner_image()
    {
        return $this->belongsTo(BannerImageProxy::modelClass(), 'mobikul_banner_id');
    }
}