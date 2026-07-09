<?php

namespace App\Http\Controllers;

use App\Models\Baggage;
use App\Models\Trip;
use Illuminate\Http\Request;

class BaggageController extends Controller
{
    public function index(Trip $trip)
    {
        $trip->load(['schedule.route', 'bus.busType', 'baggages.inputBy']);

        return view('baggage.index', compact('trip'));
    }

    public function store(Request $request, Trip $trip)
    {
        $request->validate([
            'nama_pengirim'  => 'required|string|max:100',
            'no_hp_pengirim' => 'nullable|string|max:20',
            'nama_penerima'  => 'required|string|max:100',
            'no_hp_penerima' => 'nullable|string|max:20',
            'jenis_barang'   => 'required|string|max:100',
            'keterangan'     => 'nullable|string|max:255',
            'jumlah'         => 'required|integer|min:1',
        ]);

        $trip->baggages()->create([
            ...$request->only(['nama_pengirim', 'no_hp_pengirim', 'nama_penerima', 'no_hp_penerima', 'jenis_barang', 'keterangan', 'jumlah']),
            'diinput_oleh' => auth()->id(),
        ]);

        return back()->with('success', 'Data bagasi disimpan.');
    }

    public function update(Request $request, Trip $trip, Baggage $baggage)
    {
        $request->validate([
            'nama_pengirim'  => 'required|string|max:100',
            'no_hp_pengirim' => 'nullable|string|max:20',
            'nama_penerima'  => 'required|string|max:100',
            'no_hp_penerima' => 'nullable|string|max:20',
            'jenis_barang'   => 'required|string|max:100',
            'keterangan'     => 'nullable|string|max:255',
            'jumlah'         => 'required|integer|min:1',
        ]);

        $baggage->update($request->only(['nama_pengirim', 'no_hp_pengirim', 'nama_penerima', 'no_hp_penerima', 'jenis_barang', 'keterangan', 'jumlah']));

        return back()->with('success', 'Data bagasi diperbarui.');
    }

    public function destroy(Trip $trip, Baggage $baggage)
    {
        $baggage->delete();
        return back()->with('success', 'Data bagasi dihapus.');
    }
}
