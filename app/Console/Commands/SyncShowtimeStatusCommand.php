<?php

namespace App\Console\Commands;

use App\Services\Showtime\ShowtimeService;
use Illuminate\Console\Command;

class SyncShowtimeStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'showtimes:sync-status {--showtime-id= : Sync specific showtime by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync showtime status based on current time and auto-update booking status when showtime completed';

    /**
     * Execute the console command.
     */
    public function handle(ShowtimeService $showtimeService): int
    {
        $showtimeId = $this->option('showtime-id');

        if ($showtimeId) {
            // Sync một suất chiếu cụ thể
            $this->info("Syncing status for showtime ID: {$showtimeId}...");
            
            $result = $showtimeService->syncShowtimeStatus((int) $showtimeId, true);
            
            if (!$result['success']) {
                $this->error("✗ Showtime ID {$showtimeId} not found.");
                return self::FAILURE;
            }
            
            if ($result['showtime_updated']) {
                $this->info("✓ Showtime ID {$showtimeId} status synced successfully.");
                if ($result['bookings_updated'] > 0) {
                    $this->info("  - {$result['bookings_updated']} booking(s) status updated to completed.");
                }
            } else {
                $this->info("✓ Showtime ID {$showtimeId} status is already up to date.");
            }
        } else {
            // Sync tất cả suất chiếu và bookings
            $this->info("Syncing status for all showtimes and related bookings...");
            
            $result = $showtimeService->syncAllShowtimesStatusWithBookings();
            
            $this->info("✓ Synced successfully.");
            $this->info("  - {$result['showtimes_updated']} showtime(s) status updated.");
            $this->info("  - {$result['bookings_updated']} booking(s) status updated.");
        }

        return self::SUCCESS;
    }
}
