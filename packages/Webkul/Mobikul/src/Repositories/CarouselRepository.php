<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

/**
 * Carousel Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class CarouselRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\Carousel';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\Carousel
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.carousel.create.before');

        $carousel = $this->model->create($data);

        $this->uploadImages($data, $carousel);

        Event::dispatch('mobikul.carousel.create.after', $carousel);

        return $carousel;
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

        Event::dispatch('mobikul.carousel.update.after', $carousel);

        return $carousel;
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Mobikul\Contracts\Carousel  $carousel
     * @return void
     */
    public function uploadImages($data, $carousel, $type = "image")
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'carousel/backroundImages/' . $carousel->id;

                if ($request->hasFile($file)) {
                    if ($carousel->{$type}) {
                        Storage::delete($carousel->{$type});
                    }

                    $carousel->{$type} = $request->file($file)->store($dir);
                    $carousel->save();
                }
            }
        } else {
            if ($carousel->{$type}) {
                Storage::delete($carousel->{$type});
            }

            $carousel->{$type} = null;
            $carousel->save();
        }
    }
}