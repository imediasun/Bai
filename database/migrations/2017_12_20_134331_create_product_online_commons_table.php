<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOnlineCommonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_online_commons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('product_type')->default('credit');
            $table->text('product_ids')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('middlename')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('iin')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('monthly_earnings')->nullable();
            $table->integer('monthly_extinction')->nullable();
            $table->boolean('can_confirm')->default(true);
            $table->boolean('has_expiration')->default(false);
            $table->boolean('has_pignoration')->default(false);
            $table->string('birth_date')->nullable();
            $table->string('credit_history')->nullable();
            $table->string('period')->nullable();
            $table->string('city')->nullable();
            $table->string('uid')->nullable();
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
        Schema::dropIfExists('product_online_commons');
    }
}
