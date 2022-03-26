<?php

namespace Webkul\Mobikul\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeGroupTableSeeder extends Seeder
{
    public function run()
    {
        $getIsMobikulFeaturedAttribute = DB::table('attributes')->where('code', 'is_mobikul_featured')->first();
        
        $getLastAttributeGroup = DB::table('attribute_groups')->orderBy('id', 'desc')->limit(1)->first();

        $getLastAttributeGroupId = $getLastAttributeGroup->id + 1;

        DB::table('attribute_groups')->insert([
            [
                'id'                  => $getLastAttributeGroupId,
                'name'                => 'Mobikul Configuration',
                'position'            => '3',
                'is_user_defined'     => '0',
                'attribute_family_id' => '1',
            ]
        ]);

        DB::table('attribute_group_mappings')->insert([
            [
                'attribute_id'        => $getIsMobikulFeaturedAttribute->id,
                'attribute_group_id'  => $getLastAttributeGroupId,
                'position'            => '1',
            ]
        ]);
    }
}