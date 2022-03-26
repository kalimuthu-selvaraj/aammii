<?php
namespace Webkul\MobikulApiTransformer\Helpers;
use Webkul\Attribute\Repositories\AttributeOptionRepository as AttributeOption;
use Illuminate\Support\Facades\Storage;

class ProductImage extends AbstractProduct
{
    /**
     * Retrieve collection of gallery images
     *
     * @param Product $product
     * @return array
     */
    public function getGalleryImages($product)
    {
        if (! $product)
            return [];

        $images = [];
        foreach ($product->images as $image) {
            if (! Storage::has($image->path))
                continue;
            $images[] = [
                'smallImage' => url('cache/small/' . $image->path),
                'largeImage' => url('cache/large/' . $image->path),
            ];
        }
        if (! $product->parent_id && ! count($images)) {
            $images[] = [
                'smallImage' => asset('vendor/webkul/ui/assets/images/product/small-product-placeholder.png'),
                'largeImage' => asset('vendor/webkul/ui/assets/images/product/large-product-placeholder.png'),
            ];
        }

        return $images;
    }
    /**
     * Get product's base image
     *
     * @param Product $product
     * @return array
     */
    public function getProductBaseImage($product)
    {
        $images = $product ? $product->images : null;

        if ($images && $images->count()) {
            $image = [
                'smallImage' => url('cache/small/' . $images[0]->path),
                'mediumImage' => url('cache/medium/' . $images[0]->path),
                'largeImage' => url('cache/large/' . $images[0]->path),
                'originalImage' => url('cache/original/' . $images[0]->path),
            ];
        } else {
            $image = [
                'smallImage' => asset('vendor/webkul/ui/assets/images/product/small-product-placeholder.png'),
                'medium_image_url' => asset('vendor/webkul/ui/assets/images/product/meduim-product-placeholder.png'),
                'largeImage' => asset('vendor/webkul/ui/assets/images/product/large-product-placeholder.png'),
                'original_image_url' => asset('vendor/webkul/ui/assets/images/product/large-product-placeholder.png'),
            ];
        }
        return $image;
    }
}