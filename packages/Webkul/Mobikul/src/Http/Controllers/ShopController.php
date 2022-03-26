<?php

namespace Webkul\Mobikul\Http\Controllers;

use Illuminate\Support\Facades\Event;

class ShopController extends Controller
{
    
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;
    
    public function __construct()
    {
        $this->_config = request('_config');
    }
    /**
     * Index to handle the view loaded with the search results
     *
     * @return \Illuminate\View\View
     */
    public function search()
    {
        Event::dispatch('shop.search.before');

        $results = app('Webkul\Velocity\Repositories\Product\ProductRepository')->searchProductsFromCategory(request()->all());
        
        Event::dispatch('shop.search.after', request()->all());

        return view($this->_config['view'])->with('results', $results ? $results : null);
    }
}