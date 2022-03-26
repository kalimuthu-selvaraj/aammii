<?php

namespace Webkul\PriceDropAlert\Http\Controllers\Shop;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository;
use Illuminate\Support\Facades\Log;

class SubscriptionController extends Controller
{
    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;
    
    /**
     * PriceDropSubscriptionRepository object
     *
     * @var \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository
     */
    protected $priceDropSubscriptionRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\PriceDropAlert\Repositories\PriceDropSubscriptionRepository  $priceDropSubscriptionRepository
     * @return void
     */
    public function __construct(
        ProductRepository $productRepository,
        PriceDropSubscriptionRepository $priceDropSubscriptionRepository
    )   {
        $this->productRepository = $productRepository;
        
        $this->priceDropSubscriptionRepository = $priceDropSubscriptionRepository;

        parent::__construct();
    }

    /**
     * Method to populate the cart page which will be populated before the checkout process.
     *
     * @return \Illuminate\View\View
     */
    public function subscribe()
    {
        $this->validate(request(), [
            'product_id'    => 'required|integer',
            'email'         => 'required|email',
        ]);

        $data = request()->all();

        $product = $this->productRepository->find($data['product_id']);

        $priceDropSubscriber = $this->priceDropSubscriptionRepository->findOneWhere([
            'product_id'    => $data['product_id'],
            'email'         => $data['email']
        ]);

        if ( isset($priceDropSubscriber->id)) {
            if ( $priceDropSubscriber->status ) {
                $data['status'] = 0;
            } else {
                $data['status'] = 1;
            }
            
            $priceDropSubscriber = $this->priceDropSubscriptionRepository->update($data, $priceDropSubscriber->id);

            if ( $priceDropSubscriber->status ) {
                session()->flash('success', trans('price_drop::app.shop.price-drop-alert.subscribe-success'));
            } else {
                session()->flash('success', trans('price_drop::app.shop.price-drop-alert.un-subscribe-success'));
            }
        } else {
            $priceDropSubscriber = $this->priceDropSubscriptionRepository->create($data);

            session()->flash('success', trans('price_drop::app.shop.price-drop-alert.subscribe-success'));
        }

        return redirect()->route($this->_config['redirect'], $product->url_key);
    }

    public function unsubscribe($token)
    {
        $priceDropSubscriber = $this->priceDropSubscriptionRepository->findOneWhere([
            'token' => $token,
        ]);

        $priceDropSubscriber->update(['status' => 0]);
        
        $product = $this->productRepository->find($priceDropSubscriber->product_id);

        session()->flash('success', trans('price_drop::app.shop.price-drop-alert.un-subscribe-success'));

        return redirect()->route($this->_config['redirect'], $product->url_key);
    }
}
