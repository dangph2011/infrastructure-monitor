<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportsGraphTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reports_graphs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('rgraphid');
            $table->unsignedBigInteger('reportid');
            $table->unsignedBigInteger('graphid');

            $table->index(["reportid"], 'report_idx');

            $table->index(["graphid"], 'graph_idx');

            $table->foreign('reportid', 'report_idx')
                ->references('reportid')->on('reports')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('graphid', 'graph_idx')
                ->references('graphid')->on('graphs')
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
        Schema::dropIfExists('reports_graphs');
    }
}
