<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Services\News\NewsService;
use App\Services\Translation\GoogleTranslateService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class NewsController extends Controller
{
    public function __construct(
        protected NewsService $newsService,
        protected GoogleTranslateService $translator,
    ) {
    }

    public function index(Request $request)
    {
        $news = $this->newsService->getAll($request->all());

        return view('admin.news.index', [
            'newsItems' => $news,
            'filters' => [
                'status' => $request->get('status'),
                'search' => $request->get('search'),
            ],
        ]);
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(StoreNewsRequest $request)
    {
        $data = $request->validated();

        // Auto-translate from Vietnamese to English if EN fields are empty
        if (empty($data['title_en']) && !empty($data['title_vi'])) {
            $data['title_en'] = $this->translator->translate($data['title_vi'], 'vi', 'en');
        }

        if (empty($data['summary_en']) && !empty($data['summary_vi'])) {
            $data['summary_en'] = $this->translator->translate($data['summary_vi'], 'vi', 'en');
        }

        if (empty($data['content_en']) && !empty($data['content_vi'])) {
            // Content English giữ y chang nội dung tiếng Việt (đã được sync từ TinyMCE phía client)
            $data['content_en'] = $data['content_vi'];
        }

        $news = $this->newsService->create(
            $data,
            auth()->id(),
            $request->file('thumbnail'),
            $request->file('inline_images', [])
        );

        $inlineUrls = [];
        if (!empty($news->inline_image_ids)) {
            foreach ($news->inline_image_ids as $id) {
                $inlineUrls[] = asset('storage/' . (\App\Models\MediaFile::find($id)?->file_path));
            }
        }

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('News created successfully'))
            ->with('inline_urls', array_filter($inlineUrls));
    }

    public function translate(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'from' => 'nullable|string|size:2',
            'to' => 'nullable|string|size:2',
        ]);

        $translated = $this->translator->translate(
            $validated['text'],
            $validated['from'] ?? 'vi',
            $validated['to'] ?? 'en'
        );

        return response()->json([
            'translated' => $translated,
        ]);
    }
}

