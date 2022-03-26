<?php

namespace Webkul\Mobikul\Http\Controllers;

use Illuminate\Support\Facades\Event;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Category\Models\CategoryTranslation;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Mobikul\Repositories\CustomCollectionRepository;

class CustomCollectionController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Category\Repositories\CategoryRepository  $categoryRepository
     * @param  \Webkul\Attribute\Repositories\AttributeRepository  $attributeRepository
     * @param  \Webkul\Mobikul\Repositories\CustomCollectionRepository  $customCollectionRepository
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        AttributeRepository $attributeRepository,
        CustomCollectionRepository $customCollectionRepository
    )
    {
        $this->categoryRepository = $categoryRepository;

        $this->attributeRepository = $attributeRepository;

        $this->customCollectionRepository = $customCollectionRepository;

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $attributes = $this->attributeRepository->findWhere(['is_filterable' =>  1]);

        return view($this->_config['view'], compact('attributes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'                  => 'required|string',
            'status'                => 'required|integer',
            'product_collection'    => 'required|string',
        ]);

        $data = request()->all();

        if ( $data['product_collection'] == 'product_ids' ) {
            if (! isset($data['product_ids']) || (isset($data['product_ids']) && !$data['product_ids']) ) {
                session()->flash('error', trans('mobikul::app.mobikul.custom-collection.error-product-ids'));

                return redirect()->back();
            }
        }

        if ( $data['product_collection'] == 'product_attributes' && (isset($data['attributes']) && $data['attributes'] == 'price') ) {
            if ( $data['price_from'] > $data['price_to']) {
                session()->flash('error', trans('mobikul::app.mobikul.custom-collection.error-price-change'));

                return redirect()->back();
            }
        }

        $collection = $this->customCollectionRepository->create(request()->all());

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Custom Collection']));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $collection = $this->customCollectionRepository->findOrFail($id);

        try {
            Event::dispatch('mobikul.custom-collection.delete.before', $id);

            $this->customCollectionRepository->delete($id);

            Event::dispatch('mobikul.custom-collection.delete.after', $id);

            session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Custom Collection']));

            return response()->json(['message' => true], 200);
        } catch(\Exception $e) {
            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Custom Collection']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Remove the specified resources from database
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $suppressFlash = false;

        if (request()->isMethod('delete') || request()->isMethod('post')) {
            $indexes = explode(',', request()->input('indexes'));

            foreach ($indexes as $key => $value) {
                try {
                    Event::dispatch('mobikul.custom-collection.delete.before', $value);

                    $this->customCollectionRepository->delete($value);

                    Event::dispatch('mobikul.custom-collection.delete.after', $value);
                } catch(\Exception $e) {
                    $suppressFlash = true;

                    continue;
                }
            }

            if (! $suppressFlash) {
                session()->flash('success', trans('admin::app.datagrid.mass-ops.delete-success'));
            } else {
                session()->flash('info', trans('admin::app.datagrid.mass-ops.partial-action', ['resource' => 'Custom Collection']));
            }

            return redirect()->back();
        } else {
            session()->flash('error', trans('admin::app.datagrid.mass-ops.method-error'));

            return redirect()->back();
        }
    }

     /**
     * To mass update the customer
     *
     * @return redirect
     */
    public function massUpdate()
    {
        $collectionIds = explode(',', request()->input('indexes'));
        $updateOption = request()->input('update-options');

        foreach ($collectionIds as $collectionId) {
            $collection = $this->customCollectionRepository->find($collectionId);

            $collection->update([
                'status' => $updateOption
            ]);
        }

        session()->flash('success', trans('mobikul::app.mobikul.alert.update-success', ['name' => 'Custom Collection']));

        return redirect()->back();
    }

    /**
     * Result of search attributes.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function brandSearch()
    {
        if (request()->ajax()) {
            $results = [];
            
            foreach (mobikulApi()->searchBrandAttributes(request()->input('query')) as $row) {
                $results[] = [
                    'id'    => $row->attribute_option_id,
                    'code'  => $row->code,
                    'name'  => $row->label,
                ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view']);
        }
    }

    /**
     * Result of custom collection.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function collectionSearch()
    {
        if (request()->ajax()) {
            $results = [];
            
            foreach (mobikulApi()->searchCollections(request()->input('query')) as $row) {
                $results[] = [
                    'id'    => $row->id,
                    'name'  => $row->name,
                ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view']);
        }
    }
}