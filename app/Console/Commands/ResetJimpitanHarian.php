<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:reset-jimpitan-harian')]
#[Description('Menambah tunggakan bagi warga yang belum di-scan hari ini dan me-reset status harian')]
class ResetJimpitanHarian extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wargaBelumScan = \App\Models\Warga::where('status_harian', 0)->get();
        
        foreach ($wargaBelumScan as $warga) {
            $warga->increment('tunggakan', 500);
        }

        \App\Models\Warga::query()->update(['status_harian' => 0]);

        $this->info('Reset jimpitan harian selesai. Tunggakan ditambahkan untuk ' . $wargaBelumScan->count() . ' warga.');
    }
}
