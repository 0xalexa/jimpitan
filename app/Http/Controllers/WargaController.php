<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Warga;

class WargaController extends Controller
{
    public function index(Request $request)
    {
        $query = Warga::query();

        $user = auth()->user();
        if ($user->role === 'sekretaris') {
            $query->where('rt', $user->rt);
        } else {
            if ($request->has('rt') && $request->rt != '') {
                $query->where('rt', $request->rt);
            }
        }
        
        if ($request->has('rw') && $request->rw != '') {
            $query->where('rw', $request->rw);
        }

        $wargas = $query->get();
        $totalSaldoFiltered = $query->sum('saldo');
        
        if ($user->role === 'sekretaris') {
            $rtList = collect([$user->rt]);
        } else {
            $rtList = Warga::whereNotNull('rt')->distinct()->pluck('rt')->sort();
        }
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

        $user = auth()->user();
        if ($user->role === 'sekretaris') {
            $data['rt'] = $user->rt;
        }

        $data['qr_code_string'] = 'JMP-' . $data['nik'] . '-' . time();

        Warga::create($data);

        return redirect()->route('warga.index')->with('success', 'Warga berhasil ditambahkan.');
    }

    public function show(Warga $warga)
    {
        if (auth()->user()->role === 'sekretaris' && $warga->rt !== auth()->user()->rt) abort(403);
        return view('warga.show', compact('warga'));
    }

    public function edit(Warga $warga)
    {
        if (auth()->user()->role === 'sekretaris' && $warga->rt !== auth()->user()->rt) abort(403);
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, Warga $warga)
    {
        if (auth()->user()->role === 'sekretaris' && $warga->rt !== auth()->user()->rt) abort(403);
        
        $data = $request->validate([
            'nik' => 'required|unique:wargas,nik,' . $warga->id,
            'nama' => 'required',
            'alamat' => 'nullable',
            'rt' => 'nullable|string|max:3',
            'rw' => 'nullable|string|max:3',
            'no_hp' => 'nullable',
            'saldo' => 'required|numeric|min:0',
        ]);

        if (auth()->user()->role === 'sekretaris') {
            $data['rt'] = auth()->user()->rt;
        }

        $warga->update($data);

        return redirect()->route('warga.index')->with('success', 'Data warga berhasil diupdate.');
    }

    public function destroy(Warga $warga)
    {
        if (auth()->user()->role === 'sekretaris' && $warga->rt !== auth()->user()->rt) abort(403);
        $warga->delete();
        return redirect()->route('warga.index')->with('success', 'Warga berhasil dihapus.');
    }
}
