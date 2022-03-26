<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

/**
 * ProductDataGrid Class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductDataGrid extends DataGrid
{
    protected $sortOrder = 'desc'; //asc or desc

    protected $index = 'product_id';

    protected $itemsPerPage = 20;

    public function prepareQueryBuilder()
    {
        /* locale */
        $locale = request()->get('locale') ?? app()->getLocale();

        /* channel */
        $channel = request()->get('channel') ?? (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

        $queryBuilder = DB::table('product_flat')
        ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
        ->leftJoin('attribute_families', 'products.attribute_family_id', '=', 'attribute_families.id')
        ->leftJoin('mobikul_carousel_images_products_pivot as ci_pivot', 'products.id', '=', 'ci_pivot.products_id')
        ->leftJoin('mobikul_carousel_translations as mc_t', function($leftJoin) use ($channel, $locale) {
            $leftJoin->on('ci_pivot.carousel_id', '=', 'mc_t.mobikul_carousel_id')
            ->where('mc_t.channel', $channel)
            ->where('mc_t.locale', $locale);
        })
        ->leftJoin('product_inventories', 'product_flat.product_id', '=', 'product_inventories.product_id')
        ->select('product_flat.product_id as product_id', 'product_flat.name as product_name', 'products.type as product_type', 'mc_t.title as carousel_title', 'ci_pivot.products_id as carousel_product_id', 'product_flat.status', 'product_flat.price', DB::raw('SUM(product_inventories.qty) as quantity'))
        ->where('product_flat.channel', $channel)
        ->where('product_flat.locale', $locale)
        ->whereNotNull('product_flat.url_key')
        ->groupBy('product_flat.product_id');

        $this->addFilter('product_id', 'product_flat.product_id');
        $this->addFilter('product_name', 'product_flat.name');
        $this->addFilter('product_sku', 'product_flat.sku');
        $this->addFilter('status', 'product_flat.status');
        $this->addFilter('product_type', 'products.type');
        $this->addFilter('carousel_title', 'mc_t.title');
        $this->addFilter('attribute_family', 'attribute_families.name');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'product_id',
            'label'         => trans('admin::app.datagrid.id'),
            'type'          => 'number',
            'searchable'    => false,
            'sortable'      => true,
            'filterable'    => true
        ]);

        $this->addColumn([
            'index'         => 'product_name',
            'label'         => trans('admin::app.datagrid.name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true
        ]);

        $this->addColumn([
            'index'         => 'product_type',
            'label'         => trans('admin::app.datagrid.type'),
            'type'          => 'string',
            'sortable'      => true,
            'searchable'    => true,
            'filterable'    => true
        ]);
        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('admin::app.datagrid.status'),
            'type'          => 'boolean',
            'sortable'      => true,
            'searchable'    => false,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ( $row->status == 1 )
                    return '<span class="badge badge-md badge-success">' . trans('mobikul::app.mobikul.notification.status.enabled') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('mobikul::app.mobikul.notification.status.disabled') . '</span>';
            }
        ]);

        $this->addColumn([
            'index'         => 'carousel_title',
            'label'         => trans('mobikul::app.mobikul.carousel.assigned-carousel'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ($row->carousel_product_id == $row->product_id)
                    return '<div class="badge badge-md badge-info" style="text-align: center;width: 100%;">' . $row->carousel_title . '</div>';
                else
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
            }
        ]);

        $this->addColumn([
            'index'         => 'carousel_product_id',
            'label'         => 'Assign',
            'type'          => 'boolean',
            'sortable'      => true,
            'searchable'    => false,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ($row->carousel_product_id == $row->product_id)
                    return '<span class="badge badge-md badge-success">' . trans('mobikul::app.mobikul.carousel.assigned') . '</span>';
                else
                    return '<span class="badge badge-md badge-warning">' . trans('mobikul::app.mobikul.carousel.unassigned') . '</span>';
            }
        ]);

        $this->addColumn([
            'index'         => 'quantity',
            'label'         => trans('admin::app.datagrid.qty'),
            'type'          => 'number',
            'sortable'      => true,
            'searchable'    => false,
            'filterable'    => false,
            'wrapper'       => function($value) {
                if ( is_null($value->quantity) )
                    return 0;
                else
                    return $value->quantity;
            }
        ]);
    }

    public function prepareMassActions() {
        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.datagrid.assign-products'),
            'action'    => route('mobikul.carousel.image.assigncarousel.product'),
            'method'    => 'POST',
            'options'   => [
                'Yes'       => 1,
                'No'        => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}