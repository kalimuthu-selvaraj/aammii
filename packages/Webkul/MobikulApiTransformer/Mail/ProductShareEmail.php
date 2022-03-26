<?php

namespace Webkul\MobikulApiTransformer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * ProductShareEmail Mail class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class ProductShareEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Contains the sender & receiver emails.
     *
     * @var array
     */
    protected $recipientData = [];

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct($recipientData)
    {
        $this->recipientData = $recipientData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->recipientData['receiverEmail'])
            ->subject(trans('mobikul-api::app.api.catalog.product-share.email-subject'))
            ->from($this->recipientData['senderEmail'])
            ->view('mobikul::shop.customers.email.productshare')->with(['data' => $this->recipientData]);
    }
}