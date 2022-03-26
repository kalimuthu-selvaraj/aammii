<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobikulCarouselImagesProductsPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_carousel_images_products_pivot', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('carousel_id')->nullable()->unsigned();
            $table->foreign('carousel_id')->references('id')->on('mobikul_carousel')->onDelete('cascade');

            $table->integer('carousel_image_id')->nullable()->unsigned();
            $table->foreign('carousel_image_id')->references('id')->on('mobikul_carousel_images')->onDelete('cascade');

            $table->integer('products_id')->nullable()->unsigned();
            $table->foreign('products_id')->references('id')->on('products')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobikul_carousel_images_products_pivot');
    }
}
