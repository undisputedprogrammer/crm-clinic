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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('email');
            $table->string('city');
            $table->boolean('is_valid');
            $table->boolean('is_genuine');
            $table->text('history');
            $table->text('q_visit')->nullable();
            $table->text('q_decide')->nullable();
            $table->enum('customer_segment',['hot','warm','cold'])->nullable();
            $table->enum('status',['Created','Validated','Converted','Closed','Consulted'])->default('Created');
            $table->boolean('followup_created')->default(false);
            $table->foreignId('assigned_to')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
