<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaqsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faqs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('seo_records_id');
            $table->text('question_ru')->nullable();
            $table->text('question_kz')->nullable();
            $table->text('answer_ru')->nullable();
            $table->text('answer_kz')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_approved')->default(true);


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
        Schema::dropIfExists('faqs');
    }
}
