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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soals')->cascadeOnDelete();
            $table->text('soal');
            $table->string('gambar')->nullable();
            $table->string('a')->nullable();
            $table->integer('skor_a')->default(0);
            $table->string('b')->nullable();
            $table->integer('skor_b')->default(0);
            $table->string('c')->nullable();
            $table->integer('skor_c')->default(0);
            $table->string('d')->nullable();
            $table->integer('skor_d')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
