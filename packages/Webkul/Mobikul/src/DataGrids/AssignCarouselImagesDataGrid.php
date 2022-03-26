<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * AssignCarouselImagesDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class AssignCarouselImagesDataGrid extends DataGrid
{
    protected $index = 'ci_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    public function prepareQueryBuilder()
    {
        /* locale */
        $locale = request()->get('locale') ?? app()->getLocale();

        /* channel */
        $channel = request()->get('channel') ?? (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

        $queryBuilder = DB::table('mobikul_carousel_images as c_img')
                ->addSelect('c_img.id as ci_id', 'c_img.image', 'mc_t.title as carousel_title', 'c_img.title', 'ci_pivot.carousel_image_id as image_id', 'c_img.type', 'product_category_id', 'c_img.status')
                ->leftJoin('mobikul_carousel_images_products_pivot as ci_pivot', 'c_img.id', '=', 'ci_pivot.carousel_image_id')
                ->leftJoin('mobikul_carousel_translations as mc_t', function($leftJoin) use ($channel, $locale) {
                    $leftJoin->on('ci_pivot.carousel_id', '=', 'mc_t.mobikul_carousel_id')
                    ->where('mc_t.channel', $channel)
                    ->where('mc_t.locale', $locale);
                });
                
            $queryBuilder->groupBy('c_img.id');
                
            $this->addFilter('ci_id', 'c_img.id');
            $this->addFilter('title', 'c_img.title');
            $this->addFilter('carousel_title', 'mc_t.title');
            $this->addFilter('type', 'c_img.type');
            $this->addFilter('status', 'c_img.status');
            
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'ci_id',
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
            'index'         => 'carousel_title',
            'label'         => trans('mobikul::app.mobikul.carousel.assigned-carousel'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ($row->image_id == $row->ci_id)
                    return '<div class="badge badge-md badge-info" style="text-align: center;width: 100%;">' . $row->carousel_title . '</div>';
                else
                    return '<div style="text-align: center;width: 100%;font-size: 20px;"> - </div>';
            }
        ]);

        $this->addColumn([
            'index'         => 'image_id',
            'label'         => trans('mobikul::app.mobikul.carousel.assign-status'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper' => function($row) {
                if ($row->image_id == $row->ci_id)
                    return '<span class="badge badge-md badge-success">' . trans('mobikul::app.mobikul.carousel.assigned') . '</span>';
                else
                    return '<span class="badge badge-md badge-warning">' . trans('mobikul::app.mobikul.carousel.unassigned') . '</span>';
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

    /**
     * Notification Mass Action To Delete And Change Their status
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.datagrid.assign-images'),
            'action'    => route('mobikul.carousel.image.assigncarousel.image'),
            'method'    => 'POST',
            'options'   => [
                'Yes'       => 1,
                'No'        => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
