<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreNewsRequest;
use App\Http\Requests\Admin\UpdateNewsRequest;
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

    public function show(int $id)
    {
        $news = $this->newsService->getById($id);

        if (!$news) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', __('News not found'));
        }

        return view('admin.news.show', [
            'news' => $news,
        ]);
    }

    public function store(StoreNewsRequest $request)
    {
        $data = $request->validated();

        // Trim summary fields to remove leading/trailing whitespace and line breaks
        if (isset($data['summary_vi'])) {
            $data['summary_vi'] = trim($data['summary_vi']);
        }
        if (isset($data['summary_en'])) {
            $data['summary_en'] = trim($data['summary_en']);
        }

        // Auto-translate from Vietnamese to English if EN fields are empty
        if (empty($data['title_en']) && !empty($data['title_vi'])) {
            $data['title_en'] = $this->translator->translate($data['title_vi'], 'vi', 'en');
        }

        if (empty($data['summary_en']) && !empty($data['summary_vi'])) {
            $data['summary_en'] = trim($this->translator->translate($data['summary_vi'], 'vi', 'en'));
        }

        if (empty($data['content_en']) && !empty($data['content_vi'])) {
            // Content English giữ y chang nội dung tiếng Việt (đã được sync từ TinyMCE phía client)
            $data['content_en'] = $data['content_vi'];
        }

        $news = $this->newsService->create(
            $data,
            auth()->id(),
            $request->file('thumbnail')
        );

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('News created successfully'));
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

    public function edit(int $id)
    {
        $news = $this->newsService->getById($id);

        if (!$news) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', __('News not found'));
        }

        return view('admin.news.edit', [
            'news' => $news,
        ]);
    }

    public function update(UpdateNewsRequest $request, int $id)
    {
        $news = $this->newsService->getById($id);

        if (!$news) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', __('News not found'));
        }

        $data = $request->validated();

        // Trim summary fields to remove leading/trailing whitespace and line breaks
        if (isset($data['summary_vi'])) {
            $data['summary_vi'] = trim($data['summary_vi']);
        }
        if (isset($data['summary_en'])) {
            $data['summary_en'] = trim($data['summary_en']);
        }

        // Auto-translate from Vietnamese to English if EN fields are empty
        if (empty($data['title_en']) && !empty($data['title_vi'])) {
            $data['title_en'] = $this->translator->translate($data['title_vi'], 'vi', 'en');
        }

        if (empty($data['summary_en']) && !empty($data['summary_vi'])) {
            $data['summary_en'] = trim($this->translator->translate($data['summary_vi'], 'vi', 'en'));
        }

        if (empty($data['content_en']) && !empty($data['content_vi'])) {
            // Content English giữ y chang nội dung tiếng Việt (đã được sync từ TinyMCE phía client)
            $data['content_en'] = $data['content_vi'];
        }

        $updated = $this->newsService->update(
            $id,
            $data,
            $request->file('thumbnail')
        );

        if (!$updated) {
            return redirect()
                ->route('admin.news.edit', $id)
                ->with('error', __('Failed to update news'));
        }

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('News updated successfully'));
    }

    public function destroy(int $id)
    {
        $news = $this->newsService->getById($id);

        if (!$news) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', __('News not found'));
        }

        $deleted = $this->newsService->delete($id);

        if (!$deleted) {
            return redirect()
                ->route('admin.news.index')
                ->with('error', __('Failed to delete news'));
        }

        return redirect()
            ->route('admin.news.index')
            ->with('success', __('News deleted successfully'));
    }
}

