<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Admin RT',
            'email' => 'admin@jimpitan.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        \App\Models\User::create([
            'name' => 'Petugas 1',
            'email' => 'petugas@jimpitan.com',
            'password' => bcrypt('password'),
            'role' => 'petugas',
        ]);

        $wargaNames = [
            'Budi Santoso', 'Siti Aminah', 'Agus Prayitno', 'Dewi Lestari', 'Eko Susanto',
            'Rina Wijaya', 'Bambang Hermawan', 'Ani Suryani', 'Dedi Kurniawan', 'Luluk Fauziah',
            'Heri Setiawan', 'Maya Indah', 'Joko Susilo', 'Yuni Kartika', 'Aris Munandar',
            'Novi Saputri', 'Rudi Tabuti', 'Siska Amelia', 'Andi Wijaya', 'Fitriani',
            'Taufik Hidayat', 'Indah Permata', 'Zulkifli', 'Ratna Sari', 'Hendra Gunawan',
            'Lusi Diana', 'Ahmad Fauzi', 'Ria Anjelina', 'Doni Tata', 'Santi Clarisa',
            'Iwan Fals', 'Melly Goeslaw', 'Ari Lasso', 'Raisa Adriana', 'Tulus',
            'Isyana Sarasvati', 'Glenn Fredly', 'Tompi', 'Sandhy Sondoro', 'Once Mekel',
            'Anang Hermansyah', 'Ashanty', 'Aurel Hermansyah', 'Atta Halilintar', 'Raffi Ahmad',
            'Nagita Slavina', 'Baim Wong', 'Paula Verhoeven', 'Deddy Corbuzier', 'Ivan Gunawan'
        ];

        foreach ($wargaNames as $index => $name) {
            $rtNumber = str_pad(rand(1, 4), 3, '0', STR_PAD_LEFT);
            \App\Models\Warga::create([
                'nik' => '320101' . str_pad($index + 10, 10, '0', STR_PAD_LEFT),
                'nama' => $name,
                'alamat' => 'Jl. Desa No. ' . ($index + 1),
                'rt' => $rtNumber,
                'rw' => '002',
                'no_hp' => '081234567' . str_pad($index, 3, '0', STR_PAD_LEFT),
                'saldo' => rand(500, 50000),
                'qr_code_string' => 'QR-' . strtoupper(substr($name, 0, 3)) . '-' . (1000 + $index),
            ]);
        }
    }
}
