<?php

namespace Webkul\Mobikul\Http\Controllers;

use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Mobikul\Repositories\CarouselRepository;
use Webkul\Mobikul\Repositories\ImageProductCarouselRepository;

/**
 * Carousel controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CarouselController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * ChannelRepository object
     *
     * @var \Webkul\Core\Repositories\ChannelRepository
     */
    protected $channelRepository;

    /**
     * ImageProductCarouselRepository object
     *
     * @var \Webkul\Mobikul\Repositories\ImageProductCarouselRepository
     */
    protected $imageProductCarouselRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * CarouselRepository object
     *
     * @var \Webkul\Mobikul\Repositories\CarouselRepository
     */
    protected $carouselRepository;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;


    public function __construct(
        ChannelRepository $channelRepository,
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        CarouselRepository $carouselRepository,
        ImageProductCarouselRepository $imageProductCarouselRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->channelRepository = $channelRepository;

        $this->productRepository = $productRepository;

        $this->categoryRepository = $categoryRepository;

        $this->carouselRepository = $carouselRepository;

        $this->imageProductCarouselRepository = $imageProductCarouselRepository;

        static $value = 1;
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
        $categories = $this->categoryRepository->getCategoryTree(null, ['id']);

        $channels = $this->channelRepository->get();

        return view($this->_config['view'], compact('channels','categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title'         => 'required',
            'type'          => 'required',
            'sort_order'    => 'numeric|required',
            'channels'      => 'required',
            'status'        => 'required',
        ]);

        $data = collect(request()->all())->except('_token', 'color')->toArray();

        $this->carouselRepository->create($data);

        session()->flash('success', trans('mobikul::app.mobikul.alert.create-success', ['name' => 'Carousel']));

        return redirect()->route('mobikul.carousel.index');
    }

    /**
     * Show the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $carousel = $this->carouselRepository->findOrFail($id);

        $channels = $this->channelRepository->get();

        return view($this->_config['view'], compact('carousel', 'channels'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $this->validate(request(), [
            'status'        => 'required',
            'title'         => 'required',
            'type'          => 'required',
            'sort_order'    => 'numeric|required',
            'channels'      => 'required',
            'status'        => 'numeric|required',
        ]);

        $data = collect(request()->all())->except('_token','parent_id')->toArray();
        
        $this->carouselRepository->update($data, $data['carousel_id']);

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel']));

        return redirect()->route('mobikul.carousel.index');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $this->carouselRepository->delete($id);

            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Carousel']));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-failed', ['name' => 'Carousel']));
        }

        return response()->json(['message' => false], 400);
    }

     /**
     * To mass update the customer
     *
     * @return redirect
     */
    public function massUpdate()
    {
        $carouselIds = explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($carouselIds as $carouselId) {
            $carousel = $this->carouselRepository->find($carouselId);

            $carousel->update([
                'status' => $updateOption
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel']));

        return redirect()->back();
    }

    /**
     * Remove the specified resources from database
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $suppressFlash = false;

        if (request()->isMethod('post')) {
            $indexes = explode(',', request()->input('indexes'));

            foreach ($indexes as $key => $value) {
                $this->carouselRepository->find($value);

                try {
                    $suppressFlash = true;
                    $this->carouselRepository->delete($value);
                } catch (\Exception $e) {
                    report($e);

                    $suppressFlash = true;

                    continue;
                }
            }

            if ($suppressFlash) {
                session()->flash('success', trans('admin::app.datagrid.mass-ops.delete-success', ['resource' => 'Carousel']));
            } else {
                session()->flash('error', trans('admin::app.response.user-define-error', ['name' => 'Carousel']));
            }

            return redirect()->back();
        } else {
            session()->flash('error', trans('admin::app.datagrid.mass-ops.method-error'));

            return redirect()->back();
        }
    }

    /**
     * Assign the image type and product type carousel
     *
     * @return Response
     */
    public function assign($id)
    {
        $carousel = $this->carouselRepository->findOneWhere([
            'id' => $id
        ]);

        return view($this->_config['view'], compact('carousel'));
    }

    /**
     * assign carousel image
     *
     * @return Response
     */
    public function assignCarouselImages()
    {
        $url = explode('/', url()->previous());
        $id = end($url);
        $carouselImageIds =  explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($carouselImageIds as $carouselImageId) {
            $result = $this->imageProductCarouselRepository->findOneWhere([
                'carousel_id' => $id,
                'carousel_image_id' => $carouselImageId,
            ]);
            if ($updateOption == 1) {
                if ($result) {
                     $result->update([
                        'carousel_image_id' => $carouselImageId,
                    ]);
                } else {
                    $this->imageProductCarouselRepository->create([
                        'carousel_id' => $id,
                        'carousel_image_id' => $carouselImageId,
                    ]);
                }
            } else {
                if (! is_null($result)) {
                    $result->delete();
                }
            }
        }
        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel']));

        return redirect()->route('mobikul.carousel.index');
    }

    /**
     * asssign productIds
     */
    public function assignCarouselProducts()
    {
        $url = explode('/', url()->previous());
        $id = end($url);
        $carouselProductIds = explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($carouselProductIds as $carouselProductId) {
            $result = $this->imageProductCarouselRepository->findOneWhere([
                'carousel_id' => $id,
                'products_id' => $carouselProductId,
            ]);
            if ($updateOption == 1) {
                if ($result) {
                     $result->update([
                        'products_id' => $carouselProductId,
                    ]);
                } else {
                    $this->imageProductCarouselRepository->create([
                        'carousel_id' => $id,
                        'products_id' => $carouselProductId,
                    ]);
                }
            } else {
                if (! is_null($result)) {
                    $result->delete();
                }
            }
        }
        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel']));

        return redirect()->route('mobikul.carousel.index');
    }
}