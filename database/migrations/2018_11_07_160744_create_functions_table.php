<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFunctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('functions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->bigIncrements('functionid');
            $table->unsignedBigInteger('itemid');
            $table->unsignedBigInteger('triggerid');
            $table->string('function', 12)->default('');
            $table->string('parameter', 255)->default('0');

            $table->index(["triggerid"], 'functions_1');

            $table->index(["itemid", "function", "parameter"], 'functions_2');

            $table->foreign('itemid', 'functions_2')
                ->references('itemid')->on('items')
                ->onDelete('cascade')
                ->onUpdate('restrict');

            $table->foreign('triggerid', 'functions_1')
                ->references('triggerid')->on('triggers')
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
        Schema::dropIfExists('functions');
    }
}
