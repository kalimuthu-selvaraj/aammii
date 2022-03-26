<?php

namespace Webkul\MobikulApiTransformer\Helpers;

class View extends AbstractProduct
{
    /**
    * Returns the visible custom attributes
    *
    * @param Product $product
    * @return integer
    */
    public function getAdditionalData($product)
    {
        $data = [];

        $attributes = $product->attribute_family->custom_attributes()->get();

        $attributeOptionReposotory = app('Webkul\Attribute\Repositories\AttributeOptionRepository');

        foreach ($attributes as $attribute) {
            if ($product instanceof \Webkul\Product\Models\ProductFlat) {
                $value = $product->product->{$attribute->code};
            } else {
                $value = $product->{$attribute->code};
            }

            if ($attribute->type == 'boolean') {
                $value = $value ? 'Yes' : 'No';
            } else if($value) {
                if ($attribute->type == 'select') {
                    $attributeOption = $attributeOptionReposotory->find($value);
                    if ($attributeOption)
                        $value = $attributeOption->label ?? $attributeOption->admin_name;
                } else if ($attribute->type == 'multiselect' || $attribute->type == 'checkbox') {
                    $lables = [];
                    $attributeOptions = $attributeOptionReposotory->findWhereIn('id', explode(",", $value));
                    foreach ($attributeOptions as $attributeOption) {
                        $lables[] = $attributeOption->label ?? $attributeOption->admin_name;
                    }
                    $value = implode(", ", $lables);
                }
            }

            $data[] = [
                'label' => $attribute->name,
                'value' => strip_tags($value),
            ];
        }

        return $data;
    }
}