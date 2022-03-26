<?php

namespace Webkul\Mobikul\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeTableSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $getLocales = DB::table('locales')->get();
        
        $getLastAttribute = DB::table('attributes')->orderBy('id', 'desc')->limit(1)->first();
        
        $getLastAttributeId = $getLastAttribute->id + 1;
        
        DB::table('attributes')->insert([
            [
                'id'                  => $getLastAttributeId,
                'code'                => 'is_mobikul_featured',
                'admin_name'          => 'Is featured for Mobikul ?',
                'type'                => 'boolean',
                'validation'          => NULL,
                'position'            => $getLastAttributeId,
                'is_required'         => '0',
                'is_unique'           => '0',
                'value_per_locale'    => '0',
                'value_per_channel'   => '0',
                'is_filterable'       => '0',
                'is_configurable'     => '0',
                'is_user_defined'     => '0',
                'is_visible_on_front' => '0',
                'use_in_flat'         => '1',
                'created_at'          => $now,
                'updated_at'          => $now,
                'is_comparable'       => '0',
            ]
        ]);

        $getLastAttributeTranslation = DB::table('attribute_translations')->orderBy('id', 'desc')->limit(1)->first();
        
        $attributeTranslations = [];
        foreach($getLocales as $key => $locale) {

            $attributeTranslations[] = [
                'id'           => $getLastAttributeTranslation->id + ($key + 1),
                'locale'       => $locale->code,
                'name'         => 'Is featured for Mobikul ?',
                'attribute_id' => $getLastAttributeId,
            ];
        }

        DB::table('attribute_translations')->insert($attributeTranslations);
    }
}