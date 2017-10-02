<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pads', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('heading');
            $table->string('slug');
            $table->string('password')->default('NULL');
            $table->integer('clicks')->default(0);
            $table->enum('status', ['ACTIVE','ARCHIVED','DELETED'])->default('ACTIVE');
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
        Schema::dropIfExists('pads');
    }
}
