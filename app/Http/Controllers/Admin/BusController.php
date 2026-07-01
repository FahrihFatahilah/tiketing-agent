<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bus;
use App\Models\BusType;
use Illuminate\Http\Request;

class BusController extends Controller
{
    public function index()
    {
        $buses = Bus::with('busType')->latest()->get();
        $busTypes = BusType::all();
        return view('admin.buses.index', compact('buses', 'busTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bus_type_id' => 'required|exists:bus_types,id',
            'nomor_lambung' => 'required|string|unique:buses',
        ]);
        Bus::create($request->only(['bus_type_id', 'nomor_lambung']));
        return back()->with('success', 'Armada ditambahkan.');
    }

    public function update(Request $request, Bus $bus)
    {
        $request->validate([
            'bus_type_id' => 'required|exists:bus_types,id',
            'nomor_lambung' => 'required|string|unique:buses,nomor_lambung,' . $bus->id,
        ]);
        $bus->update($request->only(['bus_type_id', 'nomor_lambung']));
        return back()->with('success', 'Armada diperbarui.');
    }

    public function destroy(Bus $bus)
    {
        $bus->delete();
        return back()->with('success', 'Armada dihapus.');
    }
}
