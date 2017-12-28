<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeoRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('url');
            $table->boolean('is_unique_seo_data')->default(false);
            $table->string('title_ru')->nullable();
            $table->string('title_kz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();
            $table->text('header_title_ru')->nullable();
            $table->text('header_title_kz')->nullable();
            $table->text('meta_description_ru')->nullable();
            $table->text('meta_description_kz')->nullable();
            $table->text('breadcrumbs_ru')->nullable();
            $table->text('breadcrumbs_kz')->nullable();
            $table->text('full_description_ru')->nullable();
            $table->text('full_description_kz')->nullable();

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
        Schema::dropIfExists('seo_records');
    }
}
