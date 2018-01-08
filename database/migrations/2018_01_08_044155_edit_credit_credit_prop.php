<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditCreditProp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_props', function (Blueprint $table) {
            $table->text('amount_comment')->after('max_amount')->nullable();
            $table->text('currency_comment')->after('currency')->nullable();
            $table->text('period_comment')->after('max_period')->nullable();
            $table->text('percent_rate_comment')->after('percent_rate')->nullable();
            $table->text('age_comment')->after('age')->nullable();
            $table->text('income_confirmation_comment')->after('income_confirmation')->nullable();
            $table->text('credit_security_comment')->after('credit_security')->nullable();
            $table->text('repayment_structure_comment')->after('repayment_structure')->nullable();

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
