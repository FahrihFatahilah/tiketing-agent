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
        $allTrips = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', '>=', today()->subDays(6))
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('id')
            ->get();

        $tripsByDate = $allTrips->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'));

        // Hanya trip aktif (dibuka) yang menghalangi pemilihan
        $activeTrips = $allTrips->where('status', 'dibuka');

        $takenSchedules = $activeTrips->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'))
            ->map(fn($trips) => $trips->pluck('schedule_id')->values());

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

        return view('trips.index', compact('schedules', 'buses', 'trip', 'seatMap', 'occupiedSeats', 'tripsByDate', 'takenSchedules'));
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

        $allTrips2 = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', '>=', today()->subDays(6))
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('id')
            ->get();

        $tripsByDate = $allTrips2->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'));
        $activeTrips2 = $allTrips2->where('status', 'dibuka');
        $takenSchedules = $activeTrips2->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'))
            ->map(fn($trips) => $trips->pluck('schedule_id')->values());

        return view('trips.index', compact('schedules', 'buses', 'trip', 'seatMap', 'occupiedSeats', 'tripsByDate', 'takenSchedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'bus_id'      => 'required|exists:buses,id',
            'tanggal'     => 'required|date',
        ]);

        $busConflict = Trip::where('bus_id', $request->bus_id)
            ->where('tanggal_berangkat', $request->tanggal)
            ->where('schedule_id', $request->schedule_id)
            ->where('status', 'dibuka')
            ->exists();

        if ($busConflict) {
            return back()->withErrors(['bus_id' => 'Armada ini sudah digunakan di jadwal dan tanggal tersebut.'])->withInput();
        }

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
