<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class News extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');

            // CATEGORY ID
            $table->unsignedBigInteger('category_id')->nullable();
            $table->index('category_id');
            $table->foreign('category_id')->references('id')->on('news_category')->onDelete('cascade');

            // SUB CATEGORY ID
            $table->unsignedBigInteger('sub_category_id')->nullable();
            $table->index('sub_category_id');
            $table->foreign('sub_category_id')->references('id')->on('news_sub_category')->onDelete('cascade');

            $table->string('title')->nullable();

            $table->string('slug')->nullable();

            
            $table->binary('description')->nullable();
            $table->string('picture_xl')->nullable();
            $table->string('picture_sm')->nullable();
            $table->string('picture_xs')->nullable();

            // SUB CATEGORY ID
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('city')->nullable();

            // STATUS
            $table->enum('popular', ['0', '1'])->nullable();
            $table->enum('breaking', ['0', '1'])->nullable();
            $table->enum('trending', ['0', '1'])->nullable();

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
        Schema::dropIfExists('news');
    }
}
