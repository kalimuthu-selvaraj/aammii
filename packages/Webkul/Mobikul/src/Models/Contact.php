<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;

use Webkul\Mobikul\Contracts\Contact as ContactContract;

class Contact extends Model implements ContactContract
{
    public $timestamps = true;

    protected $table = 'mobikul_contacts';

    protected $fillable = ['name', 'email', 'comment', 'telephone', 'channel_id', 'email_status'];
}
