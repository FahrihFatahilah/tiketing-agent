<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\BusType;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Seat;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = ['admin', 'pengurus', 'agen'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        // Users — pakai create() langsung, bukan factory(), agar tidak butuh fakerphp di production
        $admin = User::create(['name' => 'Admin', 'email' => 'admin@pomuliajaya.com', 'password' => bcrypt('password')]);
        $admin->assignRole('admin');

        $pengurus = User::create(['name' => 'Pengurus', 'email' => 'staff@pomuliajaya.com', 'password' => bcrypt('password')]);
        $pengurus->assignRole('pengurus');

        $agen = User::create(['name' => 'Agen Mataram', 'email' => 'agen@pomuliajaya.com', 'password' => bcrypt('password')]);
        $agen->assignRole('agen');

        // Routes
        $mataramBima = Route::create(['name' => 'Mataram - Bima', 'origin' => 'Mataram', 'destination' => 'Bima']);
        $bimaMataram = Route::create(['name' => 'Bima - Mataram', 'origin' => 'Bima', 'destination' => 'Mataram']);

        // Schedules
        Schedule::create(['route_id' => $mataramBima->id, 'jam_berangkat' => '09:00:00', 'label' => 'Pagi 09:00']);
        Schedule::create(['route_id' => $mataramBima->id, 'jam_berangkat' => '14:30:00', 'label' => 'Siang 14:30']);
        Schedule::create(['route_id' => $bimaMataram->id, 'jam_berangkat' => '17:00:00', 'label' => 'Sore 17:00']);
        Schedule::create(['route_id' => $bimaMataram->id, 'jam_berangkat' => '19:00:00', 'label' => 'Malam 19:00']);

        // Bus Types
        $skyclass = BusType::create([
            'name' => 'Skyclass',
            'total_seat' => 30,
            'layout_config' => [
                'rows' => 7,
                'cols' => 5, // col 0,1 = left | col 2 = aisle | col 3,4 = right
                'aisle_col' => 2,
                'sections' => [
                    ['type' => 'reguler', 'rows' => [1, 2, 3, 4, 5, 6, 7]],
                ],
                'sleeper_section' => true,
                'sleeper_count' => 2,
            ],
        ]);

        $legrest = BusType::create([
            'name' => 'Legrest',
            'total_seat' => 32,
            'layout_config' => [
                'rows' => 8,
                'cols' => 5,
                'aisle_col' => 2,
                'sections' => [
                    ['type' => 'reguler', 'rows' => [1, 2, 3, 4, 5, 6, 7, 8]],
                ],
                'sleeper_section' => false,
                'sleeper_count' => 0,
            ],
        ]);

        // Seats for Skyclass: 28 reguler (2-2 x 7 rows) + 2 sleeper
        $seatNum = 1;
        for ($row = 1; $row <= 7; $row++) {
            // Left side: col 0, 1
            foreach ([0, 1] as $col) {
                Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => (string)$seatNum++, 'kategori' => 'reguler', 'posisi_row' => $row, 'posisi_col' => $col]);
            }
            // Right side: col 3, 4
            foreach ([3, 4] as $col) {
                Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => (string)$seatNum++, 'kategori' => 'reguler', 'posisi_row' => $row, 'posisi_col' => $col]);
            }
        }
        // Sleeper seats at row 8
        Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => 'S1', 'kategori' => 'sleeper', 'posisi_row' => 8, 'posisi_col' => 0]);
        Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => 'S2', 'kategori' => 'sleeper', 'posisi_row' => 8, 'posisi_col' => 3]);

        // Seats for Legrest: 32 reguler (2-2 x 8 rows)
        $seatNum = 1;
        for ($row = 1; $row <= 8; $row++) {
            foreach ([0, 1] as $col) {
                Seat::create(['bus_type_id' => $legrest->id, 'nomor_kursi' => (string)$seatNum++, 'kategori' => 'reguler', 'posisi_row' => $row, 'posisi_col' => $col]);
            }
            foreach ([3, 4] as $col) {
                Seat::create(['bus_type_id' => $legrest->id, 'nomor_kursi' => (string)$seatNum++, 'kategori' => 'reguler', 'posisi_row' => $row, 'posisi_col' => $col]);
            }
        }

        // Buses
        Bus::create(['bus_type_id' => $skyclass->id, 'nomor_lambung' => 'DR-001']);
        Bus::create(['bus_type_id' => $skyclass->id, 'nomor_lambung' => 'DR-002']);
        Bus::create(['bus_type_id' => $legrest->id, 'nomor_lambung' => 'DR-003']);
        Bus::create(['bus_type_id' => $legrest->id, 'nomor_lambung' => 'DR-004']);
    }
}
