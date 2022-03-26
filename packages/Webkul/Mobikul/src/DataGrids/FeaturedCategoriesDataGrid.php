<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * FeaturedCategoriesDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class FeaturedCategoriesDataGrid extends DataGrid
{
    protected $index = 'featured_category_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('mobikul_featured_categories as f_cat')
                ->leftJoin('categories as cat', 'f_cat.category_id', '=', 'cat.id')
                ->addSelect('f_cat.id as featured_category_id', 'cat.image as banner_image', 'ct.name as category_name', 'f_cat.image as featured_icon', 'f_cat.sort_order', 'f_cat.id as category_id', 'fcc.channel_id', 'ch_t.name as channel_name', 'f_cat.status')
                ->leftJoin('mobikul_featured_category_channels as fcc', function($leftJoin) {
                    $leftJoin->on('f_cat.id', '=', 'fcc.featured_category_id')->leftJoin('channel_translations as ch_t', 'fcc.channel_id', '=', 'ch_t.channel_id');
                })
                ->leftJoin('category_translations as ct', function($leftJoin) {
                    $leftJoin->on('cat.id', '=', 'ct.category_id')
                        ->where('ct.locale', app()->getLocale());
                })
                ->where('ch_t.locale', app()->getLocale())
                ->groupBy('f_cat.id');
    
        $this->addFilter('featured_category_id', 'f_cat.id');
        $this->addFilter('category_name', 'ct.name');
        $this->addFilter('channel_name', 'ch_t.name');
        $this->addFilter('sort_order', 'f_cat.sort_order');
        $this->addFilter('status', 'f_cat.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'featured_category_id',
            'label'         => trans('mobikul::app.mobikul.category.featured-category-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'banner_image',
            'label'         => trans('mobikul::app.mobikul.category.banner-image'),
            'type'          => 'html',
            'searchable'    => false,
            'sortable'      => false,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->banner_image )
                    return '<img src=' . Storage::url($row->banner_image) . ' class="img-thumbnail" width="100px" height="70px" />';

            }
        ]);

        $this->addColumn([
            'index'         => 'category_name',
            'label'         => trans('mobikul::app.mobikul.category.category-name'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'featured_icon',
            'label'         => trans('mobikul::app.mobikul.category.featured-icon'),
            'type'          => 'html',
            'searchable'    => false,
            'sortable'      => false,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->featured_icon )
                    return '<img src=' . Storage::url($row->featured_icon) . ' class="img-thumbnail" width="70px" height="70px" />';

            }
        ]);

        $this->addColumn([
            'index'         => 'sort_order',
            'label'         =>  trans('mobikul::app.mobikul.category.sort-order'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'channel_name',
            'label'         =>  trans('mobikul::app.mobikul.notification.store-view'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                $featuredCategoryChannels = app('Webkul\Mobikul\Repositories\FeaturedCategoryChannelRepository')->findWhere(['featured_category_id' => $row->featured_category_id]);

                foreach ($featuredCategoryChannels as $categoryChannel) {
                    $channel = app('Webkul\Core\Repositories\ChannelRepository')->find($categoryChannel->channel_id);
                    if ( $channel ) {
                        echo $channel['name'] . '</br>' . PHP_EOL;
                    } 
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('mobikul::app.mobikul.category.status'),
            'type'          => 'string',
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
            'title'     => trans('mobikul::app.mobikul.datagrid.edit'),
            'method'    => 'GET', //use post only for redirects only
            'route'     => 'mobikul.featured-category.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'method'    => 'POST', // use GET request only for redirect purposes
            'route'     => 'mobikul.featured-category.delete',
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
            'action'    => route('mobikul.featured-category.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.category.update-status'),
            'action'    => route('mobikul.featured-category.mass-update'),
            'method'    => 'POST',
            'options'   => [
                trans('mobikul::app.mobikul.category.enabled')   => 1,
                trans('mobikul::app.mobikul.category.disabled')  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
