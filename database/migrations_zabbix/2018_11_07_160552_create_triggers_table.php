<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('triggers', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('triggerid');
            $table->string('expression', 2048)->default('');
            $table->string('description', 255)->default('');
            $table->string('url', 255)->default('');
            $table->integer('status')->default('0');
            $table->integer('value')->default('0');
            $table->integer('priority')->default('0');
            $table->integer('lastchange')->default('0');
            $table->text('comments');
            $table->string('error',2048)->default('');
            $table->integer('state')->default('0');
            $table->integer('flags')->default('0');
            $table->index(["status"], 'triggers_1');
            $table->index(["value", "lastchange"], 'triggers_2');
            $table->index(["templateid"], 'triggers_3');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('triggers');
    }
}
