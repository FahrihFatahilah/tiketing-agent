<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Trip;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));

        [$year, $month] = explode('-', $bulan);

        $trips = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->whereYear('tanggal_berangkat', $year)
            ->whereMonth('tanggal_berangkat', $month)
            ->orderBy('tanggal_berangkat')
            ->get()
            ->map(function ($trip) {
                $capacity = $trip->bus->busType->total_seat;
                $filled = $trip->passengers->count();
                return [
                    'trip' => $trip,
                    'capacity' => $capacity,
                    'filled' => $filled,
                    'pct' => $capacity > 0 ? round($filled / $capacity * 100) : 0,
                ];
            });

        return view('rekap.index', compact('trips', 'bulan'));
    }
}
