<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn('age');
            $table->dropColumn('income_project');
            $table->dropColumn('client_type');

            $table->integer('gesv')->after('online_url')->nullable();
        });

        Schema::table('credit_props', function (Blueprint $table) {
            $table->dropColumn('gesv');

            $table->string('age')->after('credit_security')->nullable();
            $table->integer('income_project')->after('credit_security')->nullable()->comment('зарплатный проект');
            $table->string('client_type')->after('credit_security')->nullable()->comment('vip, vip-elite, standart');
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
