<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailTemplateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_template_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->text('name')->nullable();
            $table->text('subject')->nullable();
            $table->text('message')->nullable();

            $table->string('locale');
            $table->integer('locale_id')->nullable()->unsigned();
            
            $table->bigInteger('email_template_id')->unsigned();
            $table->unique(['email_template_id', 'locale']);
            $table->foreign('email_template_id')->references('id')->on('email_templates')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_template_translations');
    }
}
