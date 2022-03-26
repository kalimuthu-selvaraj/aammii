<?php

namespace Webkul\PriceDropAlert\Models;

use Webkul\Core\Eloquent\TranslatableModel;
use Webkul\PriceDropAlert\Contracts\EmailTemplate as EmailTemplateContract;

class EmailTemplate extends TranslatableModel implements EmailTemplateContract
{
    public $timestamps = true;

    public $translatedAttributes = [
        'name',
        'subject',
        'message',
    ];

    protected $table = 'email_templates';

    protected $fillable = ['status'];

    protected $with = ['translations'];
}
