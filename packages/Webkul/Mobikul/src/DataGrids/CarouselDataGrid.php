<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Core\Models\Locale;
use Webkul\Core\Models\Channel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * CarouselDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CarouselDataGrid extends DataGrid
{
    protected $index = 'carousel_id'; // column that needs to be treated as index column

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

        $queryBuilder = DB::table('mobikul_carousel_translations as mc_t')
                ->leftJoin('mobikul_carousel as mc', 'mc_t.mobikul_carousel_id', '=', 'mc.id')
                ->leftJoin('channels as ch', 'mc_t.channel', '=', 'ch.code')
                ->leftJoin('channel_translations as ch_t', 'ch.id', '=', 'ch_t.channel_id')
                ->select(
                    'mc.id as carousel_id',
                    'mc_t.title as carousel_title',
                    'mc_t.channel',
                    'mc_t.locale',
                    'mc_t.mobikul_carousel_id',
                    'mc.image',
                    'mc.sort_order',
                    'mc.type',
                    'mc.status',
                    'mc.created_at',
                    'mc.updated_at',
                    'ch_t.name as channel_name'
                );

            $queryBuilder->whereIn('mc_t.locale', $this->whereInLocales);
            $queryBuilder->whereIn('mc_t.channel', $this->whereInChannels);

            $queryBuilder->groupBy('mc_t.mobikul_carousel_id', 'mc_t.channel', 'mc_t.locale');
        
        $this->addFilter('carousel_id', 'mc.id');
        $this->addFilter('carousel_title', 'mc.title');
        $this->addFilter('type', 'mc.type');
        $this->addFilter('sort_order', 'mc.sort_order');
        $this->addFilter('channel_name', 'ch_t.name');
        $this->addFilter('status', 'mc.status');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'carousel_id',
            'label'         => trans('mobikul::app.mobikul.carousel.carousel-id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'image',
            'label'         => trans('mobikul::app.mobikul.carousel.banner-image'),
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
            'index'         => 'carousel_title',
            'label'         => trans('mobikul::app.mobikul.carousel.title'),
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
            'label'         => trans('mobikul::app.mobikul.carousel.type'),
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
                $carouselImageTranslations = app('Webkul\Mobikul\Repositories\CarouselTranslationRepository')->where(['mobikul_carousel_id' => $row->mobikul_carousel_id])->groupBy('mobikul_carousel_id', 'channel')->get();

                foreach ($carouselImageTranslations as $imageChannel) {
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
            'title'     => trans('mobikul::app.mobikul.datagrid.edit'),
            'method'    => 'GET', //use post only for redirects only
            'route'     => 'mobikul.carousel.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'method'    => 'GET', // use GET request only for redirect purposes
            'title'     => trans('mobikul::app.mobikul.datagrid.assign'),
            'route'     => 'mobikul.carousel.assign',
            'icon'      => 'icon list-icon',
        ]);

        $this->addAction([
            'method'    => 'POST', // use GET request only for redirect purposes
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'route'     => 'mobikul.carousel.delete',
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
            'action'    => route('mobikul.carousel.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.category.update-status'),
            'action'    => route('mobikul.carousel.mass-update'),
            'method'    => 'POST',
            'options'   => [
                trans('mobikul::app.mobikul.category.enabled')   => 1,
                trans('mobikul::app.mobikul.category.disabled')  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
