<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventRecoveryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_recovery', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('eventid');
            $table->unsignedBigInteger('r_eventid');
            $table->unsignedBigInteger('c_eventid')->nullable()->default(null);
            $table->unsignedBigInteger('correlationid')->nullable()->default(null);
            $table->unsignedBigInteger('userid')->nullable()->default(null);

            $table->index(["c_eventid"], 'event_recovery_2');

            $table->index(["r_eventid"], 'event_recovery_1');

            $table->foreign('eventid', 'event_recovery_eventid')
                ->references('eventid')->on('events')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('r_eventid', 'event_recovery_1')
                ->references('eventid')->on('events')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('c_eventid', 'event_recovery_2')
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
        Schema::dropIfExists('event_recovery');
    }
}
