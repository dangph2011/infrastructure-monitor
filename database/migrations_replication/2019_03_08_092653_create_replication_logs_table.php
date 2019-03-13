<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplicationLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replication_logs', function (Blueprint $table) {
            $table->string('CHANNEL_NAME', 64)->primary();
            $table->enum('SERVICE_STATE', ['ON', 'OFF', 'CONNECTING'])->default('ON');
            $table->string('LAST_ERROR_MESSAGE', 1024)->nullable();
            $table->string('HOST', 1024)->nullable();
            $table->integer('PORT')->nullable();
            $table->string('USER', 32)->nullable();
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
        Schema::dropIfExists('replication_logs');
    }
}
