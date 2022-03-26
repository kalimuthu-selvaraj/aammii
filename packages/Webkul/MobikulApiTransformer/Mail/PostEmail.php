<?php

namespace Webkul\MobikulApiTransformer\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * PosttEmail Mail class
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class PostEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $email_data;

    public function __construct($email_data) {
        $this->email_data = $email_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->email_data['adminEmail'])
            ->subject('Want to Contact')
            ->html('<h3>'.'Hi ' . $this->email_data['adminName'] . ',<br>' . $this->email_data['name'] . ' want to contact!</h3><br>'.
                $this->email_data['comment'].'<br><br>'."Thanks", 'text/html')
            ->from($this->email_data['email']);
    }
}