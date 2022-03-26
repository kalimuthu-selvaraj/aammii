<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulBannerTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_banner_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->text('name')->nullable();
            $table->string('channel')->nullable();
            $table->string('locale')->nullable();
            
            $table->bigInteger('mobikul_banner_id')->unsigned();
            $table->unique(['mobikul_banner_id', 'channel', 'locale'], 'mobikul_banner_translations_locale_unique');
            $table->foreign('mobikul_banner_id', 'mobikul_banner_translations_locale_foreign')->references('id')->on('mobikul_banners')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobikul_banner_translations');
    }
}
