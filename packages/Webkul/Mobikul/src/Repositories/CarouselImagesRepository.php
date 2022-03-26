<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;
use Webkul\Core\Repositories\ChannelRepository;
use Webkul\CMS\Repositories\CmsRepository;
use Illuminate\Container\Container as App;

/**
 * Carousel Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class CarouselImagesRepository extends Repository
{
    /**
     * ChannelRepository object
     *
     * @var \Webkul\Core\Repositories\ChannelRepository
     */
    protected $channelRepository;

    /**
     * CmsRepository object
     *
     * @var \Webkul\CMS\Repositories\CmsRepository
     */
    protected $cmsRepository;

    public function __construct(
        ChannelRepository $channelRepository,
        CmsRepository $cmsRepository,
        App $app
    ) {
        $this->channelRepository = $channelRepository;

        $this->cmsRepository = $cmsRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\CarouselImages';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\CarouselImages
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.carousel-image.create.before');

        $carouselImage = $this->model->create($data);

        $this->uploadImages($data, $carouselImage);

        Event::dispatch('mobikul.carousel-image.create.after', $carouselImage);

        return $carouselImage;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Mobikul\Contracts\CarouselImages
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $carouselImage = $this->find($id);

        Event::dispatch('mobikul.carousel-image.update.before', $id);

        $carouselImage->update($data);

        $this->uploadImages($data, $carouselImage);

        Event::dispatch('mobikul.carousel-image.update.after', $carouselImage);

        return $carouselImage;
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Mobikul\Contracts\CarouselImages  $carouselImages
     * @return void
     */
    public function uploadImages($data, $carouselImage, $type = "image")
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'carousel/images/' . $carouselImage->id;

                if ($request->hasFile($file)) {
                    if ($carouselImage->{$type}) {
                        Storage::delete($carouselImage->{$type});
                    }

                    $carouselImage->{$type} = $request->file($file)->store($dir);
                    $carouselImage->save();
                }
            }
        } else {
            if ($carouselImage->{$type}) {
                Storage::delete($carouselImage->{$type});
            }

            $carouselImage->{$type} = null;
            $carouselImage->save();
        }
    }

    /**
     * get channels
    */
    public function getChannels()
    {
        $data = $this->channelRepository->get();

        foreach($data as $channel) {
            $allchannel[$channel->code] = $channel->name;
        }

        return $allchannel;
    }

    /**
     * get channels
    */
    public function getCMSPages()
    {
        $pages = [];
        $cmsPages = $this->cmsRepository->get();
        foreach($cmsPages as $cms) {
            $pages[$cms->url_key] = $cms->page_title;
        }
        
        return $pages;
    }

    public function getCarouselImages($carousel_id, $data = array())
    {
        $results = app('Webkul\Mobikul\Repositories\CarouselImagesRepository')->scopeQuery(function($query) use ($carousel_id, $data) {

            return $query->distinct()
                            ->leftJoin('mobikul_carousel_images_products_pivot as ci_pivot', 'mobikul_carousel_images.id', '=', 'ci_pivot.carousel_image_id')
                            ->leftJoin('mobikul_carousel_translations as mc_t', 'ci_pivot.carousel_id', '=', 'mc_t.mobikul_carousel_id')
                            ->addSelect('mobikul_carousel_images.id as ci_id', 'mobikul_carousel_images.image', 'mc_t.title as carousel_title', 'mobikul_carousel_images.title', 'ci_pivot.carousel_image_id as image_id', 'mobikul_carousel_images.type', 'product_category_id', 'mobikul_carousel_images.status')
                            ->where('mc_t.channel', $data['channel'])
                            ->where('mc_t.locale', $data['locale'])
                            ->where('mobikul_carousel_images.status', 1)
                            ->where('ci_pivot.carousel_id', $carousel_id);
        })->get();

        return $results;
    }

    public function getCarouselProducts($carousel_id, $data = array())
    {
        $results = app('Webkul\Product\Repositories\ProductFlatRepository')->scopeQuery(function($query) use ($carousel_id, $data) {

            return $query->distinct()
                            ->leftJoin('products', 'product_flat.product_id', '=', 'products.id')
                            ->leftJoin('mobikul_carousel_images_products_pivot as ci_pivot', 'products.id', '=', 'ci_pivot.products_id')
                            ->leftJoin('mobikul_carousel_translations', 'ci_pivot.carousel_id', '=', 'mobikul_carousel_translations.mobikul_carousel_id')

                            ->select('product_flat.product_id as product_id', 'product_flat.name as product_name', 'products.type as product_type', 'mobikul_carousel_translations.title as carousel_title', 'ci_pivot.products_id as carousel_product_id', 'product_flat.status')
                            ->where('mobikul_carousel_translations.channel', $data['channel'])
                            ->where('mobikul_carousel_translations.locale', $data['locale'])
                            ->where('product_flat.channel', $data['channel'])
                            ->where('product_flat.locale', $data['locale'])
                            ->where('product_flat.status', 1)
                            ->where('ci_pivot.carousel_id', $carousel_id)
                            ->groupBy('product_flat.product_id');
        })->get();
        
        return $results;
    }
}

