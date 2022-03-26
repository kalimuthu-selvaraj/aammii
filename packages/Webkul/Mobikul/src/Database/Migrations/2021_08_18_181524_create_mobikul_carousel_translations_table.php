<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulCarouselTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('mobikul_carousel', 'title')) {
            Schema::table('mobikul_carousel', function (Blueprint $table) {
                $table->dropColumn('title');
                $table->dropColumn('store_id');
            });
        }

        Schema::create('mobikul_carousel_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title')->nullable();
            $table->string('locale');
            $table->string('channel');
            $table->integer('mobikul_carousel_id')->nullable()->unsigned();

            $table->unique(['mobikul_carousel_id', 'locale', 'channel'], 'mobikul_carousel_translations_locale_unique');

            $table->foreign('mobikul_carousel_id', 'mobikul_carousel_id_foreign')->references('id')->on('mobikul_carousel')->onDelete('cascade');
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
        Schema::dropIfExists('mobikul_carousel_translations');
    }
}
