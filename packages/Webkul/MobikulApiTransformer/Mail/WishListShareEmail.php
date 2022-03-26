<?php

namespace Webkul\MobikulApiTransformer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * WishListShareEmail Mail class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class WishListShareEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $recipientData;

    public function __construct($recipientData) {
        $this->recipientData = $recipientData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->recipientData['recipientEmail'])
        ->from($this->recipientData['customerEmail'])
        ->subject(trans('mobikul-api::app.api.sales.share-wishlist.email-subject', ['sender_name'  => $this->recipientData['customerName']]))
        ->view('mobikul::shop.customers.email.wishlistshare')->with($this->recipientData);
    }
}