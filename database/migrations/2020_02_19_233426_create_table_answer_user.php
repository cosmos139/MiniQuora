<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAnswerUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answer_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("answer_id");
            $table->unsignedSmallInteger("vote");
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users");
            $table->foreign("answer_id")->references("id")->on("answers");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answer_user');
    }
}
