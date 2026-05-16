<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::query();

        if ($request->has('rt') && $request->rt != '') {
            $query->where('rt', $request->rt);
        }
        if ($request->has('rw') && $request->rw != '') {
            $query->where('rw', $request->rw);
        }

        $wargas = $query->get();
        $totalSaldoFiltered = $query->sum('saldo');
        
        $rtList = Warga::whereNotNull('rt')->distinct()->pluck('rt')->sort();
        $rwList = Warga::whereNotNull('rw')->distinct()->pluck('rw')->sort();

        return view('warga.index', compact('wargas', 'rtList', 'rwList', 'totalSaldoFiltered'));
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
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
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
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
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
