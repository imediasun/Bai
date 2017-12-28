<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('ownership')->nullable();
            $table->text('board_of_directors')->nullable()->comment('совет директоров банка');
            $table->text('corporate_executives')->nullable()->comment('члены правления');
            $table->text('accountant_general')->nullable()->comment('главный бухгалтер');
            $table->double('latitude', 8, 2)->comment('широта');
            $table->double('longitude', 8, 2)->comment('долгота');
            $table->string('address_ru')->nullable();
            $table->string('address_kz')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('fax')->nullable();
            $table->string('license')->nullable();
            $table->string('leader')->nullable();
            $table->string('site_url')->nullable();
            $table->string('account_url')->nullable();
            $table->string('logo')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('alt_name_ru')->nullable();
            $table->string('alt_name_kz')->nullable();
            $table->string('name_ru')->nullable();
            $table->string('name_kz')->nullable();
            $table->string('locative_ru')->nullable();
            $table->string('locative_kz')->nullable();
            $table->string('genitive_ru')->nullable();
            $table->string('genitive_kz')->nullable();
            $table->string('h1_ru')->nullable();
            $table->string('h1_kz')->nullable();
            $table->text('short_description_ru')->nullable();
            $table->text('short_description_kz')->nullable();
            $table->text('about_description_ru')->nullable();
            $table->text('about_description_kz')->nullable();
            $table->text('description_ru')->nullable();
            $table->text('description_kz')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_premium')->default(false);
            $table->boolean('is_main_branch')->default(false);
            $table->string('breadcrumbs_ru')->nullable();
            $table->string('breadcrumbs_kz')->nullable();
            $table->integer('sort_order')->default(10);
            $table->decimal('review_deposit', 3, 2)->default(0.00);
            $table->decimal('review_active', 3, 2)->default(0.00);
            $table->decimal('review_profit', 3, 2)->default(0.00);
            $table->decimal('review_overall', 3, 2)->default(0.00);



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
        Schema::dropIfExists('banks');
    }
}
