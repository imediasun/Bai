<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EditBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banks', function (Blueprint $table) {
            $table->renameColumn('meta_title', 'meta_title_ru');
            $table->text('meta_title_kz')->after('logo')->nullable();

            $table->renameColumn('meta_description', 'meta_description_ru');
            $table->text('meta_description_kz')->after('logo')->nullable();

            $table->renameColumn('meta_keywords', 'meta_keywords_ru');
            $table->text('meta_keywords_kz')->after('logo')->nullable();



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
