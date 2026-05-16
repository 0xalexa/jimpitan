<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warga extends Model
{
    protected $fillable = ['nik', 'nama', 'alamat', 'rt', 'rw', 'no_hp', 'saldo', 'tunggakan', 'qr_code_string'];

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}
