<?php

namespace Webkul\MobikulApiTransformer\Repositories;

use Webkul\Core\Eloquent\Repository;

/**
 * Customer Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class CustomerRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */

    function model()
    {
        return 'Webkul\Customer\Contracts\Customer';
    }

    /**
     * after customer data saved
     */
    public function saveCustomerData($customer)
    {
        // dd(request()->all());
    }
}