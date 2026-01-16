<?php

namespace App\Services\Showtime;

use App\Models\Seat;
use App\Models\Showtime;
use App\Models\Booking;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class ShowtimeService
{
    /**
     * Get all showtimes with filters
     */
    public function getAllShowtimes(array $filters = []): LengthAwarePaginator
    {
        $query = Showtime::query()->with(['movie', 'room.cinema', 'room.roomType']);

        if (isset($filters['movie_id'])) {
            $query->where('movie_id', $filters['movie_id']);
        }

        if (isset($filters['room_id'])) {
            $query->where('room_id', $filters['room_id']);
        }

        if (isset($filters['room_type_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            });
        }

        if (isset($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->where('date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->where('date', '<=', $filters['date_to']);
        }

        return $query->orderBy('date')->orderBy('start_time')->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get showtime by ID
     */
    public function getShowtimeById(int $id): ?Showtime
    {
        return Showtime::with(['movie', 'room.cinema', 'bookings'])->find($id);
    }

    /**
     * Get showtimes by movie
     * Filters out past showtimes (date < today OR date = today AND start_time < now)
     */
    public function getShowtimesByMovie(int $movieId, array $filters = []): Collection
    {
        $now = now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        $query = Showtime::where('movie_id', $movieId)
            ->with(['room.cinema']);

        if (isset($filters['date'])) {
            $query->where('date', $filters['date']);
        }

        if (isset($filters['room_type_id'])) {
            $query->whereHas('room', function ($q) use ($filters) {
                $q->where('room_type_id', $filters['room_type_id']);
            });
        }

        $showtimes = $query->orderBy('date')->orderBy('start_time')->get();

        // Filter out past showtimes
        $showtimes = $showtimes->filter(function ($showtime) use ($today, $currentTime) {
            $showtimeDate = $showtime->date->format('Y-m-d');
            
            // Remove showtimes from past dates
            if ($showtimeDate < $today) {
                return false;
            }
            
            // For today's showtimes, remove those that have already started
            if ($showtimeDate === $today) {
                $startTime = is_string($showtime->start_time) 
                    ? $showtime->start_time 
                    : $showtime->start_time->format('H:i:s');
                return $startTime > $currentTime;
            }
            
            // Future dates: keep all
            return true;
        });

        return $showtimes->values();
    }

    /**
     * Get seats with booking status for a showtime
     * 
     * @param int $showtimeId
     * @return array{showtime: Showtime|null, seats: Collection, booked_count: int, available_count: int, total_count: int}
     */
    public function getSeatsWithStatus(int $showtimeId): ?array
    {
        $showtime = Showtime::with(['movie', 'room.cinema'])->find($showtimeId);
        
        if (!$showtime) {
            return null;
        }

        // Get all seats of the room
        $seats = Seat::where('room_id', $showtime->room_id)
            ->orderBy('row')
            ->orderBy('number')
            ->get();

        // Get booked seat IDs for this showtime (only confirmed/pending bookings)
        $bookedSeatIds = Booking::where('showtime_id', $showtimeId)
            ->whereIn('status', ['pending', 'confirmed', 'paid'])
            ->with('seats')
            ->get()
            ->pluck('seats')
            ->flatten()
            ->pluck('id')
            ->unique()
            ->toArray();

        // Add booking status to each seat
        $seats->each(function ($seat) use ($bookedSeatIds) {
            $seat->booking_status = in_array($seat->id, $bookedSeatIds) ? 'booked' : 'available';
        });

        // Group seats by row
        $seatsByRow = $seats->groupBy('row')->map(function ($rowSeats, $row) {
            return [
                'row' => $row,
                'seats' => $rowSeats->values(),
            ];
        })->values();

        return [
            'showtime' => $showtime,
            'seats' => $seats,
            'seats_by_row' => $seatsByRow,
            'booked_count' => count($bookedSeatIds),
            'available_count' => $seats->count() - count($bookedSeatIds),
            'total_count' => $seats->count(),
        ];
    }

    /**
     * Normalize time string to H:i:s format
     * 
     * @param string $time Can be H:i or H:i:s format
     * @return string Time in H:i:s format
     */
    protected function normalizeTime(string $time): string
    {
        // If already in H:i:s format, return as is
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
            return $time;
        }
        
        // If in H:i format, add :00 for seconds
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            return $time . ':00';
        }
        
        // Try to parse and format
        try {
            return \Carbon\Carbon::parse($time)->format('H:i:s');
        } catch (\Exception $e) {
            return $time; // Return original if parsing fails
        }
    }

    /**
     * Check if showtime overlaps with existing showtimes in the same room and date
     * 
     * Logic:
     * - Only check showtimes in the same room (room_id)
     * - Only check showtimes on the same date
     * - Two showtimes overlap if:
     *   - New start_time < Existing end_time AND
     *   - New end_time > Existing start_time
     * 
     * Examples:
     * ❌ Overlap: 09:00-10:36 and 10:00-11:36 (10:00-10:36 overlaps)
     * ✅ No overlap: 09:00-10:36 and 10:36-12:00 (touching at 10:36, but not overlapping)
     * 
     * @param int $roomId
     * @param string $date Format: Y-m-d
     * @param string $startTime Format: H:i or H:i:s
     * @param string $endTime Format: H:i or H:i:s
     * @param int|null $excludeShowtimeId Exclude this showtime ID (for update)
     * @return bool True if overlaps, False otherwise
     */
    public function checkTimeOverlap(int $roomId, string $date, string $startTime, string $endTime, ?int $excludeShowtimeId = null): bool
    {
        // Get all showtimes in the same room and date
        $query = Showtime::where('room_id', $roomId)
            ->where('date', $date);
        
        // Exclude current showtime when updating
        if ($excludeShowtimeId) {
            $query->where('id', '!=', $excludeShowtimeId);
        }
        
        $existingShowtimes = $query->get();
        
        // Normalize and convert new times to Carbon for comparison
        $newStartTime = $this->normalizeTime($startTime);
        $newEndTime = $this->normalizeTime($endTime);
        $newStart = \Carbon\Carbon::createFromFormat('H:i:s', $newStartTime);
        $newEnd = \Carbon\Carbon::createFromFormat('H:i:s', $newEndTime);
        
        foreach ($existingShowtimes as $existing) {
            // Get existing showtime times and normalize
            $existingStartTime = is_string($existing->start_time) 
                ? $existing->start_time 
                : $existing->start_time->format('H:i:s');
            $existingEndTime = is_string($existing->end_time) 
                ? $existing->end_time 
                : $existing->end_time->format('H:i:s');
            
            $existingStartTime = $this->normalizeTime($existingStartTime);
            $existingEndTime = $this->normalizeTime($existingEndTime);
            
            $existingStart = \Carbon\Carbon::createFromFormat('H:i:s', $existingStartTime);
            $existingEnd = \Carbon\Carbon::createFromFormat('H:i:s', $existingEndTime);
            
            // Check overlap: new start < existing end AND new end > existing start
            // This means there's a time period where both showtimes are active
            if ($newStart->lt($existingEnd) && $newEnd->gt($existingStart)) {
                return true; // Overlaps
            }
        }
        
        return false; // No overlap
    }

    /**
     * Create a new showtime
     * 
     * @throws \Exception If showtime overlaps with existing showtime
     */
    public function createShowtime(array $data): Showtime
    {
        // Check for time overlap
        if ($this->checkTimeOverlap(
            $data['room_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time']
        )) {
            throw new \Exception('SHOWTIME_TIME_OVERLAP');
        }
        
        return Showtime::create($data);
    }

    /**
     * Update showtime
     * 
     * @throws \Exception If showtime overlaps with existing showtime
     */
    public function updateShowtime(int $id, array $data): bool
    {
        $showtime = Showtime::find($id);
        
        if (!$showtime) {
            return false;
        }

        // Get values for overlap check (use new data or existing showtime data)
        $roomId = $data['room_id'] ?? $showtime->room_id;
        $date = $data['date'] ?? $showtime->date->format('Y-m-d');
        
        // Normalize start_time and end_time
        $startTime = $data['start_time'] ?? (
            is_string($showtime->start_time) 
                ? $showtime->start_time 
                : $showtime->start_time->format('H:i:s')
        );
        $endTime = $data['end_time'] ?? (
            is_string($showtime->end_time) 
                ? $showtime->end_time 
                : $showtime->end_time->format('H:i:s')
        );
        
        // Check for time overlap (exclude current showtime)
        if ($this->checkTimeOverlap($roomId, $date, $startTime, $endTime, $id)) {
            throw new \Exception('SHOWTIME_TIME_OVERLAP');
        }

        return $showtime->update($data);
    }

    /**
     * Delete showtime (soft delete)
     */
    public function deleteShowtime(int $id): bool
    {
        $showtime = Showtime::find($id);
        
        if (!$showtime) {
            return false;
        }

        return $showtime->delete();
    }

    /**
     * Get computed status for a showtime based on current time
     * 
     * Logic:
     * - SCHEDULED: now < date + start_time
     * - ONGOING: date + start_time <= now < date + end_time
     * - COMPLETED: now >= date + end_time
     * 
     * @param Showtime $showtime
     * @return string
     */
    public function getComputedStatus(Showtime $showtime): string
    {
        $now = now();
        
        // Normalize times
        $startTime = is_string($showtime->start_time) 
            ? $showtime->start_time 
            : $showtime->start_time->format('H:i:s');
        $endTime = is_string($showtime->end_time) 
            ? $showtime->end_time 
            : $showtime->end_time->format('H:i:s');
        
        // Create datetime objects for comparison
        $showtimeDate = $showtime->date->format('Y-m-d');
        $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $showtimeDate . ' ' . $startTime);
        $endDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $showtimeDate . ' ' . $endTime);
        
        // Compare with current time
        if ($now->lt($startDateTime)) {
            return Showtime::STATUS_SCHEDULED;
        } elseif ($now->gte($endDateTime)) {
            return Showtime::STATUS_COMPLETED;
        } else {
            return Showtime::STATUS_ONGOING;
        }
    }

    /**
     * Sync showtime status based on current time
     * 
     * @param int $showtimeId
     * @return array{success: bool, showtime_updated: bool, bookings_updated: int}|bool
     */
    public function syncShowtimeStatus(int $showtimeId, bool $returnDetails = false)
    {
        $showtime = Showtime::find($showtimeId);
        
        if (!$showtime) {
            return $returnDetails ? ['success' => false, 'showtime_updated' => false, 'bookings_updated' => 0] : false;
        }

        $computedStatus = $this->getComputedStatus($showtime);
        $statusChanged = false;
        $bookingsUpdated = 0;
        
        if ($showtime->status !== $computedStatus) {
            $showtime->status = $computedStatus;
            $statusChanged = $showtime->save();
            
            // Nếu showtime vừa chuyển sang completed, sync booking status
            if ($statusChanged && $computedStatus === Showtime::STATUS_COMPLETED) {
                $bookingService = app(\App\Services\Booking\BookingService::class);
                $bookingsUpdated = $bookingService->syncBookingsStatusByShowtime($showtimeId);
            }
        }

        if ($returnDetails) {
            return [
                'success' => true,
                'showtime_updated' => $statusChanged,
                'bookings_updated' => $bookingsUpdated,
            ];
        }

        return $statusChanged || true;
    }

    /**
     * Sync all showtimes status
     * 
     * Cập nhật status cho tất cả suất chiếu dựa trên thời gian hiện tại
     * Nên chạy bằng scheduler mỗi phút
     * 
     * @return int Number of updated showtimes
     */
    public function syncAllShowtimesStatus(): int
    {
        $showtimes = Showtime::all();
        $updated = 0;

        foreach ($showtimes as $showtime) {
            $computedStatus = $this->getComputedStatus($showtime);
            
            if ($showtime->status !== $computedStatus) {
                $showtime->status = $computedStatus;
                $showtime->save();
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Sync all showtimes status and update related bookings
     * 
     * Cập nhật status cho tất cả suất chiếu và tự động update booking status
     * khi showtime completed
     * 
     * @return array{showtimes_updated: int, bookings_updated: int}
     */
    public function syncAllShowtimesStatusWithBookings(): array
    {
        $showtimesUpdated = $this->syncAllShowtimesStatus();
        
        // Sync bookings status for completed showtimes
        $bookingService = app(\App\Services\Booking\BookingService::class);
        $bookingsUpdated = $bookingService->syncAllBookingsStatusByShowtime();

        return [
            'showtimes_updated' => $showtimesUpdated,
            'bookings_updated' => $bookingsUpdated,
        ];
    }
}



