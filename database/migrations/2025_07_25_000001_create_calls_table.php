<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number');
            $table->enum('type', ['outbound', 'inbound']);
            $table->enum('status', ['initiated', 'in_progress', 'completed', 'failed', 'dropped', 'scheduled']);
            $table->integer('duration')->nullable(); // seconds
            $table->text('transcript')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
