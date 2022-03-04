<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AxisTv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('axis_tv', function (Blueprint $table) {
            $table->bigIncrements('id');

            // USER ID
            $table->unsignedBigInteger('user_id')->nullable();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('title')->nullable();
            $table->binary('description')->nullable();
            $table->string('yt_video')->nullable();

            // STATUS
            $table->enum('type', ['citizen_reporter', 'interview', 'news'])->nullable();

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
        Schema::dropIfExists('axis_tv');
    }
}
