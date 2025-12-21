@props([
    'title' => null,
    'description' => null,
])

<div class="bg-white rounded-lg border border-gray-200 shadow-sm">
    @if($title || $description)
        <div class="p-6 border-b border-gray-200">
            @if($title)
                <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
            @endif
            @if($description)
                <p class="text-sm text-gray-500">{{ $description }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">
        {{ $slot }}
    </div>
</div>

