<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->bigInteger('user_id');
            $table->string('token',45);
            $table->string('ip',45);
            $table->integer('client');
            $table->bigInteger('created_at');
            $table->bigInteger('updated_at');
            $table->bigInteger('expires_at');
            $table->primary(['user_id','client']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tokens');
    }
}
