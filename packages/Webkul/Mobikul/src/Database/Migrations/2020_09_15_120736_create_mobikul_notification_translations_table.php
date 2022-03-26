<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulNotificationTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_notification_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('title')->nullable();
            $table->text('content')->nullable();
            $table->string('locale');
            $table->string('channel');
            $table->bigInteger('mobikul_notification_id')->unsigned();

            $table->unique(['mobikul_notification_id', 'locale', 'channel'], 'mobikul_notification_translations_locale_unique');
            $table->foreign('mobikul_notification_id', 'mnt_mobikul_notification_id_foreign')->references('id')->on('mobikul_notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobikul_notification_translations');
    }
}
