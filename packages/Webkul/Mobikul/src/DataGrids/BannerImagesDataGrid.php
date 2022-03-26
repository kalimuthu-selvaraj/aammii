<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Core\Models\Locale;
use Webkul\Core\Models\Channel;
use Webkul\Ui\DataGrid\DataGrid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * BannerImagesDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class BannerImagesDataGrid extends DataGrid
{
    protected $index = 'mobikul_banner_id'; // column that needs to be treated as index column

    protected $sortOrder = 'desc'; // asc or desc

    protected $itemsPerPage = 10;

    protected $locale = 'all';

    protected $channel = 'all';

    protected $whereInChannels = [];

    protected $whereInLocales = [];

    /** @var string[] contains the keys for which extra filters to render */
    protected $extraFilters = [
        'channels',
        'locales',
    ];

    public function __construct()
    {
        parent::__construct();

        /* locale */
        $this->locale = request()->get('locale') ?? app()->getLocale();

        /* channel */
        $this->channel = request()->get('channel') ?? (core()->getCurrentChannelCode() ?: core()->getDefaultChannelCode());

        /* finding channel code */
        if ($this->channel !== 'all') {
            $this->channel = Channel::query()->where('code', $this->channel)->first();
            $this->channel = $this->channel ? $this->channel->code : 'all';
        }
    }

    public function prepareQueryBuilder()
    {
        if ($this->channel === 'all') {
            $this->whereInChannels = Channel::query()->pluck('code')->toArray();
        } else {
            $this->whereInChannels = [$this->channel];
        }

        if ($this->locale === 'all') {
            $this->whereInLocales = Locale::query()->pluck('code')->toArray();
        } else {
            $this->whereInLocales = [$this->locale];
        }

        $queryBuilder = DB::table('mobikul_banner_translations as mb_t')
                ->leftJoin('mobikul_banners as mb', 'mb_t.mobikul_banner_id', '=', 'mb.id')
                ->leftJoin('channels as ch', 'mb_t.channel', '=', 'ch.code')
                ->leftJoin('channel_translations as ch_t', 'ch.id', '=', 'ch_t.channel_id')
                ->select(
                    'mb_t.name as banner_title',
                    'mb_t.channel',
                    'mb_t.locale',
                    'mb_t.mobikul_banner_id',
                    'mb.image',
                    'mb.sort_order',
                    'mb.type',
                    'mb.product_category_id',
                    'mb.status',
                    'mb.created_at',
                    'mb.updated_at',
                    'ch_t.name as channel_name'
                );

            $queryBuilder->groupBy('mb_t.mobikul_banner_id', 'mb_t.channel', 'mb_t.locale');
    
            $queryBuilder->whereIn('mb_t.locale', $this->whereInLocales);
            $queryBuilder->whereIn('mb_t.channel', $this->whereInChannels);

        $this->addFilter('mobikul_banner_id', 'mb_t.mobikul_banner_id');
        $this->addFilter('banner_title', 'mb_t.name');
        $this->addFilter('sort_order', 'mb.sort_order');
        $this->addFilter('type', 'mb.type');
        $this->addFilter('channel_name', 'ch_t.name');
        $this->addFilter('status', 'mb.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'mobikul_banner_id',
            'label'         => trans('mobikul::app.mobikul.category.featured-category-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'image',
            'label'         => trans('mobikul::app.mobikul.category.banner-image'),
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
            'index'         => 'banner_title',
            'label'         => trans('mobikul::app.mobikul.banner-image.banner-title'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'sort_order',
            'label'         =>  trans('mobikul::app.mobikul.banner-image.sort-order'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'type',
            'label'         => trans('mobikul::app.mobikul.banner-image.banner-type'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                return ucwords(strtolower(str_replace("_", " ", "{$row->type}")));
            }
        ]);

        $this->addColumn([
            'index'         => 'channel_name',
            'label'         =>  trans('mobikul::app.mobikul.notification.store-view'),
            'type'          => 'string',
            'searchable'    => false,
            'sortable'      => true,
            'filterable'    => false,
            'closure'       => true,
            'wrapper'       => function($row) {
                $bannerImageTranslations = app('Webkul\Mobikul\Repositories\BannerImageTranslationRepository')->where(['mobikul_banner_id' => $row->mobikul_banner_id])->groupBy('mobikul_banner_id', 'channel')->get();

                foreach ($bannerImageTranslations as $imageChannel) {
                    $channel = app('Webkul\Core\Repositories\ChannelRepository')->findOneByField('code', $imageChannel->channel);
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
            'route'     => 'mobikul.banner-image.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'method'    => 'POST', // use GET request only for redirect purposes
            'route'     => 'mobikul.banner-image.delete',
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
            'action'    => route('mobikul.banner-image.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.category.update-status'),
            'action'    => route('mobikul.banner-image.mass-update'),
            'method'    => 'POST',
            'options'   => [
                trans('mobikul::app.mobikul.category.enabled')   => 1,
                trans('mobikul::app.mobikul.category.disabled')  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
