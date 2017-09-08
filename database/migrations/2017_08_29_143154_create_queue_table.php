<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('queue', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('status')->default(0);
            $table->string('name',100)->nullable();
            $table->string('mobile',125)->nullable();
            $table->string(' position',100)->nullable();
            $table->bigInteger('start_time');
            $table->bigInteger('end_time');
            $table->bigInteger('expires_at')->default(0);
            $table->string('start',100);
            $table->string('end',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
