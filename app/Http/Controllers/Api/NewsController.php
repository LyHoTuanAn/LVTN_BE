<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Http\Traits\ApiResponseTrait;
use App\Services\News\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    use ApiResponseTrait;

    protected NewsService $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    /**
     * Get news article by ID (published only)
     */
    public function show($id)
    {
        $news = $this->newsService->getPublishedById($id);

        if (!$news) {
            return $this->errorResponse(
                'NEWS_NOT_FOUND',
                [],
                null,
                404
            );
        }

        return $this->successResponse(
            'NEWS_FETCHED_SUCCESS',
            new NewsResource($news)
        );
    }
}
