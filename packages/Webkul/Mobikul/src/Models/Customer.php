<?php

namespace Webkul\Mobikul\Models;

use Webkul\Customer\Models\Customer as CustomerBaseModel;
use Illuminate\Notifications\Notifiable;
use Webkul\Mobikul\Contracts\Customer as CustomerContract;
use Illuminate\Support\Facades\Storage;

class Customer extends CustomerBaseModel implements CustomerContract
{
    use Notifiable;

    protected $table = 'customers';

    protected $fillable = ['first_name', 'last_name', 'gender', 'date_of_birth', 'email', 'phone', 'password', 'customer_group_id', 'subscribed_to_news_letter', 'is_verified', 'token', 'notes', 'status','profile_pic', 'banner_pic'];

    protected $hidden = ['password', 'remember_token'];

    /**
     * Get customer's profile url.
     */
    public function profile_pic_url()
    {
        if (! $this->profile_pic)
            return;

        return Storage::url('mobikul_profile_image/' . $this->profile_pic);
    }

    /**
     * Get customer's profile url.
     */
    public function getProfilePicUrlAttribute()
    {
        return $this->profile_pic_url();
    }

    /**
     * Get customer's banner url.
     */
    public function banner_pic_url()
    {
        if (! $this->banner_pic)
            return;

        return Storage::url('mobikul_banner_image/' . $this->banner_pic);
    }

    /**
     * Get customer's banner url.
     */
    public function getBannerPicUrlAttribute()
    {
        return $this->banner_pic_url();
    }
}
