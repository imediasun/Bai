<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreditPropFees2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credit_prop_fees2', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('credit_id')->nullable();
            $table->integer('credit_prop_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('changed_by')->nullable();

            $table->string('review')->nullable()->comment('за рассмотрение');
            $table->string('organization')->nullable()->comment('за организацию');
            $table->string('card_account_enrolment')->nullable()->comment('за зачисление на карту/счет');
            $table->string('monetisation')->nullable()->comment('за обналичивание');
            $table->string('service')->nullable()->comment('за обслуживание');
            $table->string('granting')->nullable()->comment('за выдачу кредита');
            $table->string('security')->nullable()->comment('за страхование');

            $table->string('review_input')->nullable();
            $table->string('organization_input')->nullable();
            $table->string('card_account_enrolment_input')->nullable();
            $table->string('monetisation_input')->nullable();
            $table->string('service_input')->nullable();
            $table->string('granting_input')->nullable();
            $table->string('security_input')->nullable();

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
        //
    }
}
