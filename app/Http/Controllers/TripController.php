<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function index(Request $request)
    {
        $schedules = Schedule::with('route')->where('aktif', true)->get();
        $buses = Bus::with('busType')->get();

        // Trips grouped by date (last 7 days + future)
        $tripsByDate = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', '>=', today()->subDays(6))
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('id')
            ->get()
            ->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'));

        $trip = null;
        $seatMap = null;
        $occupiedSeats = collect();

        if ($request->filled(['schedule_id', 'tanggal', 'bus_id'])) {
            $trip = Trip::firstOrCreate(
                [
                    'schedule_id' => $request->schedule_id,
                    'tanggal_berangkat' => $request->tanggal,
                ],
                ['bus_id' => $request->bus_id, 'status' => 'dibuka']
            );

            $trip->load(['schedule.route', 'bus.busType', 'passengers.seat', 'passengers.inputBy']);

            $busType = $trip->bus->busType;
            $seats = Seat::where('bus_type_id', $busType->id)->orderBy('posisi_row')->orderBy('posisi_col')->get();

            $occupiedSeats = $trip->passengers->keyBy('seat_id');

            $layout = $busType->layout_config;
            $grid = [];
            foreach ($seats->where('kategori', 'reguler') as $seat) {
                $grid[$seat->posisi_row][$seat->posisi_col] = $seat;
            }
            $sleeperSeats = $seats->where('kategori', 'sleeper');

            $seatMap = compact('grid', 'sleeperSeats', 'layout');
        }

        return view('trips.index', compact('schedules', 'buses', 'trip', 'seatMap', 'occupiedSeats', 'tripsByDate'));
    }

    public function seatmap(Trip $trip)
    {
        $trip->load(['schedule.route', 'bus.busType', 'passengers.seat', 'passengers.inputBy']);

        $busType = $trip->bus->busType;
        $seats = Seat::where('bus_type_id', $busType->id)->orderBy('posisi_row')->orderBy('posisi_col')->get();
        $occupiedSeats = $trip->passengers->keyBy('seat_id');

        $layout = $busType->layout_config;
        $grid = [];
        foreach ($seats->where('kategori', 'reguler') as $seat) {
            $grid[$seat->posisi_row][$seat->posisi_col] = $seat;
        }
        $sleeperSeats = $seats->where('kategori', 'sleeper');
        $seatMap = compact('grid', 'sleeperSeats', 'layout');

        $schedules = Schedule::with('route')->where('aktif', true)->get();
        $buses = Bus::with('busType')->get();

        $tripsByDate = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', '>=', today()->subDays(6))
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('id')
            ->get()
            ->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'));

        return view('trips.index', compact('schedules', 'buses', 'trip', 'seatMap', 'occupiedSeats', 'tripsByDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'bus_id' => 'required|exists:buses,id',
            'tanggal' => 'required|date',
        ]);

        $trip = Trip::firstOrCreate(
            ['schedule_id' => $request->schedule_id, 'tanggal_berangkat' => $request->tanggal],
            ['bus_id' => $request->bus_id, 'status' => 'dibuka']
        );

        return redirect()->route('trips.seatmap', $trip);
    }

    public function updateStatus(Request $request, Trip $trip)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'pengurus']), 403);
        $trip->update(['status' => $request->status]);
        return back()->with('success', 'Status trip diperbarui.');
    }
}
