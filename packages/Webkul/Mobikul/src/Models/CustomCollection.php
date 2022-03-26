<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Webkul\Mobikul\Contracts\CustomCollection as CustomCollectionContract;

/**
 * Class CustomCollection
 *
 * @package Webkul\Mobikul\Models
 *
 */
class CustomCollection extends Model implements CustomCollectionContract
{
    protected $table = 'mobikul_custom_collections';

    protected $fillable = [
        'name',
        'status',
        'product_collection',
        'product_ids',
        'latest_count',
        'attributes',
        'price_from',
        'price_to',
        'brand',
        'sku',
    ];
}
