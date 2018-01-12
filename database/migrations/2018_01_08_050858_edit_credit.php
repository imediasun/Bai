<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCredit_2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->text('gesv_comment')->after('gesv')->nullable();
            $table->text('time_for_consideration_comment')->after('time_for_consideration')->nullable();
            $table->text('have_early_repayment_comment')->after('have_early_repayment')->nullable();
            $table->text('occupational_life_comment')->after('occupational_life')->nullable();
            $table->text('minimum_income_comment')->after('minimum_income')->nullable();
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
