<?php

namespace Webkul\Mobikul\Repositories;

use Illuminate\Support\Facades\Event;
use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\Storage;

/**
 * ImageProductCarousel Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class ImageProductCarouselRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\ImageProductCarousel';
    }
}

