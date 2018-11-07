<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('eventid');
            $table->integer('source')->default('0');
            $table->integer('object')->default('0');
            $table->unsignedBigInteger('objectid')->default('0');
            $table->integer('clock')->default('0');
            $table->integer('value')->default('0');
            $table->integer('acknowledged')->default('0');
            $table->integer('ns')->default('0');

            $table->index(["source", "object", "clock"], 'events_2');

            $table->index(["source", "object", "objectid", "clock"], 'events_1');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
