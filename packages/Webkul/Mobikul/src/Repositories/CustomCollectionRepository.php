<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;

/**
 * CustomCollection Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class CustomCollectionRepository extends Repository
{    
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\CustomCollection';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\CustomCollection
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.custom-collection.create.before');
        
        if ( $data['product_collection'] == 'product_ids' ) {
            $data['product_ids'] = json_encode($data['product_ids']);
        }
        
        $collection = $this->model->create($data);

        Event::dispatch('mobikul.custom-collection.create.after', $collection);

        return $collection;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Mobikul\Contracts\Carousel
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $carousel = $this->find($id);

        Event::dispatch('mobikul.carousel.update.before', $id);

        $carousel->update($data);

        $this->uploadImages($data, $carousel);

        Event::dispatch('mobikul.carousel.update.after', $id);

        return $carousel;
    }
}