<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->integer('minimum_income')->after('promo')->nullable();
            $table->integer('occupational_life')->after('promo')->nullable()->comment('стаж работы');
            $table->string('method_of_repayment_ru')->after('promo')->nullable()->comment('способ погашения');
            $table->string('method_of_repayment_kz')->after('promo')->nullable()->comment('способ погашения');
            $table->text('docs_ru')->after('promo')->nullable();
            $table->text('docs_kz')->after('promo')->nullable();
            $table->text('other_claims_ru')->after('promo')->nullable()->comment('другие требования');
            $table->text('other_claims_kz')->after('promo')->nullable();
            $table->boolean('have_mobile_phone')->after('promo')->default(true);
            $table->boolean('have_early_repayment')->after('promo')->default(true)->comment('досрочное погашение');
            $table->string('debtor_category')->after('promo')->comment('категория заемщика')->nullable();
            $table->string('credit_goal')->after('promo')->comment('цель кредита')->nullable();
            $table->string('age')->after('promo')->nullable();
            $table->string('receive_mode')->after('promo')->nullable()->comment('способ выдачи');
            $table->boolean('registration')->after('promo')->nullable()->default(true);
            $table->string('time_for_consideration')->after('promo')->nullable()->comment('срок рассмотрения');
            $table->integer('income_project')->after('promo')->nullable()->comment('зарплатный проект');
            $table->string('credit_history')->after('promo')->nullable()->comment('кредитная история');
            $table->string('credit_formalization')->after('promo')->nullable()->comment('оформление кредита');
            $table->string('client_type')->after('promo')->nullable();
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
