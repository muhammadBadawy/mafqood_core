<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->default('unknown');
            $table->date('birth')->nullable()->default('0000-00-00');
            $table->date('case_date')->default('0000-00-00');
            $table->integer('area_id')->unsigned();
            $table->integer('lat')->unsigned();
            $table->integer('lang')->unsigned();
            $table->string('mental_condition')->nullable()->default('unknown');
            $table->string('type');
            $table->string('reporter_type')->default('person');
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reports');
    }
}
