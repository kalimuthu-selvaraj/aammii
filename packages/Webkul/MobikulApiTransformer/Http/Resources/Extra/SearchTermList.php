<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Extra;

use Illuminate\Http\Resources\Json\JsonResource;

class SearchTermList extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains current currency
     *
     * @var string
     */
    protected $currencyCode;

    /**
     * Contains searched product list.
     *
     * @var array
     */
    protected $searchResult = [];

    /**
     * Contains searched Term list.
     *
     * @var array
     */
    protected $searchTermList = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->searchTermRepository = app('Webkul\Mobikul\Repositories\SearchTermRepository');

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
        $this->currencyCode = request()->input('currency');

        $channel = $this->channelRepository->find($this->channel);

        $searchTerms = $this->searchTermRepository->findWhere([
            'channel_id'  =>   $channel->id
        ]);
        
        if ( count($searchTerms) ) {
            foreach ($searchTerms as $term) {
                $this->searchTermList[] = [
                    'term'  => $term['term'],
                    'ratio' => $term['ratio'],
                ];
            }
        }

        return [
            'success'   => true,
            'message'   => (count($this->searchTermList) > 0) ? 'Success: Search term list.' : 'Warning: No search term found.',
            'termList'  => $this->searchTermList,
        ];
    }
}

