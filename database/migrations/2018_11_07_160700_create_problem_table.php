<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProblemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problem', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('eventid');
            $table->integer('source')->default('0');
            $table->integer('object')->default('0');
            $table->unsignedBigInteger('objectid')->default('0');
            $table->integer('clock')->default('0');
            $table->integer('ns')->default('0');
            $table->unsignedBigInteger('r_eventid')->nullable()->default(null);
            $table->integer('r_clock')->default('0');
            $table->integer('r_ns')->default('0');
            $table->unsignedBigInteger('correlationid')->nullable()->default(null);
            $table->unsignedBigInteger('userid')->nullable()->default(null);

            $table->index(["source", "object", "objectid"], 'problem_1');

            $table->index(["r_eventid"], 'problem_3');

            $table->index(["r_clock"], 'problem_2');

            $table->foreign('eventid', 'problem_eventid')
                ->references('eventid')->on('events')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('r_eventid', 'problem_3')
                ->references('eventid')->on('events')
                ->onDelete('cascade')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problem');
    }
}
