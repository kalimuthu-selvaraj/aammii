<?php

namespace Webkul\PriceDropAlert\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(AttributeTableSeeder::class);
        $this->call(AttributeGroupTableSeeder::class);        
    }
}