<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankWorkSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_work_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bank_id');
            $table->string('type')->default('individual')->comment('Физ или юр лицо: individual/entity');
            $table->boolean('is_dinner_break')->default(false)->comment('Обеденный перерыв');
            $table->string('monday_from')->nullable();
            $table->string('monday_until')->nullable();
            $table->string('tuesday_from')->nullable();
            $table->string('tuesday_until')->nullable();
            $table->string('wednesday_from')->nullable();
            $table->string('wednesday_until')->nullable();
            $table->string('thursday_from')->nullable();
            $table->string('thursday_until')->nullable();
            $table->string('friday_from')->nullable();
            $table->string('friday_until')->nullable();
            $table->string('saturday_from')->nullable();
            $table->string('saturday_until')->nullable();
            $table->string('sunday_from')->nullable();
            $table->string('sunday_until')->nullable();

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
        Schema::dropIfExists('bank_work_schedules');
    }
}
