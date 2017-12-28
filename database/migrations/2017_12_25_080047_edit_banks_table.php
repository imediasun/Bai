<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->double('rating_assets')->after('about_description_kz')->nullable();
            $table->double('rating_profit')->after('about_description_kz')->nullable();
            $table->double('rating_deposit')->after('about_description_kz')->nullable();
            $table->double('rating_client')->after('about_description_kz')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
