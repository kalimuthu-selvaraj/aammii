<?php

namespace Webkul\PriceDropAlert\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;

/**
 * EmailTemplateDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class EmailTemplateDataGrid extends DataGrid
{
    protected $index = 'template_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    protected $locale = 'all';

    public function __construct()
    {
        parent::__construct();

        $this->locale = request()->get('locale') ?: app()->getLocale();
    }

    public function prepareQueryBuilder()
    {   
        $queryBuilder = DB::table('email_template_translations as ett')
                ->leftJoin('email_templates as et', 'ett.email_template_id', '=', 'et.id')
                ->addSelect(
                    'et.id as template_id',
                    'ett.name',
                    'ett.subject',
                    'ett.message',
                    'ett.locale',
                    'et.status',
                    'et.created_at',
                    'et.updated_at'
                );

        if ($this->locale !== 'all') {
            $queryBuilder->where('ett.locale', $this->locale);
        } else {
            $queryBuilder->whereNotNull('ett.name');
        }

        $queryBuilder->groupBy('et.id');

        $this->addFilter('template_id', 'et.id');
        $this->addFilter('name', 'ett.name');
        $this->addFilter('subject', 'ett.subject');
        $this->addFilter('status', 'et.status');
        $this->addFilter('created_at', 'et.created_at');
        $this->addFilter('updated_at', 'et.updated_at');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'template_id',
            'label'         => trans('price_drop::app.admin.email-template.template-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'name',
            'label'         => trans('price_drop::app.admin.email-template.name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'subject',
            'label'         => trans('price_drop::app.admin.email-template.subject'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'message',
            'label'         => trans('price_drop::app.admin.email-template.message'),
            'type'          => 'string',
            'searchable'    => false,
            'sortable'      => true,
            'filterable'    => false,
            'closure'       => true,
            'wrapper' => function($row) {
                return $row->message;
            }
        ]);
        

        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('price_drop::app.admin.email-template.status'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ( $row->status == 1 )
                    return '<span class="badge badge-md badge-success">' . trans('price_drop::app.admin.email-template.enabled') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('price_drop::app.admin.email-template.disabled') . '</span>';
            }
        ]);

        $this->addColumn([
            'index'         => 'created_at',
            'label'         => trans('price_drop::app.admin.email-template.created-at'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'updated_at',
            'label'         => trans('price_drop::app.admin.email-template.updated-at'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'      => 'Edit',
            'title'     => trans('mobikul::app.mobikul.datagrid.edit'),
            'method'    => 'GET', //use post only for redirects only
            'route'     => 'admin.price-alert.email-template.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'method'    => 'POST', // use GET request only for redirect purposes
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'route'     => 'admin.price-alert.email-template.delete',
            'icon'      => 'icon trash-icon',
        ]);
    }
}
