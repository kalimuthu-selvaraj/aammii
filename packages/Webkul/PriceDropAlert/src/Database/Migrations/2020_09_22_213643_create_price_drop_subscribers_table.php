<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceDropSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_drop_subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('email');
            $table->integer('product_id');
            $table->decimal('base_price', 12, 4)->nullable();
            $table->boolean('status')->default(0);
            $table->string('token')->nullable();

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
        Schema::dropIfExists('price_drop_subscribers');
    }
}
