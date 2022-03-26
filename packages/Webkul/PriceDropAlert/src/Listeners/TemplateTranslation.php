<?php

namespace Webkul\PriceDropAlert\Listeners;

use Illuminate\Database\Schema\Blueprint;
use Webkul\PriceDropAlert\Repositories\EmailTemplateTranslationRepository;

class TemplateTranslation
{
    /**
     * EmailTemplateTranslationRepository Repository Object
     *
     * @var \Webkul\PriceDropAlert\Repositories\EmailTemplateTranslationRepository
     */
    protected $emailTemplateTranslationRepository;

    /**
     * Create a new listener instance.
     *
     * @param  \Webkul\PriceDropAlert\Repositories\EmailTemplateTranslationRepository  $emailTemplateTranslationRepository
     * @return void
     */
    public function __construct(
        EmailTemplateTranslationRepository $emailTemplateTranslationRepository
    )   {
        $this->emailTemplateTranslationRepository = $emailTemplateTranslationRepository;
    }

    /**
     * Creates template translation
     *
     * @param  \Webkul\PriceDropAlert\Contracts\EmailTemplateTranslation  $emailTemplateTranslation
     * @return void
     */
    public function afterTemplateCreatedUpdated($emailTemplate)
    {
        $locale_request = request()->get('locale') ?: '';

        $data = request()->all();
        $channels[] = core()->getDefaultChannelCode();

        foreach (core()->getAllChannels() as $channel) {
            if (in_array($channel->code, $channels)) {
                foreach ($channel->locales as $locale) {
                    $templateTranslation = $this->emailTemplateTranslationRepository->findOneWhere([
                        'email_template_id' => $emailTemplate->id,
                        'locale'            => $locale->code,
                    ]);

                    if (! $templateTranslation) {
                        $templateTranslation = $this->emailTemplateTranslationRepository->create([
                            'email_template_id' => $emailTemplate->id,
                            'name'              => isset($data[$locale->code]['name']) ? $data[$locale->code]['name'] : $data['name'],
                            'subject'           => isset($data[$locale->code]['subject']) ? $data[$locale->code]['subject'] : $data['subject'],
                            'message'           => isset($data[$locale->code]['message']) ? $data[$locale->code]['message'] : $data['message'],
                            'locale'            => $locale->code,
                            'locale_id'         => $locale->id,
                        ]);
                    }

                    if ( $locale_request && ($locale->code == $locale_request) ) {
                        $templateTranslation->name      = isset($data[$locale->code]['name']) ? $data[$locale->code]['name'] : $data['name'];
                        $templateTranslation->subject   = isset($data[$locale->code]['subject']) ? $data[$locale->code]['subject'] : $data['subject'];
                        $templateTranslation->message   = isset($data[$locale->code]['message']) ? $data[$locale->code]['message'] : $data['message'];
                        
                        $templateTranslation->save();
                    }
                }
            }
        }
    }
}