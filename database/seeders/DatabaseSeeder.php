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

        // Data warga per RT — RT07: 50, RT08: 45, RT36: 40, RT06: 50
        $rtGroups = [
            ['rt' => '007', 'rw' => '002', 'count' => 50],
            ['rt' => '008', 'rw' => '002', 'count' => 45],
            ['rt' => '036', 'rw' => '002', 'count' => 40],
            ['rt' => '006', 'rw' => '002', 'count' => 50],
        ];

        // Bank nama warga (185 nama unik untuk memenuhi total 185 warga)
        $namaBank = [
            'Budi Santoso','Siti Aminah','Agus Prayitno','Dewi Lestari','Eko Susanto',
            'Rina Wijaya','Bambang Hermawan','Ani Suryani','Dedi Kurniawan','Luluk Fauziah',
            'Heri Setiawan','Maya Indah','Joko Susilo','Yuni Kartika','Aris Munandar',
            'Novi Saputri','Rudi Tabuti','Siska Amelia','Andi Wijaya','Fitriani',
            'Taufik Hidayat','Indah Permata','Zulkifli','Ratna Sari','Hendra Gunawan',
            'Lusi Diana','Ahmad Fauzi','Ria Anjelina','Doni Tata','Santi Clarisa',
            'Iwan Setiawan','Melly Pratiwi','Ari Wibowo','Raisa Putri','Tulus Handoko',
            'Isyana Dewi','Glen Surya','Tompi Prasetyo','Sandy Raharjo','Once Nugroho',
            'Anang Susanto','Ashanty Wijaya','Aurel Permata','Atta Firmansyah','Raffi Santosa',
            'Nagita Lestari','Baim Hartono','Paula Kusuma','Deddy Wibowo','Ivan Gunawan',
            'Surya Dharma','Putri Maharani','Rizky Pratama','Nadia Syafitri','Fajar Nugroho',
            'Citra Dewi','Dian Kurniawati','Wahyu Setiabudi','Leni Susilawati','Hendra Prabowo',
            'Mega Sari','Yoga Praditya','Tiara Wulandari','Bima Sakti','Rani Oktaviani',
            'Dimas Aditya','Lia Ambarwati','Galih Satria','Reni Suryani','Teguh Wicaksono',
            'Ayu Rahayu','Farid Mulyadi','Intan Nuraini','Arief Budiman','Sari Wulandari',
            'Hendro Setiawan','Wulan Sari','Rizal Arifin','Nila Cahyani','Agung Nugroho',
            'Rini Widyawati','Hasan Basri','Fitria Anggraini','Lukman Hakim','Dwi Rahayu',
            'Bayu Adiputra','Sinta Permatasari','Iqbal Ramadhan','Laras Wulandari','Yudi Firmansyah',
            'Esti Nurani','Fauzi Rahmat','Murni Lestari','Sigit Purnomo','Endah Cahyani',
            'Prasetyo Adi','Kartika Sari','Wahyu Hidayat','Nisa Rahma','Adhi Nugroho',
            'Lina Septiani','Wawan Setiawan','Rosi Andriyani','Nur Cahyo','Tri Wahyuni',
            'Joko Purnomo','Yayuk Sulistyawati','Eko Prasetyo','Nur Hayati','Arif Rahman',
            'Desi Ratnasari','Bambang Sugiarto','Susi Anggraeni','Harianto','Ernawati',
            'Mulyadi','Sri Wahyuni','Purwanto','Rahayu Ningsih','Suprapto',
            'Wiyono','Sumarno','Haryono','Sulastri','Sarwono',
            'Supriyono','Wahyudi','Slamet Riyadi','Sunarto','Sukiman',
            'Hartono','Suparman','Sulistyo','Karno','Riyanto',
            'Marsudi','Sumadi','Marjono','Jumadi','Sarjono',
            'Supono','Hartini','Sriyanti','Mujiati','Sutini',
            'Tuginem','Rusmini','Suharni','Partini','Warni',
            'Suwarto','Suyanto','Sutrisno','Mulyono','Supandi',
            'Sariono','Narimo','Sarpono','Suroto','Warjono',
            'Purnomo','Suharto','Tugiyo','Sukamto','Widodo',
            'Susanto','Margono','Sunarjo','Bejo','Sujono',
            'Trisno','Marno','Jarwanto','Karsono','Parjono',
            'Supardi','Waluyo','Subardi','Maryanto','Sunaryo',
        ];

        $globalIndex = 0;
        foreach ($rtGroups as $group) {
            for ($i = 0; $i < $group['count']; $i++) {
                $nama = $namaBank[$globalIndex % count($namaBank)];
                // Buat nama unik dengan suffix jika sudah dipakai
                $suffix = $globalIndex >= count($namaBank) ? ' ' . (intdiv($globalIndex, count($namaBank)) + 1) : '';

                \App\Models\Warga::create([
                    'nik'           => '3201' . $group['rt'] . str_pad($globalIndex + 1, 8, '0', STR_PAD_LEFT),
                    'nama'          => $nama . $suffix,
                    'alamat'        => 'Jl. RT ' . ltrim($group['rt'], '0') . ' No. ' . ($i + 1),
                    'rt'            => $group['rt'],
                    'rw'            => $group['rw'],
                    'no_hp'         => '0812' . str_pad($globalIndex + 1, 8, '0', STR_PAD_LEFT),
                    'saldo'         => rand(1000, 50000),
                    'qr_code_string'=> 'QR-RT' . ltrim($group['rt'], '0') . '-' . str_pad($globalIndex + 1, 4, '0', STR_PAD_LEFT),
                ]);

                $globalIndex++;
            }
        }
    }
}

