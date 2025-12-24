@extends('layouts.app')

@section('title', __('Create News'))
@section('page-title', __('Create News'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <form method="POST" action="{{ route('admin.news.store') }}" enctype="multipart/form-data" style="display: grid; gap: 20px;">
        @csrf

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Title (English)') }}</label>
                <input type="text" name="title_en" value="{{ old('title_en') }}"
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                @error('title_en')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Title (Vietnamese)') }}</label>
                <input type="text" name="title_vi" value="{{ old('title_vi') }}" required
                    style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                @error('title_vi')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Slug') }}</label>
            <input type="text" name="slug" value="{{ old('slug') }}" 
                style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
            @error('slug')
                <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Summary (English)') }}</label>
                <textarea name="summary_en" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">{{ old('summary_en') }}</textarea>
                @error('summary_en')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Summary (Vietnamese)') }}</label>
                <textarea name="summary_vi" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">{{ old('summary_vi') }}</textarea>
                @error('summary_vi')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Content (English)') }}</label>
                <textarea name="content_en" rows="8" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">{{ old('content_en') }}</textarea>
                @error('content_en')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Content (Vietnamese)') }}</label>
                <textarea id="content_vi_editor" name="content_vi" rows="8" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">{{ old('content_vi') }}</textarea>
                @error('content_vi')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Status') }}</label>
                <select name="status" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                    @php $selectedStatus = old('status', 'draft'); @endphp
                    <option value="draft" {{ $selectedStatus === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="published" {{ $selectedStatus === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                </select>
                @error('status')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Thumbnail') }}</label>
                <input type="file" name="thumbnail" accept="image/*" style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                <p style="font-size: 0.9em; color: #666; margin-top: 6px;">{{ __('Used as the main cover image (converted to WebP).') }}</p>
                @error('thumbnail')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px;">{{ __('Inline Images') }}</label>
                <input type="file" name="inline_images[]" accept="image/*" multiple style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
                <p style="font-size: 0.9em; color: #666; margin-top: 6px;">{{ __('Uploaded images will be processed via MediaService; URLs will show after save to embed inside content.') }}</p>
                @error('inline_images')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
                @error('inline_images.*')
                    <div style="color: #e74c3c; margin-top: 6px; font-size: 0.9em;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <a href="{{ route('admin.news.index') }}" style="padding: 10px 16px; background: #e0e0e0; color: #2c3e50; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Cancel') }}
            </a>
            <button type="submit" style="padding: 10px 18px; background: #27ae60; color: white; border: none; border-radius: 6px; font-weight: 700; cursor: pointer;">
                {{ __('Save News') }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/hscvoj37b41mksp8hrr521qn3ma15a8hhz80swo1ofa0m1xe/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
(() => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrf) return;

    const endpoint = "{{ route('admin.news.translate') }}";
    const map = [
        { source: 'title_vi', target: 'title_en' },
        { source: 'summary_vi', target: 'summary_en' },
    ];

    const debounce = (fn, delay = 500) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    };

    // Tự động dịch VI -> EN sau khi dừng gõ 3s
    const translateField = debounce(async (sourceEl, targetEl) => {
        const text = sourceEl.value.trim();
        if (!text) return;
        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ text, from: 'vi', to: 'en' }),
            });
            if (!res.ok) return;
            const data = await res.json();
            if (data.translated && !targetEl.value.trim()) {
                targetEl.value = data.translated;
            }
        } catch (e) {
            // silent fail
        }
    }, 3000);

    // Tự động generate slug từ Title (Vietnamese) sau 100ms và khóa ô slug
    const titleViEl = document.querySelector('[name="title_vi"]');
    const slugEl = document.querySelector('[name="slug"]');
    let slugTouched = false;

    const slugify = (text) => {
        return text
            .toString()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            || Math.random().toString(36).substring(2, 10);
    };

    if (slugEl) {
        slugEl.readOnly = true;
        slugEl.style.backgroundColor = '#f5f5f5';
        slugEl.addEventListener('focus', () => { slugTouched = true; });
        slugEl.addEventListener('input', () => { slugTouched = true; });
    }

    if (titleViEl && slugEl) {
        const updateSlug = debounce(() => {
            const text = titleViEl.value.trim();
            if (!text) return;
            if (slugTouched) return;
            slugEl.value = slugify(text);
        }, 100);

        titleViEl.addEventListener('input', updateSlug);
    }

    // Đồng bộ TinyMCE (VI) -> Content English y chang
    if (window.tinymce) {
        tinymce.init({
            selector: 'textarea#content_vi_editor',
            plugins: 'code table lists',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table',
            menubar: false,
            branding: false,
            height: 400,
            setup: function (editor) {
                const contentEnEl = document.querySelector('[name="content_en"]');
                if (!contentEnEl) return;

                const localDebounce = (fn, delay = 500) => {
                    let timer;
                    return (...args) => {
                        clearTimeout(timer);
                        timer = setTimeout(() => fn(...args), delay);
                    };
                };

                const syncContent = localDebounce(() => {
                    contentEnEl.value = editor.getContent();
                }, 500);

                editor.on('keyup change', syncContent);
            },
        });
    }

    map.forEach(({ source, target }) => {
        const sourceEl = document.querySelector(`[name="${source}"]`);
        const targetEl = document.querySelector(`[name="${target}"]`);
        if (!sourceEl || !targetEl) return;
        // Gõ xong 3s (không gõ thêm) sẽ tự dịch nếu ô EN đang trống
        sourceEl.addEventListener('input', () => translateField(sourceEl, targetEl));
    });
})();
</script>
@endpush
@endsection

