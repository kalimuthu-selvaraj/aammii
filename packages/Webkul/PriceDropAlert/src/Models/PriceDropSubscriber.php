<?php

namespace Webkul\PriceDropAlert\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\PriceDropAlert\Contracts\PriceDropSubscriber as PriceDropSubscriberContract;

class PriceDropSubscriber extends Model implements PriceDropSubscriberContract
{
    public $timestamps = true;

    protected $table = 'price_drop_subscribers';

    protected $fillable = ['email', 'product_id', 'base_price', 'status'];
}
