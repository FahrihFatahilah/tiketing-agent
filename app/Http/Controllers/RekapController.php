<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    private function getData(string $bulan)
    {
        [$year, $month] = explode('-', $bulan);

        return Trip::with(['schedule.route', 'bus.busType', 'passengers.seat', 'passengers.inputBy'])
            ->whereYear('tanggal_berangkat', $year)
            ->whereMonth('tanggal_berangkat', $month)
            ->orderBy('tanggal_berangkat')
            ->get()
            ->map(function ($trip) {
                $capacity = $trip->bus->busType->total_seat;
                $filled = $trip->passengers->count();
                return [
                    'trip'     => $trip,
                    'capacity' => $capacity,
                    'filled'   => $filled,
                    'pct'      => $capacity > 0 ? round($filled / $capacity * 100) : 0,
                ];
            });
    }

    public function index(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $trips = $this->getData($bulan);

        return view('rekap.index', compact('trips', 'bulan'));
    }

    public function pdf(Request $request)
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $trips = $this->getData($bulan);

        $totalFilled = $trips->sum('filled');
        $totalCapacity = $trips->sum('capacity');
        $avgPct = $totalCapacity > 0 ? round($totalFilled / $totalCapacity * 100) : 0;

        $bulanLabel = \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y');

        $pdf = Pdf::loadView('rekap.pdf', compact('trips', 'bulan', 'bulanLabel', 'totalFilled', 'totalCapacity', 'avgPct'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("rekap-okupansi-{$bulan}.pdf");
    }
}
