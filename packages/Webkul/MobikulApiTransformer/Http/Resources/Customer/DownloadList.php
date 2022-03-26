<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DownloadList extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;
    
    /**
     * Contains downloadable product list.
     *
     * @var array
     */
    protected $downloadList = [];
    
    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->downloadableLinkPurchasedRepository = app('Webkul\Sales\Repositories\DownloadableLinkPurchasedRepository');

        $this->orderRepository = app('Webkul\Sales\Repositories\OrderRepository');

        parent::__construct($resource);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $this->channel = request()->input('storeId');

        $channel = $this->channelRepository->find($this->channel);

        $params = [
            'channel_id'    => $channel->id,
            'customer_id'   => $this['customer_id'],
            'limit'         => isset($this['limit']) ? $this['limit'] : core()->getConfigData('mobikul.mobikul.basicinformation.current_page_size'),
        ];
        
        $request->merge(['page' => (isset($this['pageNumber']) && $this['pageNumber']) ? $this['pageNumber'] : 1]);
        
        $results = $this->downloadableLinkPurchasedRepository->scopeQuery(function($query) use ($params) {
            return $query->distinct()
                ->addSelect('downloadable_link_purchased.*')
                ->leftJoin('orders', 'downloadable_link_purchased.order_id', '=', 'orders.id')
                ->where('downloadable_link_purchased.customer_id', $params['customer_id'])
                ->where('orders.channel_id', $params['channel_id'])
                ->orderBy('downloadable_link_purchased.id', 'desc');
        })->paginate($params['limit']);
        
        if ( $results ) {
            foreach ($results as $download) {
                $order = $download->order;
                
                $isOrderExist = false;
                if ( $order ) {
                    $isOrderExist = true;
                }

                $remainingDownloads = $download['download_bought'] - $download['download_used'];
                $state = 'complete';
                if ( $remainingDownloads > 0 ) {
                    $state = 'remaining'; 
                }

                $fileUrl = '';
                $mimeType = '';
                if ( $download['type'] == 'file' ) {
                    $fileUrl = Storage::URL($download['file']);
                    $mimeType = mobikulApi()->from($fileUrl);
                } else {
                    $fileUrl = $download['url'];
                    $mimeType = mobikulApi()->from($fileUrl);
                }
                
                $this->downloadList[] = [
                    'message'           => '',
                    'status'            => $download['status'],
                    'downloadUrl'       => route('customer.downloadable_products.download', $download['id']),
                    'fileUrl'           => $fileUrl,
                    'hash'              => '',
                    'mimeType'          => $mimeType,
                    'canReorder'        => false,
                    'incrementId'       => $download['id'],
                    'isOrderExist'      => $isOrderExist,
                    'date'              => $download['created_at'],
                    'proName'           => $download['product_name'],
                    'downloadName'      => $download['name'],
                    'remainingDownloads'=> $remainingDownloads,
                    'state'             => $state,
                    'statusColorCode'   => "#d5d5d5"
                ];
            }
        }

        return [
            'success'       => true,
            'message'       => '',
            'totalCount'    => count($this->downloadList),
            'downloadsList' => $this->downloadList,
            'eTag'          => '3fcceed4d844f9646517de88206f3d18', //need discussion
        ];
    }
}