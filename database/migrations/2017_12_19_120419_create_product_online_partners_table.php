<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductOnlinePartnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_online_partners', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('partner_id')->nullable();
            $table->string('type')->comment('bank, shark, etc');
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->boolean('is_active')->default(true);

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
        Schema::dropIfExists('product_online_partners');
    }
}
