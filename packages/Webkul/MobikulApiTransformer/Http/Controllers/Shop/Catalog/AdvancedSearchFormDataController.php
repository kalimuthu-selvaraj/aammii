<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\AdvancedSearchFormData as AdvancedSearchFormDataResource;

/**
 * HomePage controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AdvancedSearchFormDataController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

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

    public function index()
    {
        $data = NULL;

        return response()->json(new AdvancedSearchFormDataResource($data));
    }
}