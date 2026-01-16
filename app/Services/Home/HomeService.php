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
     * @param int $comingSoonLimit Number of coming soon movies
     * @param int $roomLimit Number of rooms
     * @param int $newsLimit Number of news
     * @return array
     */
    public function getHomeData(
        int $nowShowingLimit = 10,
        int $comingSoonLimit = 10,
        int $roomLimit = 10,
        int $newsLimit = 10
    ): array {
        return [
            'now_showing' => $this->getNowShowingMovies($nowShowingLimit),
            'coming_soon' => $this->getComingSoonMovies($comingSoonLimit),
            'room_types' => $this->getRoomTypes($roomLimit),
            'news' => $this->getNews($newsLimit),
        ];
    }

    /**
     * Get now showing movies
     * Phim có release_date <= today (không phụ thuộc vào showtimes)
     */
    protected function getNowShowingMovies(int $limit): Collection
    {
        return Movie::with(['poster'])
            ->get()
            ->filter(function ($movie) {
                return $movie->getComputedStatus() === Movie::STATUS_NOW_SHOWING;
            })
            ->sortByDesc('release_date')
            ->take($limit)
            ->values();
    }

    /**
     * Get coming soon movies
     * Phim có release_date > today (không phụ thuộc vào showtimes)
     */
    protected function getComingSoonMovies(int $limit): Collection
    {
        return Movie::with(['poster'])
            ->get()
            ->filter(function ($movie) {
                return $movie->getComputedStatus() === Movie::STATUS_COMING_SOON;
            })
            ->sortBy('release_date')
            ->take($limit)
            ->values();
    }

    /**
     * Get active room types
     */
    protected function getRoomTypes(int $limit): Collection
    {
        return \App\Models\RoomType::active()
            ->with(['image'])
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


