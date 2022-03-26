<?php

namespace Webkul\Mobikul\Http\Controllers;

use Webkul\Mobikul\Repositories\CarouselImagesRepository;
use Webkul\Product\Repositories\ProductRepository;

/**
 * CarouselImage controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CarouselImageController extends Controller
{
    /**
     * Contains current guard
     *
     * @var array
     */
    protected $guard;

    /**
     * CarouselImagesRepository object
     *
     * @var \Webkul\Mobikul\Repositories\CarouselImagesRepository
     */
    protected $carouselImagesRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;


    public function __construct(
        CarouselImagesRepository $carouselImagesRepository,
        ProductRepository $productRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->carouselImagesRepository = $carouselImagesRepository;

        $this->productRepository = $productRepository;
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
        return view($this->_config['view']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'image.*'               => 'required|mimes:jpeg,jpg,bmp,png',
            'title'                 => 'required',
            'type'                  => 'required',
            'product_category_id'   => 'numeric|required',
            'status'                => 'numeric|required',
        ]);

        $data = collect(request()->all())->except('_token','parent_id')->toArray();

        if ( $data['type'] == 'product' ) {
            $product = $this->productRepository->findOrFail($data['product_category_id']);
                
            if (! isset($product->id) || !isset($product->url_key) || ( isset($product->parent_id) && $product->parent_id) ) {
                session()->flash('warning', trans('mobikul::app.mobikul.carousel.invalid-product'));

                return redirect()->back();
            }
        }
        
        $this->carouselImagesRepository->create($data);

        session()->flash('success', trans('mobikul::app.mobikul.alert.create-success', ['name' => 'Carousel Image']));

        return redirect()->route('mobikul.carousel.image.index');
      }


      /**
     * Show the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->carouselImagesRepository->findOneWhere([
            'id' => $id
        ]);

        return view($this->_config['view'], compact('data'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $this->validate(request(), [
            'title' => 'required',
            'type' => 'required',
            'product_category_id' => 'numeric|required',
            'status' => 'numeric|required',
        ]);

        $data = collect(request()->all())->except('_token','parent_id')->toArray();

        if ( $data['type'] == 'product' ) {
            $product = $this->productRepository->findOrFail($data['product_category_id']);
                
            if (! isset($product->id) || !isset($product->url_key) || ( isset($product->parent_id) && $product->parent_id) ) {
                session()->flash('warning', trans('mobikul::app.mobikul.carousel.invalid-product'));

                return redirect()->back();
            }
        }

        $previousData = $this->carouselImagesRepository->find($data['id']);

        if (isset($data['image']) && !empty ($data['image'])) {
            foreach ($data['image'] as $pic) {
                if ( !empty($pic)) {
                    $path = $pic->store('carousel/images');
                    $data['image'] = $path;
                    \Storage::delete($previousData['image']);
                } else {
                    $data['image'] = $previousData['image'];
                }
            }
        }

        $this->carouselImagesRepository->findOneWhere([
            'id' => $data['id']
        ])->update($data);

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel Image']));

        return redirect()->route('mobikul.carousel.image.index');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        try {
            $this->carouselImagesRepository->delete($id);

            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Carousel Image']));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-failed', ['name' => 'Carousel Image']));
        }

        return response()->json(['message' => false], 400);
    }

     /**
     * To mass update the category
     *
     * @return redirect
     */
    public function massUpdate()
    {
        $carouselIds = explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($carouselIds as $carouselId) {
            $category = $this->carouselImagesRepository->find($carouselId);

            $category->update([
                'status' => $updateOption
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Carousel Images']));

        return redirect()->back();
    }

    /**
     * To mass delete the customer
     *
     * @return redirect
     */
    public function massDestroy()
    {
        $carouselIds = explode(',', request()->input('indexes'));

        foreach ($carouselIds as $carouselId) {
            $this->carouselImagesRepository->deleteWhere([
                'id' => $carouselId
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Carousel Images']));

        return redirect()->back();
    }
}