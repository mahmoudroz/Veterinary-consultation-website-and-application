<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chat_id')->unsigned();
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->bigInteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('clinic_id')->unsigned();
            $table->foreign('clinic_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('status')->comment("{ '1' => 'current' ,  '-1' => 'archive'}")->default(1);
            $table->string('message')->nullable();
            $table->boolean('message_type')->comment("{ '0' => 'text' , '1' => 'file' , '2' => 'image' , '3' => 'record' }")->default(0);
            $table->boolean('sender')->comment("{ '0' => 'user' , '1' => 'clinic }")->nullable();
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
        Schema::dropIfExists('messages');
    }
}
