@extends('layouts.app')

@section('title', __('News Details'))
@section('page-title', __('News Details'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
        <h2 style="color: #2c3e50; font-size: 1.5em; margin: 0;">{{ __('News Details') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.news.index') }}" style="padding: 10px 16px; background: #e0e0e0; color: #2c3e50; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.news.edit', $news->id) }}" style="padding: 10px 16px; background: #f39c12; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">
                {{ __('Edit') }}
            </a>
        </div>
    </div>

    <div style="display: grid; gap: 24px;">
        <!-- Thumbnail and Status -->
        <div style="display: flex; gap: 16px; align-items: flex-start; justify-content: space-between;">
            <div style="flex: 1;">
                @if ($news->thumbnail)
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Thumbnail') }}</label>
                    <img src="{{ asset('storage/' . $news->thumbnail->file_path) }}" alt="Thumbnail" style="max-width: 100%; max-height: 400px; border-radius: 8px; border: 1px solid #ddd;">
                @endif
            </div>
            <div style="flex-shrink: 0; align-self: flex-start;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Status') }}</label>
                @php
                    $statusColors = [
                        'draft' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                        'published' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                    ];
                @endphp
                <span style="padding: 6px 12px; background: {{ $statusColors[$news->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$news->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.9em; text-transform: capitalize; display: inline-block;">
                    {{ __($news->status === 'draft' ? 'Draft' : 'Published') }}
                </span>
            </div>
        </div>

        <!-- Title -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Title (English)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $news->title_en }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Title (Vietnamese)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $news->title_vi }}
                </div>
            </div>
        </div>

        <!-- Slug -->
        <div>
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Slug') }}</label>
            <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; font-family: monospace;">
                {{ $news->slug }}
            </div>
        </div>

        <!-- Summary -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Summary (English)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; min-height: 60px;">
                    {!! nl2br(e(trim($news->summary_en ?: __('No summary')))) !!}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Summary (Vietnamese)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; min-height: 60px;">
                    {!! nl2br(e(trim($news->summary_vi ?: __('No summary')))) !!}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Content (English)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; min-height: 200px;">
                    {!! $news->content_en ?: __('No content') !!}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Content (Vietnamese)') }}</label>
                <div style="padding: 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6; min-height: 200px;">
                    {!! $news->content_vi ?: __('No content') !!}
                </div>
            </div>
        </div>

        <!-- Metadata -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding-top: 16px; border-top: 1px solid #dee2e6;">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Author') }}</label>
                <div style="padding: 8px 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $news->author->name ?? __('Unknown') }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Created At') }}</label>
                <div style="padding: 8px 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $news->created_at?->format('Y-m-d H:i:s') }}
                </div>
            </div>
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #2c3e50;">{{ __('Updated At') }}</label>
                <div style="padding: 8px 12px; background: #f8f9fa; border-radius: 6px; border: 1px solid #dee2e6;">
                    {{ $news->updated_at?->format('Y-m-d H:i:s') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

