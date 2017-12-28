<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->string('locative_ru')->after('is_approved')->nullable();
            $table->string('locative_kz')->after('locative_ru')->nullable();
            $table->string('genitive_ru')->after('locative_kz')->nullable();
            $table->string('genitive_kz')->after('genitive_ru')->nullable();
            $table->integer('sort_order')->after('genitive_kz')->default(10);
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
