<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PageInterfaceConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_interface_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('video_id');
            $table->integer('share_times');
            $table->boolean('ad_top_show');
            $table->boolean('ad_bottom_show');
            $table->boolean('ad_back_show');
            $table->boolean('ad_author_show');
            $table->boolean('ad_original_show');
            $table->string('description');
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
        Schema::drop('page_interface_settings');
    }
}
