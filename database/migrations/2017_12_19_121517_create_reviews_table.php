<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->nullable();
            $table->integer('category_id')->nullable();
            $table->string('subject')->nullable();
            $table->string('author')->nullable();
            $table->string('email')->nullable();
            $table->string('city')->nullable();
            $table->string('product_type')->nullable();
            $table->integer('product_id')->nullable();
            $table->decimal('overall', 3, 2)->default(0.00);
            $table->text('review')->nullable();
            $table->text('hidden_review')->nullable();
            $table->string('url')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_admin')->default(false);
            $table->string('type')->default('common')->comment('common, bad, good');

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
        Schema::dropIfExists('reviews');
    }
}
