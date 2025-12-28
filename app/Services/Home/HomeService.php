<?php

namespace App\Services\Home;

use App\Models\Room;
use App\Models\News;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Collection;

class HomeService
{
    /**
     * Get home page data
     * 
     * @param int $nowShowingLimit Number of now showing movies
     * @param int $upcomingLimit Number of upcoming movies
     * @param int $comingSoonLimit Number of coming soon movies
     * @param int $roomLimit Number of rooms
     * @param int $newsLimit Number of news
     * @return array
     */
    public function getHomeData(
        int $nowShowingLimit = 10,
        int $upcomingLimit = 10,
        int $comingSoonLimit = 10,
        int $roomLimit = 10,
        int $newsLimit = 10
    ): array {
        return [
            'now_showing' => $this->getNowShowingMovies($nowShowingLimit),
            'coming_soon' => $this->getComingSoonMovies($comingSoonLimit),
            'upcoming' => $this->getUpcomingMovies($upcomingLimit),
            'rooms' => $this->getRooms($roomLimit),
            'news' => $this->getNews($newsLimit),
        ];
    }

    /**
     * Get now showing movies
     * Phim có ít nhất 1 suất đang ONGOING hoặc có suất SCHEDULED hôm nay
     */
    protected function getNowShowingMovies(int $limit): Collection
    {
        return Movie::with(['poster', 'showtimes'])
            ->get()
            ->filter(function ($movie) {
                return $movie->getComputedStatus() === Movie::STATUS_NOW_SHOWING;
            })
            ->sortByDesc('release_date')
            ->take($limit)
            ->values();
    }

    /**
     * Get upcoming movies
     * Phim có suất SCHEDULED trong tương lai (sau hôm nay)
     */
    protected function getUpcomingMovies(int $limit): Collection
    {
        return Movie::with(['poster', 'showtimes'])
            ->get()
            ->filter(function ($movie) {
                return $movie->getComputedStatus() === Movie::STATUS_UPCOMING;
            })
            ->sortBy('release_date')
            ->take($limit)
            ->values();
    }

    /**
     * Get coming soon movies
     * Phim chưa có suất chiếu nào
     */
    protected function getComingSoonMovies(int $limit): Collection
    {
        return Movie::with(['poster', 'showtimes'])
            ->get()
            ->filter(function ($movie) {
                return $movie->getComputedStatus() === Movie::STATUS_COMING_SOON;
            })
            ->sortBy('release_date')
            ->take($limit)
            ->values();
    }

    /**
     * Get rooms with cinema information
     */
    protected function getRooms(int $limit): Collection
    {
        return Room::with(['cinema'])
            ->orderBy('name')
            ->limit($limit)
            ->get();
    }

    /**
     * Get news
     */
    protected function getNews(int $limit): Collection
    {
        return News::where('status', 'published')
            ->with(['thumbnail', 'author'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}


