<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMobikulFeaturedCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_featured_categories', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('category_id')->nullable()->unsigned();
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->string('image')->nullable();
            $table->string('sort_order')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });

        Schema::create('mobikul_featured_category_channels', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('featured_category_id')->nullable()->unsigned();
            $table->foreign('featured_category_id')->references('id')->on('mobikul_featured_categories')->onDelete('cascade');

            $table->integer('channel_id')->nullable()->unsigned();
            $table->foreign('channel_id')->references('id')->on('channels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobikul_featured_categories');
        Schema::dropIfExists('mobikul_featured_category_channels');
    }
}
