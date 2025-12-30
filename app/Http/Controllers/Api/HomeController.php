<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\MovieResource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\RoomTypeResource;
use App\Services\Home\HomeService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiResponseTrait;

    protected HomeService $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Get home page data
     */
    public function index(Request $request)
    {
        $nowShowingLimit = (int) $request->get('now_showing_limit', 10);
        $upcomingLimit = (int) $request->get('upcoming_limit', 10);
        $comingSoonLimit = (int) $request->get('coming_soon_limit', 10);
        $roomLimit = (int) $request->get('room_limit', 10);
        $newsLimit = (int) $request->get('news_limit', 10);

        $data = $this->homeService->getHomeData(
            $nowShowingLimit,
            $upcomingLimit,
            $comingSoonLimit,
            $roomLimit,
            $newsLimit
        );

        $formattedData = [
            'now_showing' => MovieResource::collection($data['now_showing']),
            'coming_soon' => MovieResource::collection($data['coming_soon']),
            'upcoming' => MovieResource::collection($data['upcoming']),
            'room_types' => RoomTypeResource::collection($data['room_types']),
            'news' => NewsResource::collection($data['news']),
        ];

        return $this->successResponse(
            'HOME_DATA_FETCHED_SUCCESS',
            $formattedData,
            'Home data fetched successfully'
        );
    }
}

