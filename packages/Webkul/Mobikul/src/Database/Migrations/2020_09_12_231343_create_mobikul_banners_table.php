<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_banners', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('image')->nullable();
            $table->integer('sort_order')->nullable();
            $table->integer('product_category_id');
            $table->string('type');
            $table->boolean('status')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobikul_banners');
    }
}
