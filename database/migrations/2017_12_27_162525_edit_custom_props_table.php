<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCustomPropsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_props', function (Blueprint $table) {
            $table->string('name_ru')->after('bank_id')->nullable();
            $table->string('alt_name_ru')->after('bank_id')->nullable();
            $table->string('value_ru')->after('bank_id')->nullable();
            $table->string('alt_value_ru')->after('bank_id')->nullable();
            $table->text('comment_ru')->after('bank_id')->nullable();
            $table->string('name_kz')->after('bank_id')->nullable();
            $table->string('alt_name_kz')->after('bank_id')->nullable();
            $table->string('value_kz')->after('bank_id')->nullable();
            $table->string('alt_value_kz')->after('bank_id')->nullable();
            $table->text('comment_kz')->after('bank_id')->nullable();

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
