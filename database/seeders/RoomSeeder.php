<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\Seat;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = Room::all();

        if ($rooms->isEmpty()) {
            $this->command->warn('No rooms found. Please create rooms first.');
            return;
        }

        $totalSeatsCreated = 0;

        foreach ($rooms as $room) {
            // Skip if room already has seats
            if ($room->seats()->count() > 0) {
                $this->command->info("Room '{$room->name}' already has seats. Skipping...");
                continue;
            }

            $seatCount = $room->seat_count;
            
            // Determine rows and seats per row
            // Common cinema layouts: 5-10 seats per row
            $seatsPerRow = 10; // Default
            if ($seatCount <= 30) {
                $seatsPerRow = 5;
            } elseif ($seatCount <= 50) {
                $seatsPerRow = 10;
            } elseif ($seatCount <= 60) {
                $seatsPerRow = 10;
            }

            $rows = ceil($seatCount / $seatsPerRow);
            $rowsArray = range('A', 'Z'); // A-Z rows

            $seatsCreated = 0;
            $seatNumber = 1;

            for ($rowIndex = 0; $rowIndex < $rows && $seatsCreated < $seatCount; $rowIndex++) {
                $row = $rowsArray[$rowIndex];
                $seatsInThisRow = min($seatsPerRow, $seatCount - $seatsCreated);

                for ($i = 1; $i <= $seatsInThisRow; $i++) {
                    Seat::create([
                        'room_id' => $room->id,
                        'row' => $row,
                        'number' => $i,
                        'type' => 'normal', // normal, vip, couple
                        'status' => 'active', // active, maintenance, disabled
                    ]);
                    $seatsCreated++;
                }
            }

            $totalSeatsCreated += $seatsCreated;
            $this->command->info("Created {$seatsCreated} seats for Room '{$room->name}'");
        }

        if ($totalSeatsCreated > 0) {
            $this->command->info("Total: Created {$totalSeatsCreated} seats successfully!");
        } else {
            $this->command->info("All rooms already have seats.");
        }
    }
}
