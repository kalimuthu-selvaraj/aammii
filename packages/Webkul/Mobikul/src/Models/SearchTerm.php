<?php

namespace Webkul\Mobikul\Models;

use Illuminate\Database\Eloquent\Model;

use Webkul\Mobikul\Contracts\SearchTerm as SearchTermContract;

class SearchTerm extends Model implements SearchTermContract
{
    public $timestamps = true;

    protected $table = 'mobikul_search_terms';

    protected $fillable = ['term', 'ratio', 'channel_id'];
}
