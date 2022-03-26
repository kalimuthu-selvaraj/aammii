<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * CarouselImagesDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CarouselImagesDataGrid extends DataGrid
{
    protected $index = 'carousel_image_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('mobikul_carousel_images as ci')
                ->addSelect('ci.id as carousel_image_id', 'ci.image', 'p_flat.name as product_name', 'ct.name as category_name', 'ci.title', 'ci.type', 'ci.product_category_id', 'ci.status')
                ->leftJoin('product_flat as p_flat', function($leftJoin) {
                    $leftJoin->on('ci.product_category_id', '=', 'p_flat.product_id')
                             ->where('ci.type', 'product')
                             ->where('p_flat.locale', app()->getLocale());
                })
                ->leftJoin('category_translations as ct', function($leftJoin) {
                    $leftJoin->on('ci.product_category_id', '=', 'ct.category_id')
                        ->where('ci.type', 'category')
                        ->where('ct.locale', app()->getLocale());
                });
        
        $this->addFilter('carousel_image_id', 'ci.id');
        $this->addFilter('title', 'ci.title');
        $this->addFilter('product_name', 'p_flat.name');
        $this->addFilter('category_name', 'ct.name');
        $this->addFilter('type', 'ci.type');
        $this->addFilter('sort_order', 'ci.sort_order');
        $this->addFilter('status', 'ci.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'carousel_image_id',
            'label'         => trans('mobikul::app.mobikul.carousel.carousel-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'image',
            'label'         => trans('mobikul::app.mobikul.carousel.image'),
            'type'          => 'html',
            'searchable'    => false,
            'sortable'      => false,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->image )
                    return '<img src=' . Storage::url($row->image) . ' class="img-thumbnail" width="100px" height="70px" />';

            }
        ]);

        $this->addColumn([
            'index'         => 'title',
            'label'         => trans('mobikul::app.mobikul.carousel.title'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'type',
            'label'         => trans('mobikul::app.mobikul.carousel.banner-type'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                return ucwords(strtolower(str_replace("_", " ", "{$row->type}")));
            }
        ]);

        $this->addColumn([
            'index'         => 'product_name',
            'label'         => trans('mobikul::app.mobikul.carousel.product-name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( isset($row->product_name) && $row->product_name ) {
                    return $row->product_name;
                } else {
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'category_name',
            'label'         => trans('mobikul::app.mobikul.carousel.category-name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( isset($row->category_name) && $row->category_name ) {
                    return $row->category_name;
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
        $this->addAction([
            'type'      => 'Edit',
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'method'    => 'GET', //use post only for redirects only
            'route'     => 'mobikul.carousel.image.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'method'    => 'POST', // use GET request only for redirect purposes
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'route'     => 'mobikul.carousel.image.delete',
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
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'action'    => route('mobikul.carousel.image.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.datagrid.update'),
            'action'    => route('mobikul.carousel.image.mass-update'),
            'method'    => 'POST',
            'options'   => [
                'Enabled'   => 1,
                'Disabled'  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
