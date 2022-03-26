<?php

namespace Webkul\PriceDropAlert\Repositories;

use Webkul\Core\Eloquent\Repository;
use Illuminate\Support\Facades\Event;

class EmailTemplateRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return mixed
     */
    function model()
    {
        return 'Webkul\PriceDropAlert\Contracts\EmailTemplate';
    }

    /**
     * @param  array  $data
     * @return \Webkul\Core\Contracts\Channel
     */
    public function create(array $data)
    {
        Event::dispatch('admin.pricedrop.create.before');

        if (isset($data['locale']) && $data['locale'] == 'all') {
            $model = app()->make($this->model());

            foreach (core()->getAllLocales() as $locale) {
                foreach ($model->translatedAttributes as $attribute) {
                    if (isset($data[$attribute])) {
                        $data[$locale->code][$attribute]    = $data[$attribute];
                        $data[$locale->code]['locale']      = $locale->code;
                        $data[$locale->code]['locale_id']   = $locale->id;
                    }
                }
            }
        }
        
        $emailTemplate = $this->model->create($data);

        Event::dispatch('admin.pricedrop.create.after', $emailTemplate);
        
        return $emailTemplate;
    }

    /**
     * @param  array  $data
     * @param  int  $id
     * @param  string  $attribute
     * @return \Webkul\PriceDropAlert\Contracts\EmailTemplate
     */
    public function update(array $data, $id, $attribute = "id")
    {
        Event::dispatch('admin.pricedrop.update.before', $id);
        
        $emailTemplate = $this->find($id);
        
        if (! isset($data['status'])) {
            $data['status'] = 0;
        }

        $emailTemplate->update($data);

        Event::dispatch('admin.pricedrop.update.after', $emailTemplate);

        return $emailTemplate;
    }
}