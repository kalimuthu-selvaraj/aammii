<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Facades\Cart;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Mail\RegistrationEmail;
use Webkul\Customer\Mail\VerificationEmail;
use Webkul\Shop\Mail\SubscriptionEmail;

class CreateAccount extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains the current cart's item count.
     *
     * @var int
     */
    protected $cartItemCount = 0;

    /**
     * Contains the customer's status.
     *
     * @var boolean
     */
    protected $responseStatus = false;

    /**
     * Contains the customer create message.
     *
     * @var string
     */
    protected $responseMessage = '';

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->customerGroupRepository = app('Webkul\Customer\Repositories\CustomerGroupRepository');

        $this->customerRepository = app('Webkul\Customer\Repositories\CustomerRepository');

        $this->subscribersListRepository = app('Webkul\Core\Repositories\SubscribersListRepository');
        
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
        $this->channel = request()->input('storeId');
        $channel = $this->channelRepository->find($this->channel);

        $password = $this['password'];

        $data = [
            'first_name'        => $this['firstName'],
            'last_name'         => $this['lastName'],
            'api_token'         => Str::random(80),
            'token'             => md5(uniqid(rand(), true)),
            'email'             => $this['email'],
            'password'          => bcrypt($this['password']),
            'channel_id'        => $channel->id,
            'is_verified'       => core()->getConfigData('customer.settings.email.verification') ? 0 : 1,
            'customer_group_id' => $this->customerGroupRepository->findOneByField('code', 'general')->id,
            'subscribed_to_news_letter' => isset($this['is_subscribed']) ? 1 : 0,
        ];
        
        Event::dispatch('customer.registration.before');

        $customer = $this->customerRepository->create($data);

        if ( $customer ) {
            Event::dispatch('customer.registration.after', $customer);

            $this->getCartItemCount();

            if ( isset($this['is_subscribed']) ) {
                $subscription = $this->subscribersListRepository->findOneByField('email', $data['email']);

                if ( $subscription ) {
                    $this->subscribersListRepository->update([
                        'customer_id' => $customer->id,
                    ], $subscription->id);
                } else {
                    $this->subscribersListRepository->create([
                        'email'         => $data['email'],
                        'customer_id'   => $customer->id,
                        'channel_id'    => $channel->id,
                        'is_subscribed' => 1,
                        'token'         => $token = uniqid(),
                    ]);

                    try {
                        Mail::queue(new SubscriptionEmail([
                            'email' => $data['email'],
                            'token' => $token,
                        ]));
                    } catch (\Exception $e) { }
                }
            }
            
            $jwtToken = null;
            if ( ! $jwtToken = auth()->guard('api')->attempt([
                'email'     => $data['email'],
                'password'  => $password,
            ])) {
                $this->responseMessage   = trans('mobikul-api::app.api.customer.login.error-username-password');
            } else {
                $data['token'] = $customer->token = $jwtToken;
                $customer->save();
                
                $this->responseStatus   = true;
                $this->responseMessage  = trans('mobikul-api::app.api.customer.login.success-login');

                if (core()->getConfigData('customer.settings.email.verification')) {
                    try {
                        if (core()->getConfigData('emails.general.notifications.emails.general.notifications.verification')) {
                            Mail::queue(new VerificationEmail(['email' => $data['email'], 'token' => $data['token']]));
                        }
                    } catch (\Exception $e) {
                        $this->responseMessage  = trans('shop::app.customer.signup-form.success-verify-email-unsent');
                    }
                } else {
                    try {
                        if (core()->getConfigData('emails.general.notifications.emails.general.notifications.registration')) {
                            Mail::queue(new RegistrationEmail(request()->all(), 'customer'));
                        }
                    } catch (\Exception $e) {
                        $this->responseMessage  = trans('shop::app.customer.signup-form.success-verify-email-unsent');
                    }
                }
            }
        } else {
            $this->responseMessage = trans('mobikul-api::app.api.customer.login.error-create-account');
        }
        
        return [
            'success'           => $this->responseStatus,
            'message'           => $this->responseMessage,
            'customerName'      => $data['first_name'] . ' ' . $data['last_name'],
            'cartCount'         => $this->cartItemCount,
            'customerEmail'     => $data['email'],
            'token'             => $data['token'],
        ];
    }

    /**
     * Get the Item count of current cart
     *
     * @return void
     */
    public function getCartItemCount()
    {
        $cart = Cart::getCart();
        if ( $cart ) {
            $this->cartItemCount = count($cart->items);
        }
    }
}