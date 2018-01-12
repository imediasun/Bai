<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditTable_2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->integer('occupational_current')->after('occupational_life')->nullable()->comment('стаж работы на текущем месте');
            $table->boolean('have_constant_income')->after('occupational_life')->comment('постоянный доход')->default(1);
            $table->boolean('have_prolongation')->after('occupational_life')->default(1);
            $table->boolean('have_citizenship')->after('occupational_life')->default(1)->nullable();
            $table->string('registration')->nullable()->default('const')->change();
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
