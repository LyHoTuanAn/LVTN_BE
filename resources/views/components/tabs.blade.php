@props([
    'defaultTab' => 'tab1',
    'tabs' => []
])

<div x-data="{ activeTab: '{{ $defaultTab }}' }">
    <div class="border-b border-gray-200">
        <nav class="flex gap-4">
            @foreach($tabs as $tabKey => $tabLabel)
                <button 
                    @click="activeTab = '{{ $tabKey }}'" 
                    :class="activeTab === '{{ $tabKey }}' ? 'border-b-2 border-gray-900 text-gray-900' : 'text-gray-500'" 
                    class="py-2 px-1 text-sm font-medium"
                >
                    {{ $tabLabel }}
                </button>
            @endforeach
        </nav>
    </div>
    
    {{ $slot }}
</div>

