<?php

namespace Webkul\Mobikul\Payment;

use Webkul\Payment\Payment\Payment;

class RazorPayMobile extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'razorpay_mobile';

    public function getRedirectUrl()
    {
        
    }

    /**
     * Returns payment method additional information
     *
     * @return array
     */
    public function getAdditionalDetails()
    {
        return [
            'title' => 'RazorPay Mobile',
            'value' => 'RazorPay Mobile Payment',
        ];
    }

}