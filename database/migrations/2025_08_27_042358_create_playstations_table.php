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
        Schema::create('playstations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();          // PS1, PS2, dsb
            $table->string('name', 100)->nullable();       // nama/label meja
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available');
            $table->decimal('price_per_hour', 10, 2)->default(0); // sementara di sini dulu (tar bisa dipindah ke tariffs)
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('playstations');
    }
};
