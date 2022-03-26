<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulRegisterDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_register_devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('os');
            $table->string('fcmToken');
            $table->integer('customer_id')->unsigned()->nullable();
            $table->unique(['customer_id', 'os', 'fcmToken']);

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
        Schema::dropIfExists('mobikul_register_devices');
    }
}
