<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateStampsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamps', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->longText('print')->nullable();
            $table->string('image')->nullable();
            $table->longText('bbox')->nullable();
            $table->integer('report_id')->unsigned();
            $table->integer('suspect_id')->unsigned()->default(0);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('stamps');
    }
}
