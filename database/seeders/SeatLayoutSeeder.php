<?php

namespace Database\Seeders;

use App\Models\BusType;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class SeatLayoutSeeder extends Seeder
{
    public function run(): void
    {
        // ===== LEGREST (32 kursi) =====
        // Foto referensi:
        // Row 1: kiri(1,2) | DRIVER kanan
        // Row 2: kiri(5,6) | kanan(3,4)
        // Row 3: kiri(9,10) | kanan(7,8)
        // Row 4: kiri(13,14) | kanan(11,12)
        // Row 5: kiri(17,18) | kanan(15,16)
        // Row 6: kiri(21,22) | kanan(19,20)
        // Row 7: kiri(25,26) | kanan(23,24)
        // Row 8: kiri(29,30) | kanan(27,28)
        // Row 9: SMOKING/TOILET kiri | kanan(31,32) PINTU DARURAT
        $legrest = BusType::where('name', 'Legrest')->first();
        if ($legrest) {
            $legrest->update([
                'total_seat' => 32,
                'layout_config' => [
                    'rows' => 9,
                    'cols' => 5,
                    'aisle_col' => 2,
                    'sleeper_section' => false,
                    'sleeper_count' => 0,
                    'back_seats' => [31, 32], // kursi di belakang smoking/toilet
                ],
            ]);

            Seat::where('bus_type_id', $legrest->id)->delete();

            $seats = [
                // Row 1: kiri saja (kanan = driver)
                ['1', 1, 0], ['2', 1, 1],
                // Row 2
                ['5', 2, 0], ['6', 2, 1], ['3', 2, 3], ['4', 2, 4],
                // Row 3
                ['9', 3, 0], ['10', 3, 1], ['7', 3, 3], ['8', 3, 4],
                // Row 4
                ['13', 4, 0], ['14', 4, 1], ['11', 4, 3], ['12', 4, 4],
                // Row 5
                ['17', 5, 0], ['18', 5, 1], ['15', 5, 3], ['16', 5, 4],
                // Row 6
                ['21', 6, 0], ['22', 6, 1], ['19', 6, 3], ['20', 6, 4],
                // Row 7
                ['25', 7, 0], ['26', 7, 1], ['23', 7, 3], ['24', 7, 4],
                // Row 8
                ['29', 8, 0], ['30', 8, 1], ['27', 8, 3], ['28', 8, 4],
                // Row 9: belakang (setelah smoking/toilet)
                ['31', 9, 3], ['32', 9, 4],
            ];

            foreach ($seats as [$nomor, $row, $col]) {
                Seat::create([
                    'bus_type_id' => $legrest->id,
                    'nomor_kursi' => $nomor,
                    'kategori' => 'reguler',
                    'posisi_row' => $row,
                    'posisi_col' => $col,
                ]);
            }
        }

        // ===== SKYCLASS (28 reguler + 2 sleeper = 30) =====
        // Foto referensi:
        // Row 0: SLEEPER kiri(S1) | DRIVER + SLEEPER kanan(S2)
        // Row 1: kiri(1,2) | kanan(3,4)
        // Row 2: kiri(5,6) | kanan(7,8)
        // Row 3: kiri(9,10) | kanan(11,12)
        // Row 4: kiri(13,14) | kanan(15,16)
        // Row 5: kiri(17,18) | kanan(19,20)
        // Row 6: kiri(21,22) | kanan(23,24)
        // Row 7: SMOKING/TOILET kiri | kanan(25,26)
        // Row 8: (kosong kiri) | kanan(27,28)
        $skyclass = BusType::where('name', 'Skyclass')->first();
        if ($skyclass) {
            $skyclass->update([
                'total_seat' => 30,
                'layout_config' => [
                    'rows' => 9,
                    'cols' => 5,
                    'aisle_col' => 2,
                    'sleeper_section' => true,
                    'sleeper_count' => 2,
                    'back_seats' => [25, 26, 27, 28],
                ],
            ]);

            Seat::where('bus_type_id', $skyclass->id)->delete();

            // Sleeper (row 0)
            Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => 'S1', 'kategori' => 'sleeper', 'posisi_row' => 0, 'posisi_col' => 0]);
            Seat::create(['bus_type_id' => $skyclass->id, 'nomor_kursi' => 'S2', 'kategori' => 'sleeper', 'posisi_row' => 0, 'posisi_col' => 3]);

            $seats = [
                // Row 1
                ['1', 1, 0], ['2', 1, 1], ['3', 1, 3], ['4', 1, 4],
                // Row 2
                ['5', 2, 0], ['6', 2, 1], ['7', 2, 3], ['8', 2, 4],
                // Row 3
                ['9', 3, 0], ['10', 3, 1], ['11', 3, 3], ['12', 3, 4],
                // Row 4
                ['13', 4, 0], ['14', 4, 1], ['15', 4, 3], ['16', 4, 4],
                // Row 5
                ['17', 5, 0], ['18', 5, 1], ['19', 5, 3], ['20', 5, 4],
                // Row 6
                ['21', 6, 0], ['22', 6, 1], ['23', 6, 3], ['24', 6, 4],
                // Row 7-8: belakang (kanan saja, kiri = smoking/toilet)
                ['25', 7, 3], ['26', 7, 4],
                ['27', 8, 3], ['28', 8, 4],
            ];

            foreach ($seats as [$nomor, $row, $col]) {
                Seat::create([
                    'bus_type_id' => $skyclass->id,
                    'nomor_kursi' => $nomor,
                    'kategori' => 'reguler',
                    'posisi_row' => $row,
                    'posisi_col' => $col,
                ]);
            }
        }
    }
}
