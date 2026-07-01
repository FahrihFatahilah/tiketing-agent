<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Trip;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today();

        $trips = Trip::with(['schedule.route', 'bus.busType', 'passengers'])
            ->where('tanggal_berangkat', $today)
            ->get()
            ->map(function ($trip) {
                $capacity = $trip->bus->busType->total_seat;
                $filled = $trip->passengers->count();
                return [
                    'trip' => $trip,
                    'capacity' => $capacity,
                    'filled' => $filled,
                    'empty' => $capacity - $filled,
                ];
            });

        $schedules = Schedule::with('route')->where('aktif', true)->get();

        return view('dashboard', compact('trips', 'schedules', 'today'));
    }
}
