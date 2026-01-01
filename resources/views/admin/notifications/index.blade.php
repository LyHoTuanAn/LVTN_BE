@extends('layouts.app')

@section('title', 'Push Notifications')
@section('page-title', 'Push Notifications')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">🔔 Push Notifications</h1>
        <p class="text-gray-600 mt-1">Gửi thông báo đẩy đến người dùng ứng dụng di động</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Send to All Users -->
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <div class="flex items-center mb-4">
                <div class="bg-blue-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Broadcast - Gửi đến tất cả</h2>
                    <p class="text-sm text-gray-500">Sử dụng Topic Messaging để gửi đến tất cả users</p>
                </div>
            </div>
            
            <form action="{{ route('admin.notifications.send-to-all') }}" method="POST" class="flex flex-col flex-grow">
                @csrf
                <div class="space-y-4 flex-grow">
                    <div>
                        <label for="broadcast_title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" name="title" id="broadcast_title" required maxlength="255"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập tiêu đề thông báo">
                    </div>
                    <div>
                        <label for="broadcast_body" class="block text-sm font-medium text-gray-700 mb-1">Nội dung *</label>
                        <textarea name="body" id="broadcast_body" rows="6" style="height:171px;" required maxlength="1000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nhập nội dung thông báo"></textarea>
                    </div>
                    <div>
                        <label for="broadcast_image" class="block text-sm font-medium text-gray-700 mb-1">URL Hình ảnh (tùy chọn)</label>
                        <input type="url" name="image_url" id="broadcast_image" maxlength="500"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors mt-4">
                    📢 Gửi đến tất cả người dùng
                </button>
            </form>
        </div>

        <!-- Send to Specific User -->
        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
            <div class="flex items-center mb-4">
                <div class="bg-green-100 p-3 rounded-full mr-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Gửi đến người dùng cụ thể</h2>
                    <p class="text-sm text-gray-500">Chọn một người dùng để gửi thông báo</p>
                </div>
            </div>

            <form action="{{ route('admin.notifications.send-to-user') }}" method="POST" class="flex flex-col flex-grow">
                @csrf
                <div class="space-y-4 flex-grow">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Chọn người dùng *</label>
                        <select name="user_id" id="user_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
                            <option value="">-- Chọn người dùng --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->name }} ({{ $user->email }})
                                    @if($user->role) - {{ $user->role->name }} @endif
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="user_title" class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề *</label>
                        <input type="text" name="title" id="user_title" required maxlength="255"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Nhập tiêu đề thông báo">
                    </div>
                    <div>
                        <label for="user_body" class="block text-sm font-medium text-gray-700 mb-1">Nội dung *</label>
                        <textarea name="body" id="user_body" rows="3" required maxlength="1000"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Nhập nội dung thông báo"></textarea>
                    </div>
                    <div>
                        <label for="user_image" class="block text-sm font-medium text-gray-700 mb-1">URL Hình ảnh (tùy chọn)</label>
                        <input type="url" name="image_url" id="user_image" maxlength="500"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="https://example.com/image.jpg">
                    </div>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors mt-4">
                    👤 Gửi đến người dùng
                </button>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
        <p class="text-sm text-yellow-800">
            <strong>💡 Lưu ý:</strong> Để nhận được thông báo broadcast, Flutter app cần subscribe vào topic <code class="bg-yellow-100 px-1 rounded">celes_all_users</code> sau khi đăng nhập.
        </p>
    </div>
</div>
@endsection
