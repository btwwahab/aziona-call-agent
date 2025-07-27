<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduledCallsTable extends Migration
{
    public function up()
    {
        Schema::create('scheduled_calls', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->timestamp('scheduled_for');
            $table->string('note')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('call_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scheduled_calls');
    }
}
