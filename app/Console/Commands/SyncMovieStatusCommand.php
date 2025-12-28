<?php

namespace App\Console\Commands;

use App\Services\Movie\MovieService;
use Illuminate\Console\Command;

class SyncMovieStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:sync-status {--movie-id= : Sync specific movie by ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync movie status based on showtimes (COMING_SOON, UPCOMING, NOW_SHOWING)';

    /**
     * Execute the console command.
     */
    public function handle(MovieService $movieService): int
    {
        $movieId = $this->option('movie-id');

        if ($movieId) {
            // Sync một phim cụ thể
            $this->info("Syncing status for movie ID: {$movieId}...");
            
            $result = $movieService->syncMovieStatus((int) $movieId);
            
            if ($result) {
                $this->info("✓ Movie ID {$movieId} status synced successfully.");
            } else {
                $this->error("✗ Movie ID {$movieId} not found.");
                return self::FAILURE;
            }
        } else {
            // Sync tất cả phim
            $this->info("Syncing status for all movies...");
            
            $updated = $movieService->syncAllMoviesStatus();
            
            $this->info("✓ Synced successfully. {$updated} movie(s) status updated.");
        }

        return self::SUCCESS;
    }
}
