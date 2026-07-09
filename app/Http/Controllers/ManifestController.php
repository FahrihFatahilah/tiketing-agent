<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ManifestController extends Controller
{
    public function show(Trip $trip, Request $request)
    {
        $trip->load(['schedule.route', 'bus.busType', 'passengers.seat', 'passengers.inputBy']);

        $passengers = $trip->passengers()
            ->with(['seat', 'inputBy'])
            ->when($request->search, fn($q) => $q->where('nama_penumpang', 'like', "%{$request->search}%"))
            ->orderBy('seat_id')
            ->get();

        return view('manifest.show', compact('trip', 'passengers'));
    }

    public function pdf(Trip $trip)
    {
        $trip->load(['schedule.route', 'bus', 'passengers.seat', 'passengers.inputBy']);
        $passengers = $trip->passengers()->with(['seat', 'inputBy'])->orderBy('seat_id')->get();

        $pdf = Pdf::loadView('manifest.pdf', compact('trip', 'passengers'))
            ->setPaper('a4', 'portrait');

        $filename = 'manifest-' . $trip->schedule->route->name . '-' . $trip->tanggal_berangkat->format('d-m-Y') . '.pdf';

        return $pdf->download($filename);
    }
}
