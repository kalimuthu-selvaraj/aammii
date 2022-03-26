<?php

namespace Webkul\MobikulApiTransformer\Http\Controllers\Shop\Extra;

use Webkul\MobikulApiTransformer\Http\Controllers\Shop\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Webkul\CMS\Repositories\CmsRepository;
use Webkul\MobikulApiTransformer\Http\Resources\Extra\RegisterDevice;
use Webkul\MobikulApiTransformer\Http\Resources\Extra\CustomCollection;
use Webkul\MobikulApiTransformer\Http\Resources\Extra\Notification;
use Webkul\MobikulApiTransformer\Http\Resources\Extra\SearchList;
use Webkul\MobikulApiTransformer\Http\Resources\Extra\SearchTermList;

/**
 * Mobikul Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MobikulController extends Controller
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
     * Contains cms page title.
     *
     * @var string
     */
    protected $cmsTitle = '';
    
    /**
     * Contains cms page url_key/slug.
     *
     * @var string
     */
    protected $urlKey = '';
    
    /**
     * Contains cms page content
     *
     * @var string
     */
    protected $cmsContent = '';
    
    /**
     * CmsRepository Object
     *
     * @var \Webkul\CMS\Repositories\CmsRepository
     */
    protected $cmsRepository;
    
    /**
     * Controller instance
     *
     * @param \Webkul\CMS\Repositories\CmsRepository    $cmsRepository
     * @return void
     */
    public function __construct(
        CmsRepository $cmsRepository
    )   {
        $this->guard = request()->has('token') ? 'api' : 'customer';

        auth()->setDefaultDriver($this->guard);

        $this->middleware('validateAPIHeader');

        $this->_config = request('_config');

        $this->cmsRepository = $cmsRepository;
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'     => 'required',
            'storeId'   => 'required|numeric',
            'id'        => 'required|numeric|min:0|not_in:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $data = request()->all();        
        $cmsData = $this->cmsRepository->find($data['id']);
        
        if ( ! empty($cmsData) ) {
            $cmsTranslation = $cmsData->translations->where('locale', request()->input('locale'))->first();
            
            if ( $cmsTranslation ) {
                $this->cmsTitle     = $cmsData->page_title;
                $this->urlKey       = $cmsData->url_key;
                $this->cmsContent   = $cmsData->html_content;
            }
        }
        
        return response()->json([
            'success'   => true,
            'message'   => '',
            'title'     => $this->cmsTitle,
            'url_key'   => $this->urlKey,
            'content'   => $this->cmsContent
        ], 200);
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function registerDevice(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'     => 'required',
            'fcmToken'  => 'required|string',
            'os'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true || $token == 0) {
            $loggedInCustomer = [];
            if ( auth($this->guard)->check() ) {
                $loggedInCustomer = auth($this->guard)->user();
            }

            return response()->json(new RegisterDevice($loggedInCustomer));
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }

        $token = request()->token;
        $authentication = mobikulApi()->customerAuthentication($token, $this->guard);
        if ( $authentication === true ) {
            if ( auth()->guard($this->guard)->check()) {
                auth()->guard($this->guard)->logout();

                return response()->json([
                    'success' => true,
                    'message' => trans('mobikul-api::app.api.extra.logout.success-logout'),
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => trans('mobikul-api::app.api.customer.address-info.error-login'),
                ], 200);
            }
        } else {
            return $authentication;
        }
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function collection(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'storeId'   => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        return response()->json(new CustomCollection(request()->all()));
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function notificationList(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(),[
            'token'     => 'required',
            'storeId'   => 'required|numeric|min:0|not_in:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        return response()->json(new Notification(request()->all()));
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(): ?JsonResponse
    {
        $validator = Validator::make(request()->all(), [
            'token'         => 'required',
            'storeId'       => 'required|numeric',
            'searchQuery'   => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'message'   => $validator->messages(),
            ], 200);
        }
        
        return response()->json(new SearchList([
            'data'  => request()->all()
        ]));
    }

    /**
     * Returns a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchTerm(): ?JsonResponse
    {
        return response()->json(new SearchTermList(request()->all()));
    }
}

