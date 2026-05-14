<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;

class WargaController extends Controller
{
    public function index()
    {
        $wargas = Warga::all();
        return view('warga.index', compact('wargas'));
    }

    public function create()
    {
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nik' => 'required|unique:wargas',
            'nama' => 'required',
            'alamat' => 'nullable',
            'no_hp' => 'nullable',
            'saldo' => 'required|numeric|min:0',
        ]);

        $data['qr_code_string'] = 'JMP-' . $data['nik'] . '-' . time();

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Warga berhasil ditambahkan.');
    }

    public function show(Warga $warga)
    {
        return view('warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        $data = $request->validate([
            'nik' => 'required|unique:wargas,nik,' . $warga->id,
            'nama' => 'required',
            'alamat' => 'nullable',
            'no_hp' => 'nullable',
            'saldo' => 'required|numeric|min:0',
        ]);

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diupdate.');
    }

    public function destroy(Warga $warga)
    {
        $warga->delete();
        return redirect()->route('warga.index')->with('success', 'Warga berhasil dihapus.');
    }
}
