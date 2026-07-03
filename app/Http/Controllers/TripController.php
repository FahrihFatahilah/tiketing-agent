<?php

namespace App\Http\Controllers;

use App\Models\Bus;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    private function buildViewData(Trip $trip = null)
    {
        $schedules = Schedule::with('route')->where('aktif', true)->get();
        $buses = Bus::with('busType')->get();

        $allTrips = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', '>=', today()->subDays(6))
            ->orderBy('tanggal_berangkat', 'desc')
            ->orderBy('id')
            ->get();

        $tripsByDate = $allTrips->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'));

        // bus_id yang sudah dipakai per tanggal+schedule
        $takenBuses = $allTrips->where('status', 'dibuka')
            ->groupBy(fn($t) => $t->tanggal_berangkat->format('Y-m-d'))
            ->map(fn($trips) => $trips->map(fn($t) => [
                'schedule_id' => $t->schedule_id,
                'bus_id'      => $t->bus_id,
            ])->values());

        $seatMap = null;
        $occupiedSeats = collect();

        if ($trip) {
            $trip->load(['schedule.route', 'bus.busType', 'passengers.seat', 'passengers.inputBy']);

            $busType = $trip->bus->busType;
            $seats = Seat::where('bus_type_id', $busType->id)
                ->orderBy('posisi_row')->orderBy('posisi_col')->get();

            $occupiedSeats = $trip->passengers->keyBy('seat_id');

            $layout = $busType->layout_config;
            $grid = [];
            foreach ($seats->where('kategori', 'reguler') as $seat) {
                $grid[$seat->posisi_row][$seat->posisi_col] = $seat;
            }
            $sleeperSeats = $seats->where('kategori', 'sleeper');
            $seatMap = compact('grid', 'sleeperSeats', 'layout');
        }

        return compact('schedules', 'buses', 'trip', 'seatMap', 'occupiedSeats', 'tripsByDate', 'takenBuses');
    }

    public function index(Request $request)
    {
        $trip = null;

        if ($request->filled(['schedule_id', 'tanggal', 'bus_id'])) {
            $trip = Trip::firstOrCreate(
                [
                    'bus_id'            => $request->bus_id,
                    'schedule_id'       => $request->schedule_id,
                    'tanggal_berangkat' => $request->tanggal,
                ],
                ['status' => 'dibuka']
            );
        }

        return view('trips.index', $this->buildViewData($trip));
    }

    public function seatmap(Trip $trip)
    {
        return view('trips.index', $this->buildViewData($trip));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'bus_id'      => 'required|exists:buses,id',
            'tanggal'     => 'required|date',
        ]);

        $conflict = Trip::where('bus_id', $request->bus_id)
            ->where('schedule_id', $request->schedule_id)
            ->where('tanggal_berangkat', $request->tanggal)
            ->exists();

        if ($conflict) {
            return back()->withErrors(['bus_id' => 'Armada ini sudah digunakan di jadwal dan tanggal tersebut.'])->withInput();
        }

        $trip = Trip::create([
            'bus_id'            => $request->bus_id,
            'schedule_id'       => $request->schedule_id,
            'tanggal_berangkat' => $request->tanggal,
            'status'            => 'dibuka',
        ]);

        return redirect()->route('trips.seatmap', $trip);
    }

    public function updateStatus(Request $request, Trip $trip)
    {
        abort_unless(auth()->user()->hasAnyRole(['admin', 'pengurus']), 403);
        $trip->update(['status' => $request->status]);
        return back()->with('success', 'Status trip diperbarui.');
    }
}
