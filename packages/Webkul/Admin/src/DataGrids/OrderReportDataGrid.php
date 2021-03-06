<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Ui\DataGrid\Traits\ProvideDataGridPlus;
use Illuminate\Support\Str;

class OrderReportDataGrid extends DataGrid
{
    use ProvideDataGridPlus;

    /**
     * Index.
     *
     * @var string
     */
    protected $index = 'id';

    /**
     * Sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
		
    public function prepareQueryBuilder()
    {
		
		if(Str::contains($_SERVER["HTTP_REFERER"],"start_date") || Str::contains($_SERVER["HTTP_REFERER"],"end_date") || Str::contains($_SERVER["HTTP_REFERER"],"status" )){	
			$url=$_SERVER["APP_URL"].parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)."?";
			$find_filter=str_replace($url,"",$_SERVER["HTTP_REFERER"]);
			$where_data = array();
			parse_str( $find_filter, $where_data);
		}
        $queryBuilder = DB::table('orders')
		->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
			$leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
				->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
		})
		->leftJoin('addresses as order_address_billing', function ($leftJoin) {
			$leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
				->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
		})
		->leftJoin('customers', function ($leftJoin) {
			$leftJoin->on('customers.id', '=', 'orders.customer_id');
		})->leftJoin('shipments', function ($leftJoin) {
			$leftJoin->on('shipments.order_id', '=', 'orders.id');
		})
		->addSelect('orders.id', 'orders.increment_id', 'orders.base_sub_total', 'orders.base_grand_total', 'orders.created_at', 'channel_name', 'orders.status','customers.phone','shipments.carrier_title','shipments.track_number')
		->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as billed_to'))
		->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name) as shipped_to'))
		->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'orders.customer_first_name, " ", ' . DB::getTablePrefix() . 'orders.customer_last_name) as customer_name'));
 		if(!empty($where_data)){
			if(isset($where_data["start_date"]))
				$queryBuilder->whereRaw(DB::raw('orders.created_at >= "'.date("Y-m-d H:i:s",strtotime($where_data["start_date"])).'"'));
			if(isset($where_data["end_date"]))
				$queryBuilder->whereRaw(DB::raw('orders.created_at <= "'.date("Y-m-d 23:59:59",strtotime($where_data["end_date"])).'"'));
			if(isset($where_data["status"]) && $where_data["status"]!='')
				$queryBuilder->whereRaw(DB::raw('orders.status = "'.$where_data["status"].'" '));
  		}
        $this->addFilter('billed_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name)'));
        $this->addFilter('shipped_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name)'));
        $this->addFilter('increment_id', 'orders.increment_id');
        $this->addFilter('created_at', 'orders.created_at');

        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index'      => 'increment_id',
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_sub_total',
            'label'      => trans('admin::app.datagrid.sub-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('admin::app.datagrid.grand-total'),
            'type'       => 'price',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);
		
		$this->addColumn([
            'index'      => 'customer_name',
            'label'      => trans('admin::app.datagrid.customer-name'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

		$this->addColumn([
            'index'      => 'phone',
            'label'      => trans('admin::app.datagrid.phone'),
            'type'       => 'phone',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.order-date'),
            'type'       => 'datetime',
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
        ]);

        /*$this->addColumn([
            'index'      => 'channel_name',
            'label'      => trans('admin::app.datagrid.channel-name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);*/

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('admin::app.datagrid.status'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => function ($value) {
                if ($value->status == 'processing') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-processing') . '</span>';
                } elseif ($value->status == 'completed') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-success') . '</span>';
                } elseif ($value->status == "canceled") {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-canceled') . '</span>';
                } elseif ($value->status == "closed") {
                    return '<span class="badge badge-md badge-info">' . trans('admin::app.sales.orders.order-status-closed') . '</span>';
                } elseif ($value->status == "pending") {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending') . '</span>';
                } elseif ($value->status == "pending_payment") {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending-payment') . '</span>';
                } elseif ($value->status == "fraud") {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-fraud') . '</span>';
                }
            },
        ]);

        /* $this->addColumn([
            'index'      => 'billed_to',
            'label'      => trans('admin::app.datagrid.billed-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'shipped_to',
            'label'      => trans('admin::app.datagrid.shipped-to'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]); */
		
		$this->addColumn([
            'index'      => 'carrier_title',
            'label'      => trans('admin::app.datagrid.carrier-title'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
		
		$this->addColumn([
            'index'      => 'track_number',
            'label'      => trans('admin::app.datagrid.tracking-number'),
            'type'       => 'number',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
		
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
		
        $this->addAction([
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.sales.orders.view',
            'icon'   => 'icon eye-icon',
        ]);
		
		$this->addAction([
            'title'  => trans('admin::app.datagrid.invoice_print'),
            'method' => 'GET',
            'route'  => 'admin.sales.invoices.orderprint',
            'icon'   => 'icon print-icon',
        ]);
    }
}
