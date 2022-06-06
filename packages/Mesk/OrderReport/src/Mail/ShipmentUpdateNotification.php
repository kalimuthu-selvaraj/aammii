<?php

namespace Mesk\OrderBulkUpdate\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ShipmentUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The customer instance.
     *
     * @var  \Webkul\Customer\Contracts\Customer
     */
    public $customer;

    /**
     * Create a new message instance.
     * 
     * @param  \Webkul\Customer\Contracts\Customer  $order
     * @param  string  $password
     * @return void
     */
    public function __construct(
        $customer
    )
    {
        $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
                    ->to($this->customer->email)
                    ->subject(trans('orderbulkupdate::app.admin.bulk-upload.emails.shipment-update'))
                    ->view('orderbulkupdate::admin.emails.shipment.update-shipment')->with(['customer' => $this->customer]);
    }
}