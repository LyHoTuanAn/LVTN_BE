@props([
    'tabKey'
])

<div x-show="activeTab === '{{ $tabKey }}'" x-cloak class="mt-6">
    {{ $slot }}
</div>

