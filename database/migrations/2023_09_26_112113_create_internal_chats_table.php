<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('internal_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users', 'id');
            $table->string('target_channel_id', 12);
            $table->text('message')->nullable();
            $table->timestamp('created_at', 0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_chats');
    }
};
