<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_ratings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id')->nullable();
            $table->string('bank_name');
            $table->string('type');
            $table->string('date_select');
            $table->decimal('amount', 10, 2);
            $table->decimal('growth_month', 10, 2);
            $table->decimal('growth_year_begin', 10, 2);


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
        Schema::dropIfExists('bank_ratings');
    }
}
