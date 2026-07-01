<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Route;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    public function index()
    {
        $routes = Route::withCount('schedules')->latest()->get();
        return view('admin.routes.index', compact('routes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'origin' => 'required|string|max:100',
            'destination' => 'required|string|max:100',
        ]);
        Route::create($request->only(['name', 'origin', 'destination']));
        return back()->with('success', 'Rute ditambahkan.');
    }

    public function update(Request $request, Route $route)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'origin' => 'required|string|max:100',
            'destination' => 'required|string|max:100',
        ]);
        $route->update($request->only(['name', 'origin', 'destination']));
        return back()->with('success', 'Rute diperbarui.');
    }

    public function destroy(Route $route)
    {
        $route->delete();
        return back()->with('success', 'Rute dihapus.');
    }
}
