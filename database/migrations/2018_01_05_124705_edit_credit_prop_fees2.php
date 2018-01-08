<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditPropFees2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_prop_fees2', function (Blueprint $table) {
            $table->dropColumn('security');
            $table->dropColumn('security_input');
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->string('security')->nullable()->after('id');
            $table->text('security_input')->nullable()->after('id');
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
