<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMobikulCustomCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobikul_custom_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->text('name');
            $table->boolean('status')->default(0);
            $table->string('product_collection');
            $table->json('product_ids')->nullable();
            $table->integer('latest_count')->nullable();
            $table->string('attributes')->nullable();
            $table->decimal('price_from', 12, 4)->default(0)->nullable();
            $table->decimal('price_to', 12, 4)->default(0)->nullable();
            $table->integer('brand')->nullable();
            $table->string('sku')->nullable();

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
        Schema::dropIfExists('mobikul_custom_collections');
    }
}
