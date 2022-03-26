<?php

namespace Webkul\PriceDropAlert\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Mail;
use Webkul\PriceDropAlert\Mail\SubscriptionEmail;
use Webkul\PriceDropAlert\Mail\UnSubscriptionEmail;
use Webkul\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class PriceDropSubscriptionRepository extends Repository
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Create a new repository instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Illuminate\Container\Container  $app
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        App $app
    )   {
        $this->productRepository = $productRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\PriceDropAlert\Contracts\PriceDropSubscriber';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Core\Contracts\Channel
     */
    public function create(array $data)
    {
        Event::dispatch('shop.price-drop.subscription.create.before');

        $product = $this->productRepository->find($data['product_id']);
        
        $data['base_price'] = $product->getTypeInstance()->getMinimalPrice();
        $data['status']     = 1;
        
        $subscriber = $this->model->create($data);

        if ( isset($subscriber->id) ) {
            $this->sendSubscribeEmail($subscriber, $product);
        }
        
        Event::dispatch('shop.price-drop.subscription.create.after', $subscriber);
        
        return $subscriber;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\PriceDropAlert\Contracts\EmailTemplate
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::dispatch('shop.price-drop.subscription.update.before', $id);
        
        $subscriber = $this->find($id);

        $subscriber->update($data);

        if ( isset($subscriber->id) ) {
            $product = $this->productRepository->find($subscriber->product_id);

            if ( $subscriber->status ) {
                $this->sendSubscribeEmail($subscriber, $product);
            } else {
                $this->sendUnSubscribeEmail($subscriber, $product);
            }
        }

        Event::dispatch('shop.price-drop.subscription.update.after', $subscriber);

        return $subscriber;
    }

    public function sendSubscribeEmail($subscriber, $product)
    {
        $mailSent = true;

        $mailData = [
            'subscriber_email'  => $subscriber->email,
            'product_name'      => $product->name,
            'product_price'     => core()->formatPrice($product->price, core()->getChannelBaseCurrencyCode()),
            'token'             => uniqid(),
        ];
        
        try {
            Mail::queue(new SubscriptionEmail($mailData));
        } catch (\Exception $e) {
            Log::error('Subscription Email : ' . $e->getMessage());
            $mailSent = false;
        }

        if ( $mailSent ) {
            $subscriber->token = $mailData['token'];
            $subscriber->save();
        }
    }

    public function sendUnSubscribeEmail($subscriber, $product)
    {
        $mailData = [
            'subscriber_email'  => $subscriber->email,
            'product_name'      => $product->name,
            'product_price'     => core()->formatPrice($product->price, core()->getChannelBaseCurrencyCode()),
        ];

        try {
            Mail::queue(new UnSubscriptionEmail($mailData));
        } catch (\Exception $e) {
            Log::error('Un-Subscription Email : ' . $e->getMessage());
        }
    }
}