<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VideoTitleGenerator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_title_template', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->string('prefix');
            $table->string('core');
            $table->string('suffix');
            $table->string('video_code');
            $table->string('template');
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
        Schema::drop('video_title_template');
    }
}
