<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

/**
 * CustomCollectionDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomCollectionDataGrid extends DataGrid
{
    protected $index = 'collection_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {   
        $queryBuilder = DB::table('mobikul_custom_collections as cc')
                ->addSelect('cc.id as collection_id', 'cc.name as collection_name', 'cc.status', 'cc.product_collection', 'cc.product_ids', DB::raw('1 as product_name'), 'cc.latest_count', 'cc.attributes', 'cc.price_from', 'cc.price_to', 'cc.brand', 'cc.sku');

        $this->addFilter('collection_id', 'cc.id');
        $this->addFilter('collection_name', 'cc.name');
        $this->addFilter('product_collection', 'cc.product_collection');
        $this->addFilter('latest_count', 'cc.latest_count');
        $this->addFilter('price_from', 'cc.price_from');
        $this->addFilter('price_to', 'cc.price_to');
        $this->addFilter('brand', 'cc.brand');
        $this->addFilter('sku', 'cc.sku');
        $this->addFilter('attributes', 'cc.attributes');
        $this->addFilter('status', 'cc.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'collection_id',
            'label'         => trans('mobikul::app.mobikul.custom-collection.id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'collection_name',
            'label'         => trans('mobikul::app.mobikul.custom-collection.collection-title'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'product_collection',
            'label'         => trans('mobikul::app.mobikul.custom-collection.product-collection'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                return ucwords(strtolower(str_replace("_", " ", "{$row->product_collection}")));
            }
        ]);

        $this->addColumn([
            'index'         => 'product_ids',
            'label'         =>  trans('mobikul::app.mobikul.custom-collection.product-ids'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->product_ids ) {
                    return implode(", ", json_decode($row->product_ids, true));
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'product_name',
            'label'         =>  trans('mobikul::app.mobikul.custom-collection.product-name'),
            'type'          => 'number',
            'searchable'    => false,
            'sortable'      => true,
            'filterable'    => false,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->product_ids ) {
                    return mobikulApi()->getProductName(json_decode($row->product_ids, true));
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'latest_count',
            'label'         => trans('mobikul::app.mobikul.custom-collection.latest-count'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->latest_count ) {
                    return $row->latest_count;
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'attributes',
            'label'         => trans('mobikul::app.mobikul.custom-collection.product-attributes'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->attributes ) {
                    return ucwords(strtolower(str_replace("_", " ", $row->attributes)));
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'price_from',
            'label'         => trans('mobikul::app.mobikul.custom-collection.price-from'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->price_from ) {
                    return $row->price_from;
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'price_to',
            'label'         => trans('mobikul::app.mobikul.custom-collection.price-to'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->price_to ) {
                    return $row->price_to;
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'brand',
            'label'         => trans('mobikul::app.mobikul.custom-collection.brand-name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->brand ) {
                    return mobikulApi()->getBrandOptionName($row->brand);
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'sku',
            'label'         => trans('mobikul::app.mobikul.custom-collection.sku'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->sku ) {
                    return $row->sku;
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('mobikul::app.mobikul.category.status'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ( $row->status == 1 )
                    return '<span class="badge badge-md badge-success">' . trans('mobikul::app.mobikul.notification.status.enabled') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('mobikul::app.mobikul.notification.status.disabled') . '</span>';
            }
        ]);
    }

    public function prepareActions()
    {
        // $this->addAction([
        //     'type'      => 'Edit',
        //     'title'     => trans('mobikul::app.mobikul.datagrid.edit'),
        //     'method'    => 'GET', //use post only for redirects only
        //     'route'     => 'mobikul.custom-collection.edit',
        //     'icon'      => 'icon pencil-lg-icon'
        // ]);

        $this->addAction([
            'method'    => 'POST', // use GET request only for redirect purposes
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'route'     => 'mobikul.custom-collection.delete',
            'icon'      => 'icon trash-icon',
        ]);
    }

    /**
     * Notification Mass Action To Delete And Change Their status
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'      => 'delete',
            'title'     => trans('mobikul::app.mobikul.category.delete'),
            'action'    => route('mobikul.custom-collection.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.category.update-status'),
            'action'    => route('mobikul.custom-collection.mass-update'),
            'method'    => 'POST',
            'options'   => [
                trans('mobikul::app.mobikul.category.enabled')   => 1,
                trans('mobikul::app.mobikul.category.disabled')  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
