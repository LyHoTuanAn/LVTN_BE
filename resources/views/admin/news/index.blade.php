@extends('layouts.app')

@section('title', __('News Management'))
@section('page-title', __('News Management'))

@section('content')
<div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap;">
        <h2 style="color: #2c3e50; font-size: 1.5em;">{{ __('News List') }}</h2>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <form method="GET" action="{{ route('admin.news.index') }}" class="news-filter-form" style="display: flex; gap: 10px; flex-wrap: wrap;">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $filters['search'] ?? '' }}" 
                    placeholder="{{ __('Search by title or slug...') }}"
                    style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;"
                >
                <select name="status" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 0.9em;">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>{{ __('Draft') }}</option>
                    <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>{{ __('Published') }}</option>
                </select>
                <button type="submit" style="padding: 0 20px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; font-size: 0.9em; line-height: 1; height: 38px; box-sizing: border-box;">
                    {{ __('Search') }}
                </button>
            </form>
            <a 
                href="{{ route('admin.news.create') }}" 
                style="padding: 0 20px; background: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-size: 0.9em; white-space: nowrap; display: inline-flex; align-items: center; justify-content: center; line-height: 1; height: 38px; box-sizing: border-box;"
            >
                + {{ __('Create News') }}
            </a>
        </div>
    </div>

    @if (session('error'))
        <div style="padding: 12px 16px; background: #f8d7da; color: #721c24; border-radius: 6px; margin-bottom: 16px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div style="width: 100%; overflow-x: auto;">
        <table class="responsive-table" style="width: 100%; border-collapse: collapse; min-width: 800px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('ID') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Title') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Status') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Created At') }}</th>
                    <th style="padding: 12px; text-align: left; color: #2c3e50; font-weight: 600;">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($newsItems as $item)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;" data-label="{{ __('ID') }}">{{ $item->id }}</td>
                    <td style="padding: 12px; font-weight: 500;" data-label="{{ __('Title') }}">{{ $item->title_en }} / {{ $item->title_vi }}</td>
                    <td style="padding: 12px;" data-label="{{ __('Status') }}">
                        @php
                            $statusColors = [
                                'draft' => ['bg' => '#fff3e0', 'color' => '#e65100'],
                                'published' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                            ];
                        @endphp
                        <span style="padding: 4px 8px; background: {{ $statusColors[$item->status]['bg'] ?? '#eee' }}; color: {{ $statusColors[$item->status]['color'] ?? '#666' }}; border-radius: 4px; font-size: 0.85em; text-transform: capitalize;">
                            {{ __($item->status === 'draft' ? 'Draft' : 'Published') }}
                        </span>
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Created At') }}">
                        {{ $item->created_at?->format('Y-m-d H:i') }}
                    </td>
                    <td style="padding: 12px;" data-label="{{ __('Actions') }}">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <a 
                                href="{{ route('admin.news.show', $item->id) }}" 
                                style="padding: 0 12px; background: #3498db; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('View') }}
                            </a>
                            <a 
                                href="{{ route('admin.news.edit', $item->id) }}" 
                                style="padding: 0 12px; background: #f39c12; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85em; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;"
                            >
                                {{ __('Edit') }}
                            </a>
                            <form method="POST" action="{{ route('admin.news.destroy', $item->id) }}" style="display: inline;" onsubmit="return confirm('{{ __('Are you sure you want to delete this news?') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 0 12px; background: #e74c3c; color: white; border: none; border-radius: 4px; font-size: 0.85em; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; height: 32px; line-height: 1; box-sizing: border-box;">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #666;">
                        {{ __('No news found') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $newsItems->links() }}
    </div>
</div>
@endsection

<style>
    @media (max-width: 768px) {
        .news-filter-form {
            flex-direction: column;
            align-items: stretch;
        }
        .news-filter-form input,
        .news-filter-form select,
        .news-filter-form button {
            width: 100%;
        }
        .responsive-table {
            min-width: unset !important;
        }
        .responsive-table thead {
            display: none;
        }
        .responsive-table tr {
            display: block;
            margin-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        .responsive-table td {
            display: flex;
            justify-content: space-between;
            padding: 10px 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #555;
            margin-right: 10px;
        }
    }
</style>

