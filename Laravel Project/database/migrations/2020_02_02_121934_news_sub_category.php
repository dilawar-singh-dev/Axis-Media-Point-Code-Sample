<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class NewsSubCategory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_sub_category', function (Blueprint $table) {
            $table->bigIncrements('id');

            // CATEGORY ID
            $table->unsignedBigInteger('category_id')->nullable();
            $table->index('category_id');
            $table->foreign('category_id')->references('id')->on('news_category')->onDelete('cascade');

            //  SUB CATEGORY ID
            $table->string('name');
            // RECORD TIME STAMP
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_sub_category');
    }
}
