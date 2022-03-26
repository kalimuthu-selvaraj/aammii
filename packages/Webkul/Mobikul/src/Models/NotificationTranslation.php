<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\Mobikul\Contracts\NotificationTranslation as NotificationTranslationContract;

/**
 * Class NotificationTranslation
 *
 * @package Webkul\Mobikul\Models
 *
 */
class NotificationTranslation extends Model implements NotificationTranslationContract
{
    public $timestamps = false;

    protected $table = 'mobikul_notification_translations';
    
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the notification that owns the attribute value.
     */
    public function notification()
    {
        return $this->belongsTo(NotificationProxy::modelClass());
    }
}