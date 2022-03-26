<?php

namespace Webkul\Mobikul\Http\Controllers;

use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Mobikul\Repositories\NotificationsRepository;
use Webkul\Mobikul\Repositories\BannerImageRepository;
use Webkul\Mobikul\Helpers\SendNotification;
use Webkul\Product\Repositories\ProductRepository;

/**
 * BannerImages Controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class BannerImagesController extends Controller
{
    /**
     * Contains route related configuration.
     *
     * @var array
     */
    protected $_config;

    /**
     * ChannelRepository object
     *
     * @var \Webkul\Core\Repositories\ChannelRepository
     */
    protected $channelRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * NotificationsRepository object
     *
     * @var \Webkul\Mobikul\Repositories\NotificationsRepository
     */
    protected $notificationsRepository;

    /**
     *  BannerImageRepository object
     *
     * @var \Webkul\Mobikul\Repositories\BannerImageRepository
     */
    protected $bannerImageRepository
    ;

    /**
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SendNotification object
     *
     * @var \Webkul\Mobikul\Helpers\SendNotification
     */
    protected $sendNotification;

    public function __construct(
        ChannelRepository $channelRepository,
        NotificationsRepository $notificationsRepository,
        SendNotification $sendNotification,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        BannerImageRepository $bannerImageRepository
    )   {

        $this->middleware('admin');

        $this->_config = request('_config');

        $this->channelRepository = $channelRepository;

        $this->notificationsRepository = $notificationsRepository;

        $this->sendNotification = $sendNotification;

        $this->productRepository = $productRepository;

        $this->categoryRepository = $categoryRepository;

        $this->bannerImageRepository = $bannerImageRepository;
    }

    public function index()
    {
        return view($this->_config['view']);
    }

     /**
     * open the create page a newly created resource in storage.
     *
     * @return View
     */
    public function create()
    {
        $channels = $this->channelRepository->get();
        
        return view($this->_config['view'], compact('channels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'                  => 'required',
            'image.*'               => 'mimes:jpeg,jpg,bmp,png',
            'sort_order'            => 'integer|required',
            'type'                  => 'string|required',
            'product_category_id'   => 'integer|required',
            'channels'              => 'required|array',
            'status'                => 'integer|required'
        ]);
        
        $data = collect(request()->all())->except('_token')->toArray();

        if ( $data['type'] == 'product' ) {
            $product = $this->productRepository->findOrFail($data['product_category_id']);
                
            if (! isset($product->id) || !isset($product->url_key) || ( isset($product->parent_id) && $product->parent_id) ) {
                session()->flash('warning', trans('mobikul::app.mobikul.banner-image.invalid-product'));

                return redirect()->back();
            }
        }
        
        $this->bannerImageRepository->create($data);

        session()->flash('success', trans('mobikul::app.mobikul.alert.create-success', ['name' => 'Banner Image']));

        return redirect()->route('mobikul.banner-image.index');
    }

    /**
     * Show the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $bannerImage = $this->bannerImageRepository->findOrFail($id);

        $channels = $this->channelRepository->get();

        return view($this->_config['view'], compact('bannerImage', 'channels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'image.*'               => 'mimes:jpeg,jpg,bmp,png',
            'name'                  => 'required',
            'type'                  => 'string|required',
            'product_category_id'   => 'integer|required',
            'sort_order'            => 'integer|required',
            'status'                => 'integer|required',
            'channels'              => 'required'
        ]);

        $data = collect(request()->all())->except('_token')->toArray();

        if ( $data['type'] == 'product' ) {
            $product = $this->productRepository->findOrFail($data['product_category_id']);
                
            if (! isset($product->id) || !isset($product->url_key) || ( isset($product->parent_id) && $product->parent_id) ) {
                session()->flash('warning', trans('mobikul::app.mobikul.banner-image.invalid-product'));

                return redirect()->back();
            }
        }

        $this->bannerImageRepository->update($data, $id);

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Banner Images']));

        return redirect()->route('mobikul.banner-image.index');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $this->bannerImageRepository->delete($id);

            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Image']));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-failed', ['name' => 'Image']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Remove the specified resources from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $bannerIds = explode(',', request()->input('indexes'));

        foreach ($bannerIds as $bannerId) {
            $this->bannerImageRepository->deleteWhere([
                'id' => $bannerId
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Images']));

        return redirect()->back();
    }

    /**
     * Mass updates the featured categories
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $bannerIds = explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($bannerIds as $bannerId) {
            $notification = $this->bannerImageRepository->find($bannerId);

            $notification->update([
                'status' => $updateOption
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Images']));

        return redirect()->back();
    }
}