<?php

namespace Webkul\TableRate\Carriers;

use Webkul\Checkout\Models\CartShippingRate;
use Webkul\Shipping\Carriers\AbstractShipping;
use Webkul\Checkout\Facades\Cart;

/**
 * Table Rate Shipping.
 *
 */
class TableRate extends AbstractShipping
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'tablerate';

    /**
     * Returns rate for flatrate
     *
     * @return array
     */
    public function calculate()
    {
        if (! $this->isAvailable()) {
            return false;
        }

        $shippingMethods    = [];
        $rates              = [];
        $cartItems          = Cart::getCart()->items;
        $shippingData       = app('Webkul\TableRate\Helpers\ShippingHelper');
        $commonMethods      = $shippingData->getCommanRates(
            $shippingData->findAppropriateTableRateMethods(),
            count($cartItems)
        );
        
        $shipping_rates     = session()->get('shipping_rates');

        if (! empty($commonMethods) ) {
            $itemShippingWeight = 0;
            $shippingCost = 0;

            foreach ($commonMethods as $superset_code => $shippingRates) {
                $totalShippingCost  = 0;

                foreach ($shippingRates as $shippingRate) {
                    $superset_name  = $shippingRate['superset_name'];

                    if ($this->getConfigData('type') == 'per_unit') {
                        $itemShippingCost =  $shippingRate['shipping_cost'] * $shippingRate['quantity'];
                        
                        $itemShippingWeight += ($shippingRate['quantity'] * $shippingRate['weight']);
                        $shippingCost = $shippingRate['shipping_cost'];
                    } else {
                        $itemShippingCost =  $shippingRate['shipping_cost'];
                    }

                    if ( isset($rates[$superset_name]) ) {
                        $rates[$superset_name] = [
                            'amount'        => core()->convertPrice($rates[$superset_name]['amount'] + $itemShippingCost),
                            'base_amount'   => $rates[$superset_name]['base_amount'] + $itemShippingCost
                        ];
                    } else {
                        $rates[$superset_name] = [
                            'amount'        => core()->convertPrice($itemShippingCost),
                            'base_amount'   => $shippingCost * ceil($itemShippingWeight)
                        ];
                    }

                    $totalShippingCost = $shippingCost * ceil($itemShippingWeight);
                }

                $object                     = new CartShippingRate;
                $object->carrier            = 'tablerate';
                $object->carrier_title      = $this->getConfigData('title');
                $object->method             = 'tablerate_' . $superset_code;
                $object->method_title       = $superset_name;
                $object->method_description = $this->getConfigData('title') . ' - ' . $superset_name;
                $object->is_calculate_tax   = $this->getConfigData('is_calculate_tax') ?: 0;
                $object->price              = core()->convertPrice($totalShippingCost);
                
                $object->base_price         = $totalShippingCost;

                $shipping_rates = session()->get('shipping_rates');

                if (! is_array($shipping_rates)) {
                    $shipment_rates['tablerate'] = $rates;

                    session()->put('shipping_rates', $shipment_rates);
                } else {
                    session()->put('shipping_rates.tablerate', $rates);
                }

                array_push($shippingMethods, $object);
            }
            
            return $shippingMethods;
        }
    }

    public function getServices()
    {
        return null;
    }
}
