<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scheduled_calls', function (Blueprint $table) {
            $table->dropForeign(['call_id']);
        });
    }

    public function down(): void
    {
        Schema::table('scheduled_calls', function (Blueprint $table) {
            $table->foreign('call_id')->references('id')->on('calls')->onDelete('set null');
        });
    }
};
