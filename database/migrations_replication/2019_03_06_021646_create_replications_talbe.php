<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplicationsTalbe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 64)->nullable();
            $table->text('description')->nullable();
            $table->string('host', 64);
            $table->string('user', 64);
            $table->string('channel', 64);
            $table->string('state', 64)->nullable();
            $table->text('error')->nullable();
            $table->text('log_text');
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
        Schema::dropIfExists('replications');
    }
}
