{{-- 
    Ví dụ sử dụng translation trong Blade template
    
    Cách sử dụng:
    1. Code bằng tiếng Anh trực tiếp
    2. Dùng __('Text') để dịch (chuẩn Laravel)
    3. Tự động dịch sang tiếng Việt khi locale là 'vi'
--}}

{{-- Ví dụ 1: Dịch text đơn giản --}}
<h1>{{ __('Hello!') }}</h1>

{{-- Ví dụ 2: Dịch text phức tạp --}}
<h2>{{ __('User Management') }}</h2>

{{-- Ví dụ 3: Trong form --}}
<form>
    <label>{{ __('Name') }}</label>
    <input type="text" name="name" placeholder="{{ __('Enter name') }}">
    
    <label>{{ __('Email') }}</label>
    <input type="email" name="email">
    
    <button type="submit">{{ __('Save') }}</button>
    <button type="button">{{ __('Cancel') }}</button>
</form>

{{-- Ví dụ 4: Trong table --}}
<table>
    <thead>
        <tr>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Email') }}</th>
            <th>{{ __('Role') }}</th>
            <th>{{ __('Actions') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->name }}</td>
            <td>
                <a href="#">{{ __('Edit') }}</a>
                <a href="#">{{ __('Delete') }}</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Ví dụ 5: Thông báo --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ __('Success') }}: 
        {{ __(session('success')) }}
    </div>
@endif

{{-- Ví dụ 6: Button với class --}}
<button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
<button class="btn btn-secondary" type="button">{{ __('Cancel') }}</button>

{{-- 
    Lưu ý:
    - Code bằng tiếng Anh trực tiếp trong Blade
    - Dùng __('Text') để dịch (chuẩn Laravel)
    - Nếu locale là 'en': trả về text gốc (tiếng Anh)
    - Nếu locale là 'vi': dịch sang tiếng Việt từ file JSON
    - Nếu không tìm thấy bản dịch, sẽ trả về text gốc (tiếng Anh)
    - Thêm bản dịch mới vào file resources/lang/translations.json
    - Format: "English Text": "Tiếng Việt"
    
    Ví dụ:
    <label>{{ __('Search by order code') }}</label>
    Thêm vào translations.json: "Search by order code": "Tìm kiếm theo mã đơn hàng"
--}}

