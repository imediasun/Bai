<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('credits', function (Blueprint $table) {
            $table->integer('created_by')->after('changed_by')->nullable();
        });

        Schema::table('credit_props', function (Blueprint $table) {
            $table->integer('changed_by')->after('credit_security')->nullable();
            $table->integer('created_by')->after('credit_security')->nullable();
        });

        Schema::table('credit_prop_fees', function (Blueprint $table) {
            $table->integer('changed_by')->after('input')->nullable();
            $table->integer('created_by')->after('input')->nullable();
        });

        Schema::table('banks', function (Blueprint $table) {
            $table->integer('changed_by')->after('review_overall')->nullable();
            $table->integer('created_by')->after('review_overall')->nullable();
        });

        Schema::table('atms', function (Blueprint $table) {
            $table->integer('changed_by')->after('is_multicurrency')->nullable();
            $table->integer('created_by')->after('is_multicurrency')->nullable();
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->integer('changed_by')->after('product_id')->nullable();
            $table->integer('created_by')->after('product_id')->nullable();
        });

        Schema::table('custom_props', function (Blueprint $table) {
            $table->integer('changed_by')->after('name_ru')->nullable();
            $table->integer('created_by')->after('name_ru')->nullable();
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
