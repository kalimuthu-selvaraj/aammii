<?php

namespace Webkul\MobikulApiTransformer\Http\Resources\Contact;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Mail;
use Webkul\MobikulApiTransformer\Mail\PostEmail;

class Post extends JsonResource
{
    /**
     * Contains current channel
     *
     * @var string
     */
    protected $channel;

    /**
     * Contains email related data.
     *
     * @var array
     */
    protected $emailData = [];

    /**
     * Contains response message.
     *
     * @var string
     */
    protected $responseMessage = '';

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($resource)
    {
        $this->channelRepository = app('Webkul\Core\Repositories\ChannelRepository');

        $this->contactRepository = app('Webkul\Mobikul\Repositories\ContactRepository');

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

        $emailData = [
            'name'          => $this['name'],
            'email'         => $this['email'],
            'comment'       => $this['comment'],
            'telephone'     => isset($this['telephone']) ? $this['telephone'] : '',
            'channel_id'    => $channel->id,
            'adminEmail'    => core()->getAdminEmailDetails()['email'],
            'adminName'     => core()->getAdminEmailDetails()['name'],
            'email_status'  => 0,
        ];

        try {
            
            Mail::queue(new PostEmail($emailData));

            $emailData['email_status']  = 1;
            $this->responseMessage = trans('mobikul-api::app.api.contact.success-email');

        } catch (\Exception $e) {
            $emailData['email_status']  = 0;
            $this->responseMessage = $e->getMessage();
        }

        $contact = $this->contactRepository->create($emailData);

        if ( $contact ) {
            return [
                'success' => true,
                'message' => $this->responseMessage,
            ];
        } else {
            return [
                'success' => false,
                'message' => $this->responseMessage,
            ];
        }
    }
}
