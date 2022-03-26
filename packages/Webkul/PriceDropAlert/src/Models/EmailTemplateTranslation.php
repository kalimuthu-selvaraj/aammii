<?php

namespace Webkul\PriceDropAlert\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\PriceDropAlert\Contracts\EmailTemplateTranslation as EmailTemplateTranslationContract;

/**
 * Class EmailTemplateTranslation
 *
 * @package Webkul\PriceDropAlert\Models
 *
 */
class EmailTemplateTranslation extends Model implements EmailTemplateTranslationContract
{
    public $timestamps = false;

    protected $table = 'email_template_translations';

    protected $fillable = [
        'name',
        'subject',
        'message',
        'locale',
        'locale_id',
    ];
}