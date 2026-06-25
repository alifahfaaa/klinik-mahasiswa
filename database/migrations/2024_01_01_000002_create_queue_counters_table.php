<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->constrained('polis')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('counter')->default(0);
            $table->timestamps();
            $table->unique(['poli_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_counters');
    }
};
