<?php

namespace Webkul\Mobikul\Models;

use Webkul\Velocity\Models\VelocityCustomerCompareProduct as BaseVelocityCustomerCompareProduct;
use Webkul\Mobikul\Contracts\CompareProduct as CompareProductContract;
use Webkul\Product\Models\ProductFlatProxy;

class CompareProduct extends BaseVelocityCustomerCompareProduct implements CompareProductContract
{
    protected $table = 'velocity_customer_compare_products';

    protected $guarded = [];

    /**
     * Get the notification that owns the attribute value.
     */
    public function product_flat()
    {
        return $this->belongsTo(ProductFlatProxy::modelClass(), 'product_flat_id');
    }
}