<?php

namespace App\Http\Controllers;

use App\Models\Passenger;
use App\Models\Trip;
use Illuminate\Http\Request;

class PassengerController extends Controller
{
    public function store(Request $request, Trip $trip)
    {
        $request->validate([
            'seat_id' => 'required|exists:seats,id',
            'nama_penumpang' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat_naik' => 'nullable|string|max:100',
            'alamat_turun' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:255',
        ]);

        abort_if($trip->status === 'ditutup', 403, 'Trip sudah ditutup.');

        Passenger::updateOrCreate(
            ['trip_id' => $trip->id, 'seat_id' => $request->seat_id],
            [
                'nama_penumpang' => $request->nama_penumpang,
                'no_hp' => $request->no_hp,
                'alamat_naik' => $request->alamat_naik,
                'alamat_turun' => $request->alamat_turun,
                'catatan' => $request->catatan,
                'diinput_oleh' => auth()->id(),
            ]
        );

        return back()->with('success', 'Data penumpang disimpan.');
    }

    public function update(Request $request, Trip $trip, Passenger $passenger)
    {
        $request->validate([
            'nama_penumpang' => 'required|string|max:100',
            'no_hp' => 'nullable|string|max:20',
            'alamat_naik' => 'nullable|string|max:100',
            'alamat_turun' => 'nullable|string|max:100',
            'catatan' => 'nullable|string|max:255',
        ]);

        abort_if($trip->status === 'ditutup' && !auth()->user()->hasRole('admin'), 403);
        abort_if($passenger->diinput_oleh !== auth()->id() && !auth()->user()->hasRole('admin'), 403, 'Anda tidak berhak mengedit data ini.');

        $passenger->update($request->only(['nama_penumpang', 'no_hp', 'alamat_naik', 'alamat_turun', 'catatan']));

        return back()->with('success', 'Data penumpang diperbarui.');
    }

    public function destroy(Trip $trip, Passenger $passenger)
    {
        abort_if($trip->status === 'ditutup' && !auth()->user()->hasRole('admin'), 403);
        abort_if($passenger->diinput_oleh !== auth()->id() && !auth()->user()->hasRole('admin'), 403, 'Anda tidak berhak menghapus data ini.');

        $passenger->delete();

        return back()->with('success', 'Penumpang dihapus.');
    }
}
