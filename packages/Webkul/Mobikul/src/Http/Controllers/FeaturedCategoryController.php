<?php

namespace Webkul\Mobikul\Http\Controllers;

use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Mobikul\Repositories\FeaturedCategoryRepository;
use Illuminate\Support\Facades\Event;

/**
 * FeaturedCategory controller
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class FeaturedCategoryController extends Controller
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
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * FeaturedCategoryRepository object
     *
     * @var \Webkul\Mobikul\Repositories\FeaturedCategoryRepository
     */
    protected $featuredCategoryRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository
     * @param  \Webkul\Core\Repositories\ChannelRepository  $channelRepository
     * @param  \Webkul\Product\Repositories\ProductRepository  $productRepository
     * @param  \Webkul\Mobikul\Repositories\FeaturedCategoryRepository  $featuredCategoryRepository
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ChannelRepository $channelRepository,
        ProductRepository $productRepository,
        FeaturedCategoryRepository $featuredCategoryRepository
    ) {
        $this->channelRepository = $channelRepository;

        $this->productRepository = $productRepository;

        $this->categoryRepository = $categoryRepository;

        $this->featuredCategoryRepository = $featuredCategoryRepository;

        $this->_config = request('_config');
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

        return view($this->_config['view'], compact('channels', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'image.*'       => 'mimes:jpeg,jpg,bmp,png',
            'sort_order'    => 'integer|required',
            'status'        => 'required',
            'category_id'   => 'integer|required',
            'channels'      => "required|array|min:1",
        ]);

        $data = collect(request()->all())->except('_token')->toArray();
        
        $featuredCategory = $this->featuredCategoryRepository->findOneByField('category_id', $data['category_id']);

        if ( isset($featuredCategory->id) ) {
            $category = $this->categoryRepository->findOrFail($featuredCategory->category_id);

            session()->flash('error', trans('mobikul::app.mobikul.category.error-already-created', [
                'category_name' => $category->name,
            ]));

            return redirect()->back();
        }

        $this->featuredCategoryRepository->create($data);

        session()->flash('success', trans('mobikul::app.mobikul.alert.create-success', ['name' => 'Featured Category']));

        return redirect()->route('mobikul.featured-category.index');
    }

    /**
     * Show the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = $this->featuredCategoryRepository->findOrFail($id);

        $categories = $this->categoryRepository->getCategoryTree(null, ['id']);

        $channels = $this->channelRepository->get();

        return view($this->_config['view'], compact('data', 'channels', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $this->validate(request(), [
            'image.*'       => 'mimes:jpeg,jpg,bmp,png',
            'sort_order'    => 'integer|required',
            'status'        => 'required',
            'category_id'   => 'integer|required',
            'channels'      => "required|array|min:1",
        ]);

        $data = collect(request()->all())->except('_token')->toArray();
        
        $featuredCategory = $this->featuredCategoryRepository->findOneByField('category_id', $data['category_id']);

        if ( isset($featuredCategory->id) && $featuredCategory->id != $data['id'] ) {
            $category = $this->categoryRepository->findOrFail($data['category_id']);

            session()->flash('error', trans('mobikul::app.mobikul.category.error-already-created', [
                'category_name' => $category->name,
            ]));

            return redirect()->back();
        }
        
        $this->featuredCategoryRepository->update($data, $data['id']);

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', [
            'name' => 'Featured Category'
            ]));

        return redirect()->route('mobikul.featured-category.index');
    }

    /**
     * Delete the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $featuredCategory = $this->featuredCategoryRepository->findOrFail($id);

        try {
            Event::dispatch('mobikul.featured-category.delete.before', $id);

            $featuredCategory->delete();

            Event::dispatch('mobikul.featured-category.delete.after', $id);

            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Featured Category']));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-failed', ['name' => 'Featured Category']));
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
        $suppressFlash = false;
        $categoryIds = explode(',', request()->input('indexes'));

        foreach ($categoryIds as $categoryId) {
            $featuredCategory = $this->featuredCategoryRepository->find($categoryId);
            
            if ( isset($featuredCategory) ) {
                try {
                    $suppressFlash = true;
                    Event::dispatch('mobikul.featured-category.delete.before', $categoryId);

                    $featuredCategory->delete();

                    Event::dispatch('mobikul.featured-category.delete.after', $categoryId);
                } catch (\Exception $e) {
                    session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Featured Category']));
                }
            }
        }

        if ( $suppressFlash == true ) {
            session()->flash('success', trans('mobikul::app.mobikul.alert.delete-success', ['name' => 'Featured Categories']));
        }

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Mass updates the featured categories
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (! isset($data['massaction-type'])) {
            return redirect()->back();
        }

        if (! $data['massaction-type'] == 'update') {
            return redirect()->back();
        }

        $categoryIds = explode(',', $data['indexes']);

        foreach ($categoryIds as $categoryId) {
            $featuredCategory = $this->featuredCategoryRepository->findOrFail($categoryId);

            $featuredCategory->update([
                'status' => $data['update-options']
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Featured Categories']));

        return redirect()->route($this->_config['redirect']);
    }
}