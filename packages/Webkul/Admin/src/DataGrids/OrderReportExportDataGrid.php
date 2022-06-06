<?php

namespace Webkul\Admin\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Ui\DataGrid\Traits\ProvideDataGridPlus;
use Illuminate\Support\Str;

class OrderReportExportDataGrid extends DataGrid
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
			$url=$_SERVER["APP_URL"]."/admin/orderreport?";
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
            })->leftJoin('order_payment', function ($leftJoin) {
                $leftJoin->on('order_payment.order_id', '=', 'orders.id');
            })->leftJoin('order_items', function ($leftJoin) {
                $leftJoin->on('order_items.order_id', '=', 'orders.id');
            })
            ->addSelect('orders.id as ID', 'orders.created_at as DATE', 'orders.status as STATUS', 'channel_name as CHANNEL_NAME',DB::raw('CONCAT(' . DB::getTablePrefix() . 'customers.first_name, " ", ' . DB::getTablePrefix() . 'customers.last_name) as "CUSTOMER_NAME"'),'customers.phone as PHONE_NO','customers.email as EMAIL','order_address_billing.address1 as ADDRESS','order_address_billing.postcode as PINCODE',
'order_address_billing.city as CITY','order_address_billing.state as STATE','order_address_billing.country as COUNTRY',
'order_payment.method as PAYMENT','orders.grand_total as AMOUNT','orders.shipping_amount as SHIPPING_CHARGES','shipments.carrier_title as CARRIER_TITLE','shipments.track_number as TRACKING_NUMBER','order_items.name as PRODUCT_NAME',DB::raw(" '' as BARCODE"),DB::raw(" '' as HSNCODE"),'order_items.qty_ordered as QTY','order_items.price as ORDER_AMOUNT','order_items.total as Total','order_items.product_id as PRODUCT_ID');
            //->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as "BILLING_TO"'))
           // ->addSelect(DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name) as "SHIPPING_TO"'));
		if(!empty($where_data)){
			if(isset($where_data["start_date"]))
				$queryBuilder->whereRaw(DB::raw('orders.created_at >= "'.$where_data["start_date"].'"'));
			if(isset($where_data["end_date"]))
				$queryBuilder->whereRaw(DB::raw('orders.created_at < "'.$where_data["end_date"].'"'));
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

        $this->addColumn([
            'index'      => 'channel_name',
            'label'      => trans('admin::app.datagrid.channel-name'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
        ]);

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

        $this->addColumn([
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
        ]);
		
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

}
