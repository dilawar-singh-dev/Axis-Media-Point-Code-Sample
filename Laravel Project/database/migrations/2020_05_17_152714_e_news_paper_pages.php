<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ENewsPaperPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('e_news_paper_pages', function (Blueprint $table) {
            $table->bigIncrements('id');

            // USER ID
            $table->unsignedBigInteger('e_news_paper_id')->nullable();
            $table->index('e_news_paper_id');
            $table->foreign('e_news_paper_id')->references('id')->on('e_news_paper')->onDelete('cascade');

            $table->string('page_no')->nullable();

            $table->string('image')->nullable();

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
        //
    }
}
