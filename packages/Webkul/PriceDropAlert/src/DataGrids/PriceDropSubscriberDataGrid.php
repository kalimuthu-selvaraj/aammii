<?php

namespace Webkul\PriceDropAlert\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

/**
 * PriceDropSubscriberDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PriceDropSubscriberDataGrid extends DataGrid
{
    protected $index = 'subscriber_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    public function __construct()
    {
        parent::__construct();

        $this->locale = request()->get('locale') ?: app()->getLocale();
        
        $this->channel = request()->get('channel') ?: core()->getDefaultChannelCode();
    }

    public function prepareQueryBuilder()
    {   
        $queryBuilder = DB::table('price_drop_subscribers as pds')
                ->leftJoin('product_flat as pf', 'pf.product_id', '=', 'pds.product_id')
                ->addSelect(
                    'pds.id as subscriber_id',
                    'pds.email',
                    'pf.name',
                    'pf.price',
                    'pds.status',
                    'pds.created_at',
                    'pds.updated_at'
                )
                ->where('pf.locale', $this->locale)
                ->where('pf.channel', $this->channel);

        $this->addFilter('subscriber_id', 'pds.id');
        $this->addFilter('email', 'pds.email');
        $this->addFilter('name', 'pf.name');
        $this->addFilter('price', 'pf.price');
        $this->addFilter('status', 'pds.status');
        $this->addFilter('created_at', 'pds.created_at');
        $this->addFilter('updated_at', 'pds.updated_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'subscriber_id',
            'label'         => trans('price_drop::app.admin.price-drop-alert.subscriber-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'email',
            'label'         => trans('price_drop::app.admin.price-drop-alert.email'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);  

        $this->addColumn([
            'index'         => 'name',
            'label'         => trans('price_drop::app.admin.price-drop-alert.name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);  

        $this->addColumn([
            'index'         => 'price',
            'label'         => trans('price_drop::app.admin.price-drop-alert.price'),
            'type'          => 'decimal',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'created_at',
            'label'         => trans('price_drop::app.admin.price-drop-alert.created-at'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'updated_at',
            'label'         => trans('price_drop::app.admin.price-drop-alert.updated-at'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('price_drop::app.admin.price-drop-alert.status'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ( $row->status == 1 )
                    return '<span class="badge badge-md badge-success">' . trans('price_drop::app.admin.price-drop-alert.subscribed') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('price_drop::app.admin.price-drop-alert.un-subscribed') . '</span>';
            }
        ]);
    }
}
