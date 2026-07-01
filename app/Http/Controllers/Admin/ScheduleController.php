<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with('route')->orderBy('route_id')->orderBy('jam_berangkat')->get();
        $routes = Route::all();
        return view('admin.schedules.index', compact('schedules', 'routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'jam_berangkat' => 'required',
            'label' => 'required|string|max:50',
        ]);
        Schedule::create($request->only(['route_id', 'jam_berangkat', 'label']));
        return back()->with('success', 'Jadwal ditambahkan.');
    }

    public function update(Request $request, Schedule $schedule)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,id',
            'jam_berangkat' => 'required',
            'label' => 'required|string|max:50',
            'aktif' => 'boolean',
        ]);
        $schedule->update($request->only(['route_id', 'jam_berangkat', 'label', 'aktif']));
        return back()->with('success', 'Jadwal diperbarui.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();
        return back()->with('success', 'Jadwal dihapus.');
    }
}
