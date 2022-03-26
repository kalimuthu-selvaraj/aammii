<?php

namespace Webkul\PriceDropAlert\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupTableSeeder extends Seeder
{
    public function run()
    {
        $getPriceDropAttribute = DB::table('attributes')->where('code', 'price_drop_alert')->first();
        
        $getLastAttributeGroup = DB::table('attribute_groups')->orderBy('id', 'desc')->limit(1)->first();

        $getLastAttributeGroupId = $getLastAttributeGroup->id + 1;

        DB::table('attribute_groups')->insert([
            [
                'id'                  => $getLastAttributeGroupId,
                'name'                => 'Price Drop Alert',
                'position'            => '3',
                'is_user_defined'     => '0',
                'attribute_family_id' => '1',
            ]
        ]);

        DB::table('attribute_group_mappings')->insert([
            [
                'attribute_id'        => $getPriceDropAttribute->id,
                'attribute_group_id'  => $getLastAttributeGroupId,
                'position'            => '1',
            ]
        ]);
    }
}