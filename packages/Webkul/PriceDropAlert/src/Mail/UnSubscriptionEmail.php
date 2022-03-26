<?php

namespace Webkul\PriceDropAlert\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Webkul\PriceDropAlert\Repository\EmailTemplateRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class UnSubscriptionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $unsubscriber;

    protected $templateIndexes = [
        'subscriber_email',
        'product_name',
        'product_price',
        'channel_name',
    ];

    /**
     * Create a mailable instance
     * 
     * @param  array  $subscriptionData
     */
    public function __construct(
        $unsubscriber
    )   {
        $this->unsubscriber = $unsubscriber;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = [];
        $emailTemplate = app('Webkul\PriceDropAlert\Repositories\EmailTemplateRepository')->findOneWhere([
            'id'        => core()->getConfigData('price_drop_alert.general.email_setting.unsubscription_email'),
            'status'    => 1
        ]);

        if ( isset($emailTemplate->id) ) {
            $mailTemplate  = $emailTemplate->translations->where('locale', app()->getLocale())->first();

            $find       = [];
            $replace    = [];
            foreach($this->templateIndexes as $index) {
                $find[$index] = '{' . $index . '}';
                $replace[$index] = '';
            }

            $replace['channel_name']    = core()->getDefaultChannel()->name;

            $replace = array_merge($replace, $this->unsubscriber);
            ksort($find);
            ksort($replace);

            $data['message'] = trim(str_replace($find, $replace, $mailTemplate->message));

            $data['subject'] = trim(str_replace($find, $replace, $mailTemplate->subject));

            $data['subject'] = html_entity_decode($data['subject'], ENT_QUOTES, "UTF-8");

            return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
                ->to($this->unsubscriber['subscriber_email'])
                ->subject($data['subject'])
                ->view('price_drop::shop.emails.subscribe-email')
                ->with('data', $data);
        }
    }
}