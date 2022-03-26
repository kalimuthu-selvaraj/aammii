<?php

namespace Webkul\PriceDropAlert\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * EmailTemplateTranslation Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class EmailTemplateTranslationRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\PriceDropAlert\Contracts\EmailTemplateTranslation';
    }
}