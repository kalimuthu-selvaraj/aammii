<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_contacts', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name');
            $table->string('email');
            $table->text('comment');
            $table->integer('channel_id')->nullable();
            $table->string('telephone')->nullable();
            $table->boolean('email_status')->default(0);

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
        Schema::dropIfExists('mobikul_contacts');
    }
}
