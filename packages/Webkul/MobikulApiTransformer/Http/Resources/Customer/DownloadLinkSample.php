<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DownloadLinkSample extends JsonResource
{    
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
        $this->downloadableLinkPurchasedRepository = app('Webkul\Sales\Repositories\DownloadableLinkPurchasedRepository');

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
        $customer = $this['customer'];
        
        $downloadableLinkPurchased = $this->downloadableLinkPurchasedRepository->findOneWhere([
            'id'            => $request->linkId,
            'customer_id'   => $customer->id
        ]);

        if (! isset($downloadableLinkPurchased->id) 
        || (isset($downloadableLinkPurchased->id) && $downloadableLinkPurchased->status == 'pending' )) {
            return [
                'success'   => false,
                'message'   => trans('mobikul-api::app.api.customer.download.error-download-auth'),
            ];
        }

        if ( $downloadableLinkPurchased->download_bought 
        && ($downloadableLinkPurchased->download_bought - $downloadableLinkPurchased->download_used) <= 0 ) {
            return [
                'success'   => false,
                'message'   => trans('shop::app.customer.account.downloadable_products.download-error'),
            ];
        }

        $remainingDownloads = $downloadableLinkPurchased->download_bought - ($downloadableLinkPurchased->download_used + 1);

        if ( $downloadableLinkPurchased->download_bought ) {
            $this->downloadableLinkPurchasedRepository->update([
                'download_used' => $downloadableLinkPurchased->download_used + 1,
                'status'        => $remainingDownloads <= 0 ? 'expired' : $downloadableLinkPurchased->status,
            ], $downloadableLinkPurchased->id);
        }

        if ( $downloadableLinkPurchased->type == 'file' ) {
            return [
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.customer.download.success-download'),
                'link'      => Storage::URL($downloadableLinkPurchased->file),
            ];
        } else {
            $fileName = $name = substr($downloadableLinkPurchased->url, strrpos($downloadableLinkPurchased->url, '/') + 1);;

            $tempImage = tempnam(sys_get_temp_dir(), $fileName);

            copy($downloadableLinkPurchased->url, $tempImage);

            return [
                'success'   => true,
                'message'   => trans('mobikul-api::app.api.customer.download.success-download'),
                'link'      => Storage::URL($tempImage),
            ];
        }
    }
}