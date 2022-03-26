<?php

namespace Webkul\Mobikul\DataGrids;

use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Core\Models\Locale;
use Webkul\Core\Models\Channel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * NotificationsDataGrid class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright  2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class NotificationsDataGrid extends DataGrid
{
    protected $index = 'notification_id';

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

        $queryBuilder = DB::table('mobikul_notification_translations as mn_t')
                            ->leftJoin('mobikul_notifications as mn', 'mn_t.mobikul_notification_id', '=', 'mn.id')
                            ->leftJoin('channels as ch', 'mn_t.channel', '=', 'ch.code')
                            ->leftJoin('channel_translations as ch_t', 'ch.id', '=', 'ch_t.channel_id')
                            ->addSelect(
                                'mn_t.mobikul_notification_id as notification_id',
                                'mn.image',
                                'mn_t.title',
                                'mn_t.content',
                                'mn_t.channel',
                                'mn_t.locale',
                                'mn.type',
                                'mn.product_category_id',
                                'mn.status',
                                'mn.created_at',
                                'mn.updated_at',
                                'ch_t.name as channel_name'
                            );
            $queryBuilder->whereIn('mn_t.locale', $this->whereInLocales);
            $queryBuilder->whereIn('mn_t.channel', $this->whereInChannels);
        
            $queryBuilder->groupBy('mn_t.mobikul_notification_id', 'mn_t.channel', 'mn_t.locale');

        $this->addFilter('notification_id', 'mn_t.mobikul_notification_id');
        $this->addFilter('title', 'mn_t.title');
        $this->addFilter('content', 'mn_t.content');
        $this->addFilter('channel_name', 'ch_t.name');
        $this->addFilter('status', 'mn.status');
        $this->addFilter('type', 'mn.type');

        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {
        $this->addColumn([
            'index'         => 'notification_id',
            'label'         => trans('mobikul::app.mobikul.notification.id'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'image',
            'label'         => trans('mobikul::app.mobikul.notification.image'),
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
            'label'         => trans('mobikul::app.mobikul.notification.title'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
        ]);

        $this->addColumn([
            'index'         => 'content',
            'label'         => trans('mobikul::app.mobikul.notification.notification-content'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true
        ]);

        $this->addColumn([
            'index'         => 'type',
            'label'         => trans('mobikul::app.mobikul.notification.notification-type'),
            'type'          => 'string',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                return ucwords(strtolower(str_replace("_", " ", $row->type)));
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
                $notificationTranslations = app('Webkul\Mobikul\Repositories\NotificationTranslationRepository')->where(['mobikul_notification_id' => $row->notification_id])->groupBy('mobikul_notification_id', 'channel')->get();

                foreach ($notificationTranslations as $imageChannel) {
                    $channel = app('Webkul\Core\Repositories\ChannelRepository')->findOneByField('code', $imageChannel->channel);
                    if ( $channel ) {
                        echo $channel['name'] . '</br>' . PHP_EOL;
                    } 
                }
            }
        ]);

        $this->addColumn([
            'index'         => 'status',
            'label'         => trans('mobikul::app.mobikul.notification.notification-status'),
            'type'          => 'number',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true,
            'closure'       => true,
            'wrapper'       => function($row) {
                if ( $row->status == 1 )
                    return '<span class="badge badge-md badge-success">' . trans('mobikul::app.mobikul.notification.status.enabled') . '</span>';
                else
                    return '<span class="badge badge-md badge-danger">' . trans('mobikul::app.mobikul.notification.status.disabled') . '</span>';
            }
        ]);

        $this->addColumn([
            'index'         => 'created_at',
            'label'         =>  trans('mobikul::app.mobikul.notification.created'),
            'type'          => 'datetime',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true
        ]);

        $this->addColumn([
            'index'         => 'updated_at',
            'label'         => trans('mobikul::app.mobikul.notification.modified'),
            'type'          => 'datetime',
            'searchable'    => true,
            'sortable'      => true,
            'filterable'    => true
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'type'      => 'Edit',
            'title'     => trans('mobikul::app.mobikul.datagrid.edit'),
            'method'    => 'GET', //use post only for redirects only
            'route'     => 'mobikul.notification.edit',
            'icon'      => 'icon pencil-lg-icon'
        ]);

        $this->addAction([
            'title'     => trans('mobikul::app.mobikul.datagrid.delete'),
            'method'    => 'POST', // use GET request only for redirect purposes
            'route'     => 'mobikul.notification.delete',
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
            'action'    => route('mobikul.notification.mass-delete'),
            'method'    => 'POST',
        ]);

        $this->addMassAction([
            'type'      => 'update',
            'title'     => trans('mobikul::app.mobikul.category.update-status'),
            'action'    => route('mobikul.notification.mass-update'),
            'method'    => 'POST',
            'options'   => [
                trans('mobikul::app.mobikul.category.enabled')   => 1,
                trans('mobikul::app.mobikul.category.disabled')  => 0
            ]
        ]);

        $this->enableMassAction = true;
    }
}
