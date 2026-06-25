<?php

namespace Database\Seeders;

use App\Models\Poli;
use App\Models\Queue;
use App\Models\QueueCounter;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Users ────────────────────────────────────────────────────────────
        User::firstOrCreate(['username' => 'admin'], [
            'name'          => 'Administrator',
            'email'         => 'admin@klinik.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'admin',
            'program_studi' => null,
        ]);

        User::firstOrCreate(['username' => 'staff01'], [
            'name'          => 'Dr. Andi Santoso',
            'email'         => 'andi@klinik.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'staff',
            'program_studi' => null,
        ]);

        User::firstOrCreate(['username' => 'staff02'], [
            'name'          => 'Ns. Sari Dewi',
            'email'         => 'sari@klinik.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'staff',
            'program_studi' => null,
        ]);

        User::firstOrCreate(['username' => '1234567890'], [
            'name'          => 'Budi Santoso',
            'email'         => 'budi@mahasiswa.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'mahasiswa',
            'program_studi' => 'Teknik Informatika',
        ]);

        User::firstOrCreate(['username' => '9876543210'], [
            'name'          => 'Siti Aminah',
            'email'         => 'siti@mahasiswa.ac.id',
            'password'      => Hash::make('password'),
            'role'          => 'mahasiswa',
            'program_studi' => 'Manajemen',
        ]);

        // ─── Poli ─────────────────────────────────────────────────────────────
        $polis = [
            ['kode' => 'A', 'nama' => 'Poli Umum',  'lokasi_ruang' => 'Ruang 101'],
            ['kode' => 'B', 'nama' => 'Poli Anak',  'lokasi_ruang' => 'Ruang 102'],
            ['kode' => 'C', 'nama' => 'Poli Gigi',  'lokasi_ruang' => 'Ruang 103'],
            ['kode' => 'D', 'nama' => 'Poli Mata',  'lokasi_ruang' => 'Ruang 104'],
        ];

        foreach ($polis as $p) {
            Poli::firstOrCreate(['kode' => $p['kode']], array_merge($p, ['is_active' => true]));
        }

        // ─── Demo Antrian Hari Ini ─────────────────────────────────────────────
        $today   = now()->toDateString();
        $poliUmum = Poli::where('kode', 'A')->first();
        $poliAnak = Poli::where('kode', 'B')->first();
        $poliGigi = Poli::where('kode', 'C')->first();

        if (Queue::whereDate('tanggal_antrian', $today)->count() === 0) {
            $demoQueues = [
                ['poli' => $poliUmum, 'nim' => '2021001', 'nama' => 'Budi Santoso',   'status' => 'serving', 'no' => 'A-042'],
                ['poli' => $poliUmum, 'nim' => '2021002', 'nama' => 'Ibu Siti Aminah','status' => 'waiting', 'no' => 'A-043'],
                ['poli' => $poliUmum, 'nim' => '2021003', 'nama' => 'Tn. Hendra W',   'status' => 'done',    'no' => 'A-041'],
                ['poli' => $poliAnak, 'nim' => '2021004', 'nama' => 'An. Kevin P',     'status' => 'waiting', 'no' => 'B-015'],
                ['poli' => $poliAnak, 'nim' => '2021005', 'nama' => 'An. Rani K',      'status' => 'waiting', 'no' => 'B-016'],
                ['poli' => $poliAnak, 'nim' => '2021006', 'nama' => 'An. Doni S',      'status' => 'waiting', 'no' => 'B-017'],
                ['poli' => $poliAnak, 'nim' => '2021007', 'nama' => 'An. Lisa M',      'status' => 'waiting', 'no' => 'B-018'],
                ['poli' => $poliGigi, 'nim' => '2021008', 'nama' => 'Ny. Rina Kusuma', 'status' => 'done',    'no' => 'C-008'],
                ['poli' => $poliGigi, 'nim' => '2021009', 'nama' => 'Tn. Eko Budi',    'status' => 'waiting', 'no' => 'C-009'],
                ['poli' => $poliGigi, 'nim' => '2021010', 'nama' => 'Ny. Dewi Sari',   'status' => 'waiting', 'no' => 'C-010'],
                ['poli' => $poliGigi, 'nim' => '2021011', 'nama' => 'Tn. Agus P',      'status' => 'waiting', 'no' => 'C-011'],
            ];

            foreach ($demoQueues as $q) {
                Queue::create([
                    'queue_number'    => $q['no'],
                    'poli_id'         => $q['poli']->id,
                    'nim'             => $q['nim'],
                    'nama_pasien'     => $q['nama'],
                    'program_studi'   => 'Teknik Informatika',
                    'keluhan'         => 'Sakit kepala dan demam ringan',
                    'status'          => $q['status'],
                    'estimated_time'  => now()->addMinutes(rand(10, 60))->format('H:i'),
                    'tanggal_antrian' => $today,
                    'served_at'       => $q['status'] === 'serving' ? now()->subMinutes(10) : null,
                    'done_at'         => $q['status'] === 'done' ? now()->subMinutes(30) : null,
                ]);
            }

            // Update counters
            QueueCounter::updateOrCreate(
                ['poli_id' => $poliUmum->id, 'tanggal' => $today],
                ['counter' => 43]
            );
            QueueCounter::updateOrCreate(
                ['poli_id' => $poliAnak->id, 'tanggal' => $today],
                ['counter' => 18]
            );
            QueueCounter::updateOrCreate(
                ['poli_id' => $poliGigi->id, 'tanggal' => $today],
                ['counter' => 11]
            );
        }
    }
}
