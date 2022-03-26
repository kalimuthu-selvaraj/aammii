<?php

namespace Webkul\Mobikul\Listeners;

use Illuminate\Database\Schema\Blueprint;
use Webkul\Mobikul\Repositories\SearchTermRepository;

class Search
{
    /**
     * SearchTermRepository Repository Object
     *
     * @var \Webkul\Mobikul\Repositories\SearchTermRepository
     */
    protected $searchTermRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\Mobikul\Repositories\SearchTermRepository  $searchTermRepository
     * @return void
     */
    public function __construct(
        SearchTermRepository $searchTermRepository
    )   {
        $this->searchTermRepository = $searchTermRepository;
    }

    /**
     * Creates search term
     *
     * @param  \Webkul\Mobikul\Contracts\SearchTerm  $searchTerm
     * @return void
     */
    public function afterSearch($data)
    {
        $request = request()->get('term');
        if ( isset($request) && $request ) {
            $searchTerm = $this->searchTermRepository->findOneWhere([
                'term'          => $request,
                'channel_id'    => core()->getDefaultChannel()->id
                ]);

            if ( isset($searchTerm->id)) {
                $searchTerm->ratio += 1;
                $searchTerm->save();
            } else {
                $this->searchTermRepository->create([
                    'term'          => $request,
                    'ratio'         => 0,
                    'channel_id'    => core()->getDefaultChannel()->id
                ]);
            }
        }
    }
}