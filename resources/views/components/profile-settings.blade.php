<form class="space-y-4 max-w-sm">
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
        <input type="text" id="name" placeholder="{{ __('John Doe') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
    </div>
    <div class="space-y-2">
        <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
        <input type="email" id="email" placeholder="{{ __('john@example.com') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
    </div>
    <div class="space-y-2">
        <label for="bio" class="block text-sm font-medium text-gray-700">{{ __('Bio') }}</label>
        <input type="text" id="bio" placeholder="{{ __('A short bio about yourself') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
    </div>
    <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800 transition-colors">
        {{ __('Save Changes') }}
    </button>
</form>

