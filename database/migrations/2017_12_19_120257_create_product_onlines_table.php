<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOnlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_onlines', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_type')->default('credit');
            $table->string('product_id')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('iin')->nullable();
            $table->string('amount')->nullable();
            $table->string('monthly_earnings')->nullable();
            $table->integer('monthly_extinction')->nullable()->default(0)->comment('ежемесячное погашение др кредита');
            $table->boolean('can_confirm')->default(true);
            $table->boolean('has_expiration')->default(false);
            $table->string('birth_date')->nullable();
            $table->string('credit_history')->nullable();
            $table->string('period')->nullable();
            $table->string('city_id')->nullable();
            $table->text('http_referer')->nullable();


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
        Schema::dropIfExists('product_onlines');
    }
}
