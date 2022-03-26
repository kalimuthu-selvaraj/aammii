<?php

namespace Webkul\Mobikul\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Container\Container as App;
use Webkul\Mobikul\Repositories\FeaturedCategoryChannelRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Event;

/**
 * FeaturedCategoryRepository Reposotory
 *
 * @author    Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2020 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class FeaturedCategoryRepository extends Repository
{
    /**
     * FeaturedCategoryChannelRepository object
     *
     * @var \Webkul\Mobikul\Repositories\FeaturedCategoryChannelRepository
     */
    protected $featuredCategoryChannelRepository;

    /**
     * Create a new repository instance.
     *
     * @param \Webkul\Mobikul\Repositories\FeaturedCategoryChannelRepository $featuredCategoryChannelRepository
     *
     * @return void
     */
    public function __construct(
        FeaturedCategoryChannelRepository $featuredCategoryChannelRepository,
        App $app
    )   {
        $this->featuredCategoryChannelRepository = $featuredCategoryChannelRepository;

        parent::__construct($app);
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public function model()
    {
        return 'Webkul\Mobikul\Contracts\FeaturedCategory';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Mobikul\Contracts\FeaturedCategory
     */
    public function create(array $data)
    {
        Event::dispatch('mobikul.featured-category.create.before');

        $featuredCategory = $this->model->create($data);

        if ( $featuredCategory && is_array($data['channels'])) {
            foreach ($data['channels'] as $key => $channel_id) {
                $this->featuredCategoryChannelRepository->create([
                    'channel_id'            => $channel_id,
                    'featured_category_id'  => $featuredCategory->id,
                ]);
            }
        }

        $this->uploadImages($data, $featuredCategory);

        Event::dispatch('mobikul.featured-category.create.after', $featuredCategory);

        return $featuredCategory;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\Mobikul\Contracts\FeaturedCategory
     */
    public function update(array $data, $id, $attribute = "id")
    {
        $featuredCategory = $this->find($id);

        Event::dispatch('mobikul.featured-category.update.before', $id);

        $featuredCategory->update($data);

        if ( $featuredCategory && is_array($data['channels'])) {
            $featuredCategoryChannels = $featuredCategory->featuredCategoryChannelsArray();

            foreach ($data['channels'] as $channel_id) {
                if ( ($key = array_search($channel_id, $featuredCategoryChannels)) !== false) {
                    unset($featuredCategoryChannels[$key]);
                } else {
                    $this->featuredCategoryChannelRepository->create([
                        'channel_id'            => $channel_id,
                        'featured_category_id'  => $featuredCategory->id,
                    ]);
                }
            }
            
            if (! empty($featuredCategoryChannels)) {
                foreach ($featuredCategoryChannels as $channel_id) {
                    $this->featuredCategoryChannelRepository->findOneWhere([
                        'featured_category_id'  => $id,
                        'channel_id'            => $channel_id,
                    ])->delete();
                }
            }
        }

        $this->uploadImages($data, $featuredCategory);

        Event::dispatch('mobikul.featured-category.update.after', $id);

        return $featuredCategory;
    }

    /**
     * @param  array  $data
     * @param  \Webkul\Mobikul\Contracts\FeaturedCategory  $featuredCategory
     * @return void
     */
    public function uploadImages($data, $featuredCategory, $type = "image")
    {
        if (isset($data[$type])) {
            $request = request();

            foreach ($data[$type] as $imageId => $image) {
                $file = $type . '.' . $imageId;
                $dir = 'category/images/' . $featuredCategory->id;

                if ($request->hasFile($file)) {
                    if ($featuredCategory->{$type}) {
                        Storage::delete($featuredCategory->{$type});
                    }

                    $featuredCategory->{$type} = $request->file($file)->store($dir);
                    $featuredCategory->save();
                }
            }
        } else {
            if ($featuredCategory->{$type}) {
                Storage::delete($featuredCategory->{$type});
            }

            $featuredCategory->{$type} = null;
            $featuredCategory->save();
        }
    }
}

