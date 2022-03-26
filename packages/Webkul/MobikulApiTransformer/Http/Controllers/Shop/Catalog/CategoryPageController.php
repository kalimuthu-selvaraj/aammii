<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Catalog;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Catalog\CategoryPage;

/**
 * CategoryPage controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CategoryPageController extends Controller
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

    /**
     * Contains current locale
     *
     * @var string
     */
    protected $localeCode;

    /**
     * CategoryRepository
     *
     * @var \Webkul\Category\Repositories\CategoryRepository $categoryRepository
     */
    protected $categoryRepository;

    /**
     * Contains list of category.
     *
     * @var array
     */
    protected $categoryList;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $data = request()->all();
        $token = request()->token;
        if ( $token ) {
            $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
            
            if ( $authentication === true ) {
                $data['customer_id'] = auth()->guard($this->guard)->user()->id;
            } else {
                return $authentication;
            }
        }

        return response()->json(new CategoryPage($data));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function categoryList(): ?JsonResponse
    {
        $this->localeCode = request()->input('locale');

        foreach($this->categoryRepository->get() as $category) {
            $categoryTranslation = $category->translations->where('locale', $this->localeCode)->first();

            if ( $categoryTranslation ) {
                $this->categoryList[] = [
                    'id'            => $category->id,
                    'name'          => $categoryTranslation->name,
                    'img_url'       => $category->image_url,
                    'dominantColor' => $category->image_url ? mobikulApi()->getImageDominantColor($category->image_url) : '',
                ];
            } 
        }
        
        return response()->json([
            'success'       => true,
            'message'       => "",
            'categoryData'  => $this->categoryList,
            'eTag'          => 'adf1f528f1a114398f8614d1df9c2da5', //need discussion
        ]);
    }
}