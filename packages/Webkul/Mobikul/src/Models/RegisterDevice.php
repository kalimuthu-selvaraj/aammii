<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;

use Webkul\Mobikul\Contracts\RegisterDevice as RegisterDeviceContract;

class RegisterDevice extends Model implements RegisterDeviceContract
{
    public $timestamps = true;

    protected $table = 'mobikul_register_devices';

    // protected $guarded = ['_token'];

    protected $fillable = ['os', 'customer_id', 'fcmToken'];

}
