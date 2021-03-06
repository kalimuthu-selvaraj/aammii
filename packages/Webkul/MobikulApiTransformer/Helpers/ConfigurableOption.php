<?php

namespace Webkul\MobikulApiTransformer\Helpers;

use Webkul\Attribute\Repositories\AttributeOptionRepository as AttributeOption;
use Webkul\Product\Helpers\ProductImage;
use Webkul\MobikulApiTransformer\Helpers\Price;
use Webkul\Product\Models\Product;
use Webkul\Product\Models\ProductAttributeValue;

class ConfigurableOption extends AbstractProduct
{
    /**
     * AttributeOptionRepository object
     *
     * @var array
     */
    protected $attributeOption;

    /**
     * ProductImage object
     *
     * @var array
     */
    protected $productImage;

    /**
     * Price object
     *
     * @var array
     */
    protected $price;

    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\AttributeOptionRepository $attributeOption
     * @param  Webkul\Product\Helpers\ProductImage                     $productImage
     * @param  Webkul\Product\Helpers\Price                            $price
     * @return void
     */
    public function __construct(
        AttributeOption $attributeOption,
        ProductImage $productImage,
        Price $price
    )
    {
        $this->attributeOption = $attributeOption;

        $this->productImage = $productImage;

        $this->price = $price;
    }

    /**
     * Returns the allowed variants
     *
     * @param Product $product
     * @return float
     */
    public function getAllowedProducts($product)
    {
        static $variants = [];

        if (count($variants))
            return $variants;

        foreach ($product->variants as $variant) {
            if ($variant->isSaleable()) {
                $variants[] = $variant;
            }
        }

        return $variants;
    }

    /**
     * Returns the allowed variants JSON
     *
     * @param Product $product
     * @return float
     */
    public function getConfigurationConfig($product)
    {
        $options = $this->getOptions($product, $this->getAllowedProducts($product));

        $config = [
            'attributes' => $this->getAttributesData($product, $options),
            'index' => isset($options['index']) ? $options['index'] : [],
            'regular_price' => [
                'formated_price' => core()->currency($this->price->getMinimalPrice($product)),
                'price' => $this->price->getMinimalPrice($product)
            ],
            'variant_prices' => $this->getVariantPrices($product),
            'variant_images' => $this->getVariantImages($product),
            'chooseText' => trans('shop::app.products.choose-option')
        ];

        return $config;
    }

    /**
     * Get allowed attributes
     *
     * @param Product $product
     * @return array
     */
    public function getAllowAttributes($product)
    {
        return $product->product->super_attributes;
    }

    /**
     * Get Configurable Product Options
     *
     * @param Product $currentProduct
     * @param array $allowedProducts
     * @return array
     */
    public function getOptions($currentProduct, $allowedProducts)
    {
        $options = [];

        $allowAttributes = $this->getAllowAttributes($currentProduct);

        foreach ($allowedProducts as $product) {
            if ($product instanceof \Webkul\Product\Models\ProductFlat) {
                $productId = $product->product_id;
            } else {
                $productId = $product->id;
            }

            foreach ($allowAttributes as $productAttribute) {
                $productAttributeId = $productAttribute->id;

                $attributeValue = $product->{$productAttribute->code};

                if ($attributeValue == '' && $product instanceof \Webkul\Product\Models\ProductFlat)
                    $attributeValue = $product->product->{$productAttribute->code};

                $options[$productAttributeId][$attributeValue][] = $productId;

                $options['index'][$productId][$productAttributeId] = $attributeValue;
            }
        }

        return $options;
    }

    /**
     * Get product attributes
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesData($product, array $options = [])
    {
        $defaultValues = [];

        $attributes = [];

        $allowAttributes = $this->getAllowAttributes($product);

        foreach ($allowAttributes as $attribute) {

            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);

            if (isset($attributeOptionsData)) {
                $attributeId = $attribute->id;

                $attributes[] = [
                    'id' => $attributeId,
                    'code' => $attribute->code,
                    'label' => $attribute->name ? $attribute->name : $attribute->admin_name,
                    'options' => $this->getAttributeOptionsData($attribute,$attributeOptionsData),
                ];
            }
        }

        return $attributes;
    }

    /**
     * @param Attribute $attribute
     * @param array $options
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $options)
    {
        $attributeOptionsData = [];

        foreach ($attribute->options as $attributeOption) {

            $optionId = $attributeOption->id;

            if (isset($options[$attribute->id][$optionId])) {
                $attributeOptionsData[] = [
                    'id' => $optionId,
                    'label' => $attributeOption->label,
                    'products' => $options[$attribute->id][$optionId]
                ];
            }
        }

        return $attributeOptionsData;
    }

    /**
     * Get product prices for configurable variations
     *
     * @param Product $product
     * @return array
     */

    public function getVariantPrices($product)
    {
        $prices = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Webkul\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $prices[] = [
                'oldPrice' => [
                    'formated_price' => core()->currency($variant->price),
                    'amount' => core()->convertPrice($variant->price)
                ],
                'basePrice' => [
                    'formated_price' => core()->currency($this->price->getMinimalPrice($variant)),
                    'amount' => $this->price->getMinimalPrice($variant)
                ],
                'finalPrice' => [
                    'formated_price' => core()->currency($this->price->getMinimalPrice($variant)),
                    'amount' => core()->convertPrice($this->price->getMinimalPrice($variant))
                ],
                'product' => $variantId
            ];
        }

    return $prices;
    }

    /**
     * Get product images for configurable variations
     *
     * @param Product $product
     * @return array
     */
    protected function getVariantImages($product)
    {
        $images = [];

        foreach ($this->getAllowedProducts($product) as $variant) {
            if ($variant instanceof \Webkul\Product\Models\ProductFlat) {
                $variantId = $variant->product_id;
            } else {
                $variantId = $variant->id;
            }

            $images[$variantId] = $this->productImage->getGalleryImages($variant);
        }

        return $images;
    }
}