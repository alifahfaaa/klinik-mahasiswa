<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->string('queue_number', 10);   // e.g. A-023
            $table->foreignId('poli_id')->constrained('polis')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('nim', 20)->nullable();
            $table->string('nama_pasien');
            $table->string('program_studi')->nullable();
            $table->text('keluhan')->nullable();
            $table->enum('status', ['waiting', 'serving', 'done', 'skipped'])->default('waiting');
            $table->time('estimated_time')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->date('tanggal_antrian');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
