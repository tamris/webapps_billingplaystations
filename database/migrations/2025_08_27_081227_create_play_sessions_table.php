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
        Schema::create('ps_sessions', function (Blueprint $t) {
            $t->id();
            $t->foreignId('playstation_id')->constrained('playstations')->cascadeOnDelete();
            $t->dateTime('started_at');
            $t->dateTime('ended_at')->nullable();
            $t->integer('duration_minutes')->nullable();
            $t->decimal('total_price', 10, 2)->nullable();
            $t->enum('status', ['open', 'closed'])->default('open');
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('play_sessions');
    }
};
