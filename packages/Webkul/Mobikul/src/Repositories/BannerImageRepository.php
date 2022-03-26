<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Webkul\Mobikul\Repositories\BannerImageTranslationRepository;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

/**
 * BannerImage Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class BannerImageRepository extends Repository
{
    /**
     * BannerImageTranslationRepository object
     *
     * @var \Webkul\Mobikul\Repositories\BannerImageTranslationRepository
     */
    protected $bannerImageTranslationRepository;

    /**
     * Create a new repository instance.
     *
     * @param \Webkul\Mobikul\Repositories\BannerImageTranslationRepository $bannerImageTranslationRepository
     *
     * @return void
     */
    public function __construct(
        BannerImageTranslationRepository $bannerImageTranslationRepository,
        App $app
    ) {
        $this->bannerImageTranslationRepository = $bannerImageTranslationRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\BannerImage';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\BannerImage
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.banner-image.create.before');
        
        $bannerImage = $this->model->create($data);
        
        $this->uploadImages($data, $bannerImage);

        Event::dispatch('mobikul.banner-image.create.after', $bannerImage);

        return $bannerImage;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Mobikul\Contracts\MobikulBanner
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $bannerImage = $this->findOrFail($id);

        Event::dispatch('mobikul.banner-image.update.before', $id);

        $bannerImage->update($data);

        $this->uploadImages($data, $bannerImage);

        Event::dispatch('mobikul.banner-image.update.after', $bannerImage);

        return $bannerImage;
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Mobikul\Contracts\BannerImages  $bannerImage
     * @return void
     */
    public function uploadImages($data, $bannerImage, $type = "image")
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'banner/images/' . $bannerImage->id;

                if ($request->hasFile($file)) {
                    if ($bannerImage->{$type}) {
                        Storage::delete($bannerImage->{$type});
                    }

                    $bannerImage->{$type} = $request->file($file)->store($dir);
                    $bannerImage->save();
                }
            }
        } else {
            if ($bannerImage->{$type}) {
                Storage::delete($bannerImage->{$type});
            }

            $bannerImage->{$type} = null;
            $bannerImage->save();
        }
    }
}

