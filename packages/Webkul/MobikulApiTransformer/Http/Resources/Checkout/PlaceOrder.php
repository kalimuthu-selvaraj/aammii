<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Checkout;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Facades\DB;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Payment\Facades\Payment;
use Razorpay\Api\Errors\SignatureVerificationError;
use Razorpay\Api\Api;

class PlaceOrder extends JsonResource
{
    protected $authError = null;
    /**
     * Contains customer's group id.
     *
     * @var int
     */
    protected $customerGroupId = 0;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->orderRepository = app('Webkul\Sales\Repositories\OrderRepository');

        $this->cartRepository = app('Webkul\Checkout\Repositories\CartRepository');

        $this->customerRepository = app('Webkul\Customer\Repositories\CustomerRepository');

        $this->customerGroupRepository = app('Webkul\Customer\Repositories\CustomerGroupRepository');

        $this->invoiceRepository = app('Webkul\Sales\Repositories\InvoiceRepository');

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $payment = $this['payment'];

        if (Cart::hasError() || ! $payment || ! Cart::savePaymentMethod($payment)) {
            return [
                'status'    => false,
                'message'   => trans('mobikul-api::app.api.checkout.place-order.error-save-payment'),
                'redirect'  => true
            ];
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        if ( Cart::hasError() ) {
            return [
                'status'    => false,
                'message'   => trans('mobikul-api::app.api.checkout.place-order.error-create-order'),
                'redirect'  => true
            ];
        }

        if ( request()->input('currency') && $cart->cart_currency_code != request()->input('currency') ) {
            $this->cartRepository->update(['cart_currency_code'   => request()->input('currency')], $cart->id);

            Shipping::collectRates();

            $shippingMethod = $cart->shipping_method;

            if (Cart::hasError() || !$shippingMethod || !Cart::saveShippingMethod($shippingMethod)) {
                return response()->json([
                    'status'    => false,
                    'message'   => trans('mobikul-api::app.api.checkout.review-and-payment.error-save-shipping'),
                    'redirect'  => true
                ]);
            }

            DB::table('cart_shipping_rates')
                ->where('id', $cart->selected_shipping_rate->id)
                ->update(['price' => $cart->selected_shipping_rate->price]);
        }

        Cart::collectTotals();

        $validate = mobikulApi()->validateOrder();

        if ( $validate['success'] == false ) {
            return $validate;
        }

        $cart = Cart::getCart();

        $result= [];

        if ($request->input('paymentMethod') == 'razorpay_mobile') {

            $details = $request->input('razorpay_details');

            if ($details != null) {
                $result = $this->authenticatePayment(json_decode($details, true));

                if ($result != null) {
                    return [
                        'success' => false,
                        'message' => $result,
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'message' => 'Somthing went wrong while processing payment!',
                ];
            }
        } else {
            if ($redirectUrl = Payment::getRedirectUrl($cart)) {
                return response()->json([
                    'success'      => true,
                    'redirect_url' => $redirectUrl,
                ], 200);
            }
        }

        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        $this->orderRepository->update(['status' => 'processing'], $order->id);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }

        Cart::deActivateCart();

        if ( $order->is_guest == 1 ) {
            $this->customerGroupId = $this->customerGroupRepository->findOneByField('code', 'guest')->id;
        } else {
            $customer = $this->customerRepository->findOneByField('email', $order->customer_email);

            if ( $customer ) {
                $this->customerGroupId = $customer->customer_group_id;
            }
        }

        if ( $order ) {
            $order->mobikul_order = 1;
            $order->save();
        }

        return [
            'success'           => true,
            'message'           => trans('mobikul-api::app.api.checkout.review-and-payment.success-payment'),
            'cartCount'         => 0,
            'email'             => $order->customer_email,
            'canReorder'        => true,
            'customerDetails'   => [
                'guestCustomer'     => $order->is_guest,
                'groupId'           => $this->customerGroupId,
                'firstname'         => $order->customer_first_name,
                'lastname'          => $order->customer_last_name,
                'email'             => $order->customer_email,
            ],
            'orderId'           => $order->id,
            'incrementId'       => $order->increment_id
        ];
    }

    /**
     * Prepares order's invoice data for creation.
     *
     * @return array
     */
    protected function prepareInvoiceData($order)
    {
        $invoiceData = ["order_id" => $order->id,];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
    
    public function authenticatePayment($details)
    {
        //include __DIR__ . '/../../../../../../vendor/wontonee/razorpay/src/razorpay-php/Razorpay.php';
       include base_path(). '/packages/Mesk/razorpay/src/razorpay-php/Razorpay.php';

        $api = new Api(core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_id'), core()->getConfigData('mobikul.mobikul.razorpay_mobile.merchant_secret'));

        try {
            $attributes = array(
                'razorpay_order_id'   => $details['razorpay_order_id'],
                'razorpay_payment_id' => $details['razorpay_payment_id'],
                'razorpay_signature'  => $details['razorpay_signature']
            );

            $api->utility->verifyPaymentSignature($attributes);

        } catch (SignatureVerificationError $e) {
            $this->authError =  'Razorpay Error : ' . $e->getMessage();
        }

        return $this->authError;
    }
}
