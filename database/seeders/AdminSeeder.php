<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@jimpitan.com'],
            [
                'name' => 'Administrator Pusat',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
