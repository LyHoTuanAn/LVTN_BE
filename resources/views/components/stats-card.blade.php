@props([
    'title',
    'value',
    'change' => null,
])

<div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm">
    <div class="flex flex-row items-center justify-between pb-2">
        <h3 class="text-sm font-medium text-gray-600">{{ $title }}</h3>
    </div>
    <div class="text-2xl font-bold text-gray-900">{{ $value }}</div>
    @if($change)
        <p class="text-xs text-gray-500">{{ $change }}</p>
    @endif
</div>

