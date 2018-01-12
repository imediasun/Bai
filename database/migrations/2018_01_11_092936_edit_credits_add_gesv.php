<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditsAddGesv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credit_props', function (Blueprint $table) {
            $table->double('gesv')->after('age_comment')->nullable();
            $table->text('gesv_comment')->after('age_comment')->nullable();
        });

        Schema::table('credits', function (Blueprint $table) {
            $table->dropColumn(['gesv', 'gesv_comment']);
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
