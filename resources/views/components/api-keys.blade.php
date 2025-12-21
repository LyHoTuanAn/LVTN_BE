@php
$apiKeys = [
    ['id' => 1, 'name' => 'Production Key', 'key' => 'pk_live_1234567890abcdef', 'created' => '2023-01-15'],
    ['id' => 2, 'name' => 'Test Key', 'key' => 'pk_test_1234567890abcdef', 'created' => '2023-02-20'],
];
@endphp

<div class="space-y-4">
    <div class="flex items-center gap-2">
        <input type="text" placeholder="Enter key name" class="px-3 py-2 border border-gray-300 rounded-md text-sm max-w-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
        <button class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition-colors">
            Generate New Key
        </button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="text-left py-3 px-4 font-medium text-gray-600">Name</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600">Key</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600">Created</th>
                    <th class="text-left py-3 px-4 font-medium text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($apiKeys as $apiKey)
                <tr class="border-b border-gray-100">
                    <td class="py-3 px-4 text-gray-900">{{ $apiKey['name'] }}</td>
                    <td class="py-3 px-4 text-gray-600 font-mono text-xs">{{ $apiKey['key'] }}</td>
                    <td class="py-3 px-4 text-gray-600">{{ $apiKey['created'] }}</td>
                    <td class="py-3 px-4">
                        <button class="text-gray-600 hover:text-red-600 text-sm">Revoke</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

