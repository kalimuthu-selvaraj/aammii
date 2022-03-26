<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * BannerImageTranslation Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class BannerImageTranslationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\BannerImageTranslation';
    }
}