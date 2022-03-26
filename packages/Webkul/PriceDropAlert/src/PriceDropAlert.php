<?php

namespace Webkul\PriceDropAlert;

use Webkul\PriceDropAlert\Repositories\EmailTemplateRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;

class PriceDropAlert
{
    /**
     * EmailTemplateRepository class
     *
     * @var \Webkul\PriceDropAlert\Repositories\EmailTemplateRepository
     */
    protected $emailTemplateRepository;
    
    /**
     * ProductAttributeValueRepository class
     *
     * @var \Webkul\Product\Repositories\ProductAttributeValueRepository
     */
    protected $productAttributeValueRepository;

    /**
     * Create a new instance.
     *
     * @param  \Webkul\PriceDropAlert\Repositories\EmailTemplateRepository  $emailTemplateRepository
     * @param  \Webkul\Product\Repositories\ProductAttributeValueRepository  $productAttributeValueRepository
     *
     * @return void
     */
    public function __construct(
        EmailTemplateRepository $emailTemplateRepository,
        ProductAttributeValueRepository $productAttributeValueRepository
    )   {
        $this->emailTemplateRepository = $emailTemplateRepository;
        
        $this->productAttributeValueRepository = $productAttributeValueRepository;
    }

    /**
     * Returns all email template
     *
     * @return \Illuminate\Support\Collection
     */
    public function getEmailTemplates()
    {
        $email_templates = [];
        $emailTemplates = $this->emailTemplateRepository->with('translations')->get();

        if ( $emailTemplates ) {
            foreach($emailTemplates as $emailTemplate) {
                $email_templates[$emailTemplate->id] = $emailTemplate->name;
            }
        }
        
        return $email_templates;
    }

    /**
     * Returns product price drop alert
     *
     * @return \Illuminate\Support\Collection
     */
    public function isPriceDropAlertProduct($product)
    {
        $status = false;
        $attribute = $product->attribute_family->custom_attributes()->where('attributes.code', 'price_drop_alert')->first();

        if ( isset($attribute->id)) {
            $productAttributeValue = $this->productAttributeValueRepository->findOneWhere([
                'product_id'    => $product->product_id,
                'attribute_id'  => $attribute->id,
                // 'channel'       => core()->getCurrentChannelCode(),
                // 'locale'        => app()->getLocale(),
            ]);

            if ( isset($productAttributeValue->boolean_value)) {
                $status = $productAttributeValue->boolean_value;
            }
        }

        return $status;
    }
}