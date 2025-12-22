# HƯỚNG DẪN PHÁT TRIỂN DỰ ÁN - HỆ THỐNG ĐẶT VÉ XEM PHIM

## 📋 MỤC LỤC

1. [Tổng quan dự án](#tổng-quan-dự-án)
2. [Cấu trúc thư mục dự án](#cấu-trúc-thư-mục-dự-án)
3. [Cấu trúc Database](#cấu-trúc-database)
4. [Quy tắc API](#quy-tắc-api)
5. [Cấu trúc Response](#cấu-trúc-response)
6. [Service Pattern](#service-pattern)
7. [Quy tắc viết Code](#quy-tắc-viết-code)
8. [Documentation](#documentation)
9. [Quy trình làm việc](#quy-trình-làm-việc)

---

## 🎯 TỔNG QUAN DỰ ÁN

### Công nghệ sử dụng
- **Framework**: Laravel 12
- **Database**: MySQL
- **Authentication**: JWT (tymon/jwt-auth)
- **API**: RESTful API cho Mobile App (Flutter)
- **Web**: Admin Panel + Public Website

### Kiến trúc
- **Service Pattern**: Tất cả business logic đặt trong Services, Controllers chỉ gọi Services
- **Response Format**: Thống nhất format response cho tất cả API
- **i18n**: Hỗ trợ đa ngôn ngữ (Tiếng Việt và Tiếng Anh)

---

## 📁 CẤU TRÚC THƯ MỤC DỰ ÁN

### Cấu trúc hoàn chỉnh

```
LVTN_BE/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API Controllers cho Mobile
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── MovieController.php
│   │   │   │   ├── CinemaController.php
│   │   │   │   ├── ShowtimeController.php
│   │   │   │   └── BookingController.php
│   │   │   ├── Admin/            # Admin Controllers
│   │   │   │   ├── MovieController.php
│   │   │   │   ├── CinemaController.php
│   │   │   │   ├── ShowtimeController.php
│   │   │   │   ├── BookingController.php
│   │   │   │   ├── RoleController.php
│   │   │   │   └── PermissionController.php
│   │   │   ├── Partner/          # Partner Controllers
│   │   │   │   ├── CinemaController.php
│   │   │   │   ├── ShowtimeController.php
│   │   │   │   └── BookingController.php
│   │   │   └── Web/              # Public Web Controllers
│   │   │       ├── MovieController.php
│   │   │       └── BookingController.php
│   │   ├── Middleware/
│   │   │   ├── ApiKeyMiddleware.php
│   │   │   ├── LanguageMiddleware.php
│   │   │   ├── RoleMiddleware.php
│   │   │   └── PermissionMiddleware.php
│   │   ├── Requests/             # Form Requests
│   │   │   ├── Auth/
│   │   │   │   ├── LoginRequest.php
│   │   │   │   └── RegisterRequest.php
│   │   │   ├── Booking/
│   │   │   │   └── CreateBookingRequest.php
│   │   │   └── ...
│   │   ├── Resources/            # API Resources
│   │   │   ├── UserResource.php
│   │   │   ├── MovieResource.php
│   │   │   ├── CinemaResource.php
│   │   │   ├── BookingResource.php
│   │   │   ├── ShowtimeResource.php
│   │   │   └── ...
│   │   ├── Responses/
│   │   │   └── ApiResponse.php
│   │   └── Traits/
│   │       └── ApiResponseTrait.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Role.php
│   │   ├── Permission.php
│   │   ├── Movie.php
│   │   ├── Cinema.php
│   │   ├── Room.php
│   │   ├── Seat.php
│   │   ├── Showtime.php
│   │   ├── Booking.php
│   │   ├── BookingSeat.php
│   │   ├── Review.php
│   │   ├── FavoriteMovie.php
│   │   ├── Voucher.php
│   │   ├── MediaFolder.php
│   │   └── MediaFile.php
│   ├── Services/                 # Service Layer
│   │   ├── Auth/
│   │   │   ├── AuthService.php
│   │   │   └── PasswordResetService.php
│   │   ├── Movie/
│   │   │   ├── MovieService.php
│   │   │   └── MovieSearchService.php
│   │   ├── Cinema/
│   │   │   ├── CinemaService.php
│   │   │   ├── RoomService.php
│   │   │   └── SeatService.php
│   │   ├── Showtime/
│   │   │   └── ShowtimeService.php
│   │   ├── Booking/
│   │   │   ├── BookingService.php
│   │   │   └── BookingValidationService.php
│   │   ├── Role/
│   │   │   └── RoleService.php
│   │   ├── Permission/
│   │   │   └── PermissionService.php
│   │   └── Authorization/
│   │       └── AuthorizationService.php
│   ├── Helpers/                  # Helper Classes
│   │   └── LanguageHelper.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php
├── config/
│   ├── api.php
│   ├── jwt.php
│   └── ...
├── database/
│   ├── migrations/
│   │   ├── xxxx_create_roles_table.php
│   │   ├── xxxx_create_permissions_table.php
│   │   ├── xxxx_create_role_permissions_table.php
│   │   ├── xxxx_create_users_table.php
│   │   ├── xxxx_create_media_folders_table.php
│   │   ├── xxxx_create_media_files_table.php
│   │   ├── xxxx_create_movies_table.php
│   │   ├── xxxx_create_cinemas_table.php
│   │   ├── xxxx_create_rooms_table.php
│   │   ├── xxxx_create_seats_table.php
│   │   ├── xxxx_create_showtimes_table.php
│   │   ├── xxxx_create_bookings_table.php
│   │   ├── xxxx_create_booking_seats_table.php
│   │   ├── xxxx_create_reviews_table.php
│   │   ├── xxxx_create_favorite_movies_table.php
│   │   └── xxxx_create_vouchers_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RoleSeeder.php
│       ├── PermissionSeeder.php
│       └── RolePermissionSeeder.php
├── doc/
│   ├── api/                      # API Documentation (Markdown)
│   │   ├── auth-otp.md
│   │   ├── example-booking-create.md
│   │   └── ...
│   ├── html/                     # API Documentation (HTML)
│   │   ├── auth-otp.html
│   │   ├── example-booking-create.html
│   │   ├── instructions.html
│   │   └── ...
│   └── task/                     # Task Documentation
│       └── ...
├── resources/
│   ├── lang/
│   │   ├── en/
│   │   │   ├── errors.php
│   │   │   ├── success.php
│   │   │   ├── validation.php
│   │   │   └── en.json          # JSON translations cho tiếng Anh
│   │   └── vi/
│   │       ├── errors.php
│   │       ├── success.php
│   │       ├── validation.php
│   │       └── vi.json          # JSON translations cho tiếng Việt
│   └── views/
├── routes/
│   ├── api.php
│   └── web.php
├── storage/
├── tests/
├── .env
├── composer.json
├── INSTRUCTIONS.md
└── b.sql
```

### Mô tả các thư mục chính

- **`app/Http/Controllers/Api/`**: Controllers cho Mobile App API
- **`app/Http/Controllers/Admin/`**: Controllers cho Admin Panel
- **`app/Http/Controllers/Partner/`**: Controllers cho Partner Dashboard
- **`app/Http/Controllers/Web/`**: Controllers cho Public Website
- **`app/Http/Resources/`**: API Resources để format dữ liệu trả về
- **`app/Http/Middleware/`**: Custom Middleware
- **`app/Http/Requests/`**: Form Request Validation
- **`app/Services/`**: Business Logic Layer
- **`app/Models/`**: Eloquent Models
- **`app/Helpers/`**: Helper Classes
- **`doc/api/`**: API Documentation (Markdown) - File gốc
- **`doc/html/`**: API Documentation (HTML) - File HTML được convert từ Markdown để hiển thị trên web
- **`doc/task/`**: Task Documentation
- **`resources/lang/`**: i18n files

---

## 🗄️ CẤU TRÚC DATABASE

### Schema chính
Database schema được định nghĩa trong file `b.sql`. Các bảng chính:

#### 1. Phân quyền
- `roles` - Vai trò (admin, partner, customer)
- `permissions` - Quyền hạn
- `role_permissions` - Phân quyền cho từng role
- `users` - Người dùng (có `role_id`, `avatar_id`)

#### 2. Media System
- `media_folders` - Thư mục media
- `media_files` - File media (ảnh, video)

#### 3. Core Business
- `movies` - Phim
- `cinemas` - Rạp chiếu phim
- `rooms` - Phòng chiếu
- `seats` - Ghế ngồi
- `showtimes` - Suất chiếu
- `bookings` - Đặt vé
- `booking_seats` - Ghế đã đặt

#### 4. Reviews & Favorites
- `reviews` - Đánh giá phim
- `favorite_movies` - Phim yêu thích

#### 5. Vouchers
- `vouchers` - Mã giảm giá

### Quy tắc khi tạo Migration

✅ **BẮT BUỘC:**
1. **Tất cả Foreign Keys PHẢI có Index**
   ```php
   $table->foreign('user_id')->references('id')->on('users');
   $table->index('user_id'); // BẮT BUỘC
   ```

2. **Tất cả cột thường query PHẢI có Index**
   - `status`, `email`, `code`, `slug`, `name`, `date`, `created_at`
   - Composite indexes cho các query phức tạp

3. **Soft Deletes cho các bảng quan trọng**
   ```php
   $table->softDeletes();
   $table->index('deleted_at');
   ```

4. **ENUM cho các trường có giá trị cố định**
   ```php
   $table->enum('status', ['active', 'inactive'])->default('active');
   ```

5. **UNIQUE constraints cho các trường không được trùng**
   ```php
   $table->unique(['cinema_id', 'name']); // Không được trùng tên phòng trong cùng rạp
   ```

6. **NOT NULL cho các trường bắt buộc**
   ```php
   $table->string('name')->nullable(); // Chỉ nullable khi thực sự cần
   ```

### Ví dụ Migration đúng chuẩn

```php
Schema::create('showtimes', function (Blueprint $table) {
    $table->id();
    $table->foreignId('movie_id')->constrained()->onDelete('restrict');
    $table->foreignId('room_id')->constrained()->onDelete('restrict');
    $table->date('date');
    $table->time('start_time');
    $table->time('end_time');
    $table->decimal('price', 10, 2);
    $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');
    $table->timestamps();
    $table->softDeletes();
    
    // Indexes BẮT BUỘC
    $table->index('movie_id');
    $table->index('room_id');
    $table->index('date');
    $table->index('status');
    $table->index(['date', 'start_time']);
    $table->index(['movie_id', 'date']);
    $table->index('deleted_at');
    
    // UNIQUE constraint
    $table->unique(['room_id', 'date', 'start_time']);
});
```

---

## 🔐 QUY TẮC API

### Headers bắt buộc

**TẤT CẢ API phải có 2 headers sau:**

1. **X-Api-Key**: API key do server cấp
   ```
   X-Api-Key: your-api-key-here
   ```

2. **Language**: Ngôn ngữ (en hoặc vi), mặc định: en
   ```
   Language: vi
   hoặc
   Language: en
   ```

### Middleware Order

Thứ tự middleware áp dụng:
```
LanguageMiddleware → ApiKeyMiddleware → JWT → Role → Permission
```

**Lưu ý quan trọng:** `LanguageMiddleware` PHẢI chạy trước `ApiKeyMiddleware` để đảm bảo message error được dịch đúng ngôn ngữ khi API key không hợp lệ.

---

## 📤 CẤU TRÚC RESPONSE

### Format thống nhất

**TẤT CẢ API phải trả về cùng 1 format:**

#### Response thành công:
```json
{
  "success": true,
  "code": "USER_CREATED_SUCCESS",
  "message": "User created successfully",
  "data": {
    "id": 10,
    "name": "Nguyễn Văn A"
  }
}
```

#### Response lỗi:
```json
{
  "success": false,
  "code": "EMAIL_EXISTS",
  "message": "The email has already been taken",
  "errors": {
    "email": "This email is already in use"
  }
}
```

### Quy tắc

1. **Luôn có `code`**: Cả success và error đều phải có `code`
2. **Message theo ngôn ngữ**: Lấy từ `resources/lang/{locale}/errors.php` hoặc `success.php`
3. **Không dùng text tự do**: Tất cả message phải định nghĩa trong file lang

### API Resources

**TẤT CẢ dữ liệu trả về PHẢI sử dụng API Resources để format.**

- Đặt trong `app/Http/Resources/`
- Mỗi Model có 1 Resource tương ứng
- Resource format dữ liệu trước khi trả về

#### Cấu trúc thư mục

```
app/Http/Resources/
├── UserResource.php
├── MovieResource.php
├── CinemaResource.php
├── BookingResource.php
├── ShowtimeResource.php
└── ...
```

#### Ví dụ Resource

```php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'role' => new RoleResource($this->whenLoaded('role')),
            'avatar' => new MediaFileResource($this->whenLoaded('avatar')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

#### Sử dụng trong Controller

```php
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    use ApiResponseTrait;
    
    public function register(Request $request)
    {
        // ... logic tạo user
        
        return $this->successResponse(
            'USER_CREATED_SUCCESS',
            new UserResource($user),
            'User created successfully'
        );
    }
    
    public function me()
    {
        $user = auth()->user();
        $user->load(['role', 'avatar']); // Eager load relationships
        
        return $this->successResponse(
            'USER_FETCHED_SUCCESS',
            new UserResource($user),
            'User fetched successfully'
        );
    }
}
```

#### Collection Resource

Khi trả về danh sách, sử dụng `ResourceCollection`:

```php
use App\Http\Resources\MovieResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MovieController extends Controller
{
    use ApiResponseTrait;
    
    public function index(Request $request)
    {
        $movies = $this->movieService->getAllMovies($request->all());
        
        return $this->successResponse(
            'MOVIES_FETCHED_SUCCESS',
            MovieResource::collection($movies),
            'Movies fetched successfully'
        );
    }
}
```

#### Quy tắc viết Resource

1. **Chỉ trả về dữ liệu cần thiết**, không trả về password, token, etc.
2. **Sử dụng `whenLoaded()`** để chỉ include relationship khi đã eager load
3. **Format dữ liệu** (dates, numbers, etc.) trong Resource
4. **Nested Resources** cho relationships phức tạp
5. **Conditional attributes** với `when()` nếu cần

```php
class BookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'price' => (float) $this->price,
            'total_price' => (float) $this->total_price,
            'payment_method' => $this->payment_method,
            'user' => new UserResource($this->whenLoaded('user')),
            'showtime' => new ShowtimeResource($this->whenLoaded('showtime')),
            'seats' => SeatResource::collection($this->whenLoaded('seats')),
            'voucher' => new VoucherResource($this->whenLoaded('voucher')),
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ];
    }
}
```

---

## 🏗️ SERVICE PATTERN

### Nguyên tắc

**Controllers KHÔNG được chứa business logic. Tất cả logic đặt trong Services.**

### Cấu trúc thư mục

```
app/Services/
├── Auth/
│   └── AuthService.php
├── Movie/
│   ├── MovieService.php
│   └── MovieSearchService.php
├── Booking/
│   ├── BookingService.php
│   └── BookingValidationService.php
├── Cinema/
│   ├── CinemaService.php
│   ├── RoomService.php
│   └── SeatService.php
├── Showtime/
│   └── ShowtimeService.php
├── Role/
│   └── RoleService.php
├── Permission/
│   └── PermissionService.php
└── Authorization/
    └── AuthorizationService.php
```

### Quy tắc viết Service

1. **Service trực tiếp làm việc với Model** (không dùng Repository pattern)
2. **Mỗi Service class chỉ xử lý 1 domain cụ thể**
3. **Service methods phải có tên rõ ràng, mô tả đúng chức năng**
4. **Service trả về data, không trả về Response**

### Ví dụ Service

```php
namespace App\Services\Movie;

use App\Models\Movie;
use Illuminate\Support\Facades\DB;

class MovieService
{
    public function getAllMovies(array $filters = [])
    {
        $query = Movie::query();
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }
        
        return $query->with(['poster', 'trailer'])->paginate(15);
    }
    
    public function createMovie(array $data): Movie
    {
        return DB::transaction(function () use ($data) {
            return Movie::create($data);
        });
    }
}
```

### Ví dụ Controller sử dụng Service và Resource

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Http\Resources\MovieResource;
use App\Services\Movie\MovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use ApiResponseTrait;
    
    protected MovieService $movieService;
    
    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }
    
    public function index(Request $request)
    {
        $movies = $this->movieService->getAllMovies($request->all());
        
        return $this->successResponse(
            'MOVIES_FETCHED_SUCCESS',
            MovieResource::collection($movies),
            'Movies fetched successfully'
        );
    }
    
    public function show($id)
    {
        $movie = $this->movieService->getMovieById($id);
        $movie->load(['poster', 'trailer', 'showtimes']); // Eager load
        
        return $this->successResponse(
            'MOVIE_FETCHED_SUCCESS',
            new MovieResource($movie),
            'Movie fetched successfully'
        );
    }
}
```

---

## 📝 QUY TẮC VIẾT CODE

### 1. Models

#### Relationships
- Định nghĩa đầy đủ relationships
- Sử dụng `$fillable`, `$casts`, `$hidden` phù hợp
- Eager loading để tránh N+1 queries

```php
class Movie extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title',
        'description',
        'duration',
        'release_date',
        'status',
        'poster_id',
        'trailer_id',
    ];
    
    protected $casts = [
        'release_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    // Relationships
    public function poster()
    {
        return $this->belongsTo(MediaFile::class, 'poster_id');
    }
    
    public function trailer()
    {
        return $this->belongsTo(MediaFile::class, 'trailer_id');
    }
    
    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }
}
```

### 2. Controllers

#### Quy tắc
- Chỉ gọi Service, không có business logic
- Sử dụng `ApiResponseTrait` để trả về response
- Validation sử dụng Form Requests
- Tên method rõ ràng: `index`, `store`, `show`, `update`, `destroy`

```php
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    use ApiResponseTrait;
    
    public function store(CreateBookingRequest $request, BookingService $service)
    {
        $booking = $service->createBooking($request->validated(), auth()->id());
        $booking->load(['user', 'showtime', 'seats', 'voucher']); // Eager load
        
        return $this->successResponse(
            'BOOKING_CREATED_SUCCESS',
            new BookingResource($booking),
            'Booking created successfully'
        );
    }
}
```

### 3. Form Requests

#### Validation
- Tạo Form Request cho các endpoint quan trọng
- Validation rules rõ ràng
- Custom messages nếu cần

```php
class CreateBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'showtime_id' => 'required|exists:showtimes,id',
            'seat_ids' => 'required|array|min:1',
            'seat_ids.*' => 'exists:seats,id',
            'voucher_code' => 'nullable|exists:vouchers,code',
        ];
    }
}
```

### 4. Middleware

#### Tạo Middleware mới
- Đặt trong `app/Http/Middleware/`
- Đăng ký trong `bootstrap/app.php`
- Tên rõ ràng, mô tả chức năng

```php
class ApiKeyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Api-Key');
        
        if (!$apiKey || $apiKey !== config('api.key')) {
            return response()->json([
                'success' => false,
                'code' => 'INVALID_API_KEY',
                'message' => __('errors.INVALID_API_KEY'),
            ], 401);
        }
        
        return $next($request);
    }
}
```

#### Đăng ký Middleware trong Config Files

**❌ KHÔNG làm như này:**
```php
// config/api.php
return [
    'middleware' => [
        'api.key' => \App\Http\Middleware\ApiKeyMiddleware::class,
    ],
];
```

**✅ NÊN làm như này:**
```php
// config/api.php
use App\Http\Middleware\ApiKeyMiddleware;

return [
    'middleware' => [
        'api.key' => ApiKeyMiddleware::class,
    ],
];
```

**Quy tắc:**
- Luôn dùng `use` statement ở đầu file config
- Sử dụng class name thay vì full namespace string
- Code rõ ràng, dễ đọc và maintain hơn

---

## 🌐 i18n (Internationalization)

### File cấu trúc

```
resources/lang/
├── en/
│   ├── errors.php
│   ├── success.php
│   ├── validation.php
│   └── en.json          # JSON translations cho tiếng Anh
└── vi/
    ├── errors.php
    ├── success.php
    ├── validation.php
    └── vi.json          # JSON translations cho tiếng Việt
```

### Format file errors.php

```php
return [
    'INVALID_API_KEY' => 'Invalid API key',
    'EMAIL_EXISTS' => 'The email has already been taken',
    'LOGIN_FAILED' => 'Invalid credentials',
    // ...
];
```

### Format file success.php

```php
return [
    'USER_CREATED_SUCCESS' => 'User created successfully',
    'LOGIN_SUCCESS' => 'Login successful',
    'BOOKING_CREATED_SUCCESS' => 'Booking created successfully',
    // ...
];
```

### JSON Translations (vi.json và en.json)

**Laravel load các file JSON translations từ `resources/lang/{locale}/{locale}.json` (được cấu hình trong AppServiceProvider)**

- **`resources/lang/vi/vi.json`**: Chứa bản dịch tiếng Việt
- **`resources/lang/en/en.json`**: Chứa bản dịch tiếng Anh (key = value)

**Format file vi.json:**
```json
{
  "Filter": "Lọc",
  "Search by order code": "Tìm kiếm theo mã đơn hàng",
  "User Management": "Quản lý người dùng",
  "Your OTP Code": "Mã OTP của bạn",
  "Thank you for registering!": "Cảm ơn bạn đã đăng ký!"
}
```

**Format file en.json:**
```json
{
  "Filter": "Filter",
  "Search by order code": "Search by order code",
  "User Management": "User Management",
  "Your OTP Code": "Your OTP Code",
  "Thank you for registering!": "Thank you for registering!"
}
```

**Cách sử dụng:**
```blade
{{-- Code bằng tiếng Anh, tự động dịch theo locale --}}
<label>{{ __('Search by order code') }}</label>
<button class="btn btn-primary" type="submit">{{ __('Filter') }}</button>
```

**Kết quả:**
- Locale = `'en'`: Hiển thị text gốc (tiếng Anh) từ `en.json`
- Locale = `'vi'`: Tự động dịch sang tiếng Việt từ `vi.json`

**Thêm bản dịch mới:**
Chỉ cần thêm vào cả 2 file `resources/lang/vi/vi.json` và `resources/lang/en/en.json`:
```json
// resources/lang/vi/vi.json
{
  "Your new text": "Văn bản mới của bạn"
}

// resources/lang/en/en.json
{
  "Your new text": "Your new text"
}
```

### Sử dụng trong code

```php
// Lấy message theo ngôn ngữ hiện tại (API) - từ errors.php/success.php
$message = __('errors.EMAIL_EXISTS');
$message = __('success.USER_CREATED_SUCCESS');

// Admin Panel / Email - Dùng trực tiếp text tiếng Anh (từ JSON files)
$label = __('Filter'); // Tự động dịch theo locale từ vi.json/en.json
$otpTitle = __('Your OTP Code'); // Tự động dịch theo locale

// Hoặc sử dụng LanguageHelper
use App\Helpers\LanguageHelper;

$message = LanguageHelper::get('errors.EMAIL_EXISTS');
```

**Lưu ý:**
- JSON translations được load từ `resources/lang/{locale}/{locale}.json` (cấu hình trong AppServiceProvider)
- Có thể dùng `__('Text')` trực tiếp, không cần namespace
- File PHP (`errors.php`, `success.php`) dùng cho API messages
- File JSON (`vi/vi.json`, `en/en.json`) dùng cho Admin Panel và Email templates

### Email với i18n (Gửi Email Theo Ngôn Ngữ)

**Khi gửi email (OTP, notifications, etc.), email PHẢI được gửi theo đúng ngôn ngữ mà client yêu cầu trong header `Language`.**

#### Flow gửi email với locale

1. **Client gửi request** với header `Language: vi` hoặc `Language: en`
2. **LanguageMiddleware** set locale cho app từ header này
3. **Controller** lấy locale hiện tại: `app()->getLocale()`
4. **Service** truyền locale vào Mailable class
5. **Email view** set locale trước khi render để dịch đúng ngôn ngữ

#### Ví dụ: Gửi OTP Email

**1. Controller - Lấy locale và truyền vào Service:**

```php
public function register(RegisterRequest $request)
{
    // Locale đã được set bởi LanguageMiddleware từ header Language
    $user = $this->authService->register($request->validated());
    // Service sẽ tự động lấy locale hiện tại
}
```

**2. Service - Lấy locale và truyền vào Mail:**

```php
public function register(array $data): User
{
    $user = User::create($data);
    
    // Lấy locale hiện tại (đã được set bởi LanguageMiddleware)
    $locale = app()->getLocale();
    
    // Truyền locale vào OtpService
    $this->otpService->generateOtp($user->email, 'register', $user->id, $locale);
    
    return $user;
}
```

**3. OtpService - Truyền locale vào Mailable:**

```php
public function generateOtp(string $email, string $type, ?int $userId = null, ?string $locale = null): Otp
{
    // ... tạo OTP ...
    
    // Truyền locale vào email
    $this->sendOtpEmail($otp, $locale);
    
    return $otp;
}

public function sendOtpEmail(Otp $otp, ?string $locale = null): void
{
    Mail::to($otp->email)->send(new OtpMail($otp, $locale));
}
```

**4. Mailable Class - Lưu locale và set trong view:**

```php
class OtpMail extends Mailable
{
    public Otp $otp;
    public ?string $locale;

    public function __construct(Otp $otp, ?string $locale = null)
    {
        $this->otp = $otp;
        $this->locale = $locale ?? App::getLocale();
    }

    public function envelope(): Envelope
    {
        // Set locale cho subject
        $originalLocale = App::getLocale();
        App::setLocale($this->locale);
        
        $subject = __('Your OTP Code');
        
        // Restore original locale
        App::setLocale($originalLocale);
        
        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.otp',
            with: [
                'otpCode' => $this->otp->otp_code,
                'type' => $this->otp->type,
                'expiresIn' => 5,
                'locale' => $this->locale, // Truyền locale vào view
            ],
        );
    }
}
```

**5. Email Blade Template - Set locale trước khi render:**

```blade
@php
    // Set locale cho email view
    $originalLocale = app()->getLocale();
    if (isset($locale)) {
        app()->setLocale($locale);
    }
@endphp

<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Your OTP Code') }}</title>
</head>
<body>
    <h2>{{ __('Your OTP Code') }}</h2>
    <p>{{ $type === 'register' ? __('Thank you for registering!') : __('You have requested to reset your password.') }}</p>
    <p>{{ __('Your OTP code is:') }}</p>
    <h1>{{ $otpCode }}</h1>
    <p>{{ __('This OTP code will expire in :minutes minutes.', ['minutes' => $expiresIn]) }}</p>
</body>
</html>

@php
    // Restore original locale
    app()->setLocale($originalLocale);
@endphp
```

#### Quy tắc

1. **Luôn truyền locale từ Controller → Service → Mail**
2. **Lấy locale từ `app()->getLocale()`** (đã được set bởi LanguageMiddleware)
3. **Set locale trong email view** trước khi render để dịch đúng
4. **Restore locale** sau khi render để không ảnh hưởng đến các request khác
5. **Thêm bản dịch vào `resources/lang/vi/vi.json` và `resources/lang/en/en.json`** cho các text trong email template

#### Kết quả

- Header `Language: vi` → Email tiếng Việt
- Header `Language: en` → Email tiếng Anh
- Không có header → Mặc định tiếng Anh

---

## 🔑 PHÂN QUYỀN

### Roles mặc định

1. **admin**: Quản trị viên hệ thống
2. **partner**: Đối tác (quản lý rạp phim)
3. **customer**: Khách hàng

### Permissions

- Permissions được định nghĩa trong bảng `permissions`
- Gán permissions cho roles qua bảng `role_permissions`
- Kiểm tra permission qua `PermissionMiddleware`

### Sử dụng trong Routes

```php
// Admin routes
Route::middleware(['auth:api', 'role:admin', 'permission:manage_movies'])->group(function () {
    Route::apiResource('movies', Admin\MovieController::class);
});

// Partner routes
Route::middleware(['auth:api', 'role:partner'])->group(function () {
    Route::apiResource('cinemas', Partner\CinemaController::class);
});
```

---

## 📚 DOCUMENTATION

### API Documentation

**Sau khi viết xong 1 API, BẮT BUỘC phải viết documentation:**

1. **File Markdown** trong `doc/api/` (ví dụ: `doc/api/auth-otp.md`)
2. **File HTML** trong `doc/html/` (ví dụ: `doc/html/auth-otp.html`) - Convert từ Markdown với dark theme, format đẹp để hiển thị trên web

#### Format chuẩn

Mỗi file documentation phải có format như sau:

```markdown
# [Tên API]

## URL
[Method] [Endpoint]

Ví dụ: `POST /api/bookings`

## Method
`POST` / `GET` / `PUT` / `DELETE` / `PATCH`

## Headers
**Bắt buộc** ✓

- `X-Api-Key`: API key do server cấp
- `Language`: `en` hoặc `vi` (mặc định: `en`)
- `Authorization`: `Bearer {token}` (nếu cần authentication)
- `Content-Type`: `application/json`
- `Accept`: `application/json`

## Body

```json
{
  "showtime_id": 1,
  "seat_ids": [1, 2, 3],
  "voucher_code": "DISCOUNT10"
}
```

## Request Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Miêu tả |
|--------|----------|--------------|---------|
| `showtime_id` | ✓ | Number | ID của suất chiếu |
| `seat_ids` | ✓ | Array | Danh sách ID ghế ngồi (tối thiểu 1 ghế) |
| `seat_ids.*` | ✓ | Number | ID của từng ghế ngồi |
| `voucher_code` | ✗ | String | Mã voucher giảm giá (nếu có) |

### Chi tiết các trường

#### `showtime_id`
- **Bắt buộc**: Có
- **Kiểu**: Number
- **Mô tả**: ID của suất chiếu muốn đặt vé
- **Validation**: Phải tồn tại trong bảng `showtimes`

#### `seat_ids`
- **Bắt buộc**: Có
- **Kiểu**: Array of Numbers
- **Mô tả**: Danh sách ID các ghế muốn đặt
- **Validation**: 
  - Phải là array
  - Tối thiểu 1 phần tử
  - Tất cả ID phải tồn tại trong bảng `seats`
  - Ghế phải thuộc phòng của suất chiếu
  - Ghế chưa được đặt trong suất chiếu đó

#### `voucher_code`
- **Bắt buộc**: Không
- **Kiểu**: String
- **Mô tả**: Mã voucher để giảm giá
- **Validation**: 
  - Nếu có, phải tồn tại trong bảng `vouchers`
  - Voucher phải còn hiệu lực
  - Voucher phải áp dụng được cho user/movie

## Response Success

```json
{
  "success": true,
  "code": "BOOKING_CREATED_SUCCESS",
  "message": "Đặt vé thành công",
  "data": {
    "id": 123,
    "code": "ABC12345",
    "status": "pending",
    "is_paid": false,
    "price": 150000,
    "total_price": 135000,
    "voucher_amount": 15000,
    "user": {
      "id": 10,
      "name": "Nguyễn Văn A"
    },
    "showtime": {
      "id": 1,
      "date": "2024-01-15",
      "start_time": "14:00:00"
    },
    "seats": [
      {
        "id": 1,
        "row": "A",
        "number": 5
      }
    ],
    "created_at": "2024-01-15 10:30:00"
  }
}
```

## Response Fields

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả |
|--------|----------|--------------|-------|
| `success` | ✓ | Boolean | Trạng thái thành công (`true`) |
| `code` | ✓ | String | Mã response (`BOOKING_CREATED_SUCCESS`) |
| `message` | ✓ | String | Thông báo theo ngôn ngữ client yêu cầu |
| `data` | ✓ | Object | Dữ liệu booking đã tạo |
| `data.id` | ✓ | Number | ID của booking |
| `data.code` | ✓ | String | Mã đặt vé (8 ký tự) |
| `data.status` | ✓ | String | Trạng thái: `pending`, `confirmed`, `canceled`, `completed` |
| `data.is_paid` | ✓ | Boolean | Đã thanh toán hay chưa |
| `data.price` | ✓ | Number | Tổng tiền trước giảm (VND) |
| `data.total_price` | ✓ | Number | Tổng tiền sau giảm (VND) |
| `data.voucher_amount` | ✓ | Number | Số tiền được giảm (VND) |
| `data.user` | ✓ | Object | Thông tin user đặt vé |
| `data.showtime` | ✓ | Object | Thông tin suất chiếu |
| `data.seats` | ✓ | Array | Danh sách ghế đã đặt |
| `data.created_at` | ✓ | String | Thời gian tạo (format: YYYY-MM-DD HH:mm:ss) |

## Response Error

### Validation Error

```json
{
  "success": false,
  "code": "VALIDATION_ERROR",
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "showtime_id": "Suất chiếu không tồn tại",
    "seat_ids": "Vui lòng chọn ít nhất 1 ghế"
  }
}
```

### Business Logic Error

```json
{
  "success": false,
  "code": "SEAT_ALREADY_BOOKED",
  "message": "Ghế đã được đặt",
  "errors": {
    "seat_ids": "Một số ghế đã được đặt trong suất chiếu này"
  }
}
```

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `BOOKING_CREATED_SUCCESS` | 201 | Tạo đặt vé thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `SEAT_ALREADY_BOOKED` | 400 | Ghế đã được đặt |
| `SHOWTIME_NOT_FOUND` | 404 | Suất chiếu không tồn tại |
| `VOUCHER_INVALID` | 400 | Voucher không hợp lệ hoặc hết hạn |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập |
| `FORBIDDEN` | 403 | Không có quyền truy cập |

## Postman

### Collection
Import collection từ: [Link hoặc file]

### Environment Variables
- `base_url`: `http://localhost:8000`
- `api_key`: `your-api-key-here`
- `token`: JWT token sau khi login

### Example Request
```
POST {{base_url}}/api/bookings
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json

Body:
{
  "showtime_id": 1,
  "seat_ids": [1, 2, 3],
  "voucher_code": "DISCOUNT10"
}
```

## Success Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `BOOKING_CREATED_SUCCESS` | 201 | Tạo đặt vé thành công |

## Error Codes

| Code | HTTP Status | Mô tả |
|------|-------------|-------|
| `VALIDATION_ERROR` | 422 | Dữ liệu validation không hợp lệ |
| `SEAT_ALREADY_BOOKED` | 400 | Ghế đã được đặt |
| `SHOWTIME_NOT_FOUND` | 404 | Suất chiếu không tồn tại |
| `VOUCHER_INVALID` | 400 | Voucher không hợp lệ hoặc hết hạn |
| `UNAUTHORIZED` | 401 | Chưa đăng nhập |
| `FORBIDDEN` | 403 | Không có quyền truy cập |

## Notes

- Booking code được tự động generate (8 ký tự)
- Status mặc định là `pending`
- Có thể hủy booking trong vòng 30 phút sau khi tạo
- Voucher chỉ áp dụng 1 lần cho mỗi user
```

### Task Documentation

**Sau khi hoàn thành 1 task, ghi lại trong `doc/task/`:**

#### Format chuẩn

```markdown
# Task: [Tên Task]

## Thông tin
- **Ngày**: 2024-01-15
- **Người thực hiện**: Nguyễn Văn A
- **Thời gian**: 4 giờ

## Mô tả
[Miêu tả ngắn gọn về task]

## Công việc đã thực hiện

### 1. [Công việc 1]
- Chi tiết công việc
- Kết quả đạt được

### 2. [Công việc 2]
- Chi tiết công việc
- Kết quả đạt được

## Files đã tạo

### Services
- `app/Services/Auth/AuthService.php` - Service xử lý authentication

### Controllers
- `app/Http/Controllers/Api/AuthController.php` - API endpoints

### Requests
- `app/Http/Requests/Auth/LoginRequest.php` - Validation cho login
- `app/Http/Requests/Auth/RegisterRequest.php` - Validation cho register

### Resources
- `app/Http/Resources/UserResource.php` - Format user data

### Migrations
- `database/migrations/xxxx_add_role_id_to_users_table.php`

### Documentation
- `doc/api/auth.md` - API documentation (Markdown)
- `doc/html/auth.html` - API documentation (HTML)

## Files đã sửa
- `routes/api.php` - Thêm auth routes
- `bootstrap/app.php` - Đăng ký middleware

## Testing
- [x] Test login thành công
- [x] Test login với sai password
- [x] Test register với email đã tồn tại
- [x] Test JWT token generation
- [x] Test API với Postman

## Issues gặp phải
- [Nếu có] Mô tả issue và cách giải quyết

## Notes
- [Ghi chú thêm nếu cần]
```

#### Quy tắc đặt tên file

- Format: `YYYY-MM-DD-[tên-task].md`
- Ví dụ: `2024-01-15-implement-authentication-api.md`

---

## ✅ CHECKLIST KHI TẠO API MỚI

- [ ] Tạo Migration với đầy đủ indexes
- [ ] Tạo Model với relationships đầy đủ
- [ ] Tạo API Resource trong `app/Http/Resources/` để format dữ liệu
- [ ] Tạo Service với business logic
- [ ] Tạo Controller sử dụng Service và Resource
- [ ] Tạo Form Request cho validation (nếu cần)
- [ ] Sử dụng `ApiResponseTrait` cho response
- [ ] Message lấy từ file lang (errors.php/success.php)
- [ ] Eager load relationships trước khi trả về Resource
- [ ] Đăng ký routes với middleware phù hợp
- [ ] Viết API documentation trong `doc/api/` (theo format chuẩn với bảng Request Parameters và Response Fields)
- [ ] Convert documentation sang HTML và lưu vào `doc/html/` (dark theme, format đẹp)
- [ ] Test API với Postman
- [ ] Ghi lại task trong `doc/task/` (theo format chuẩn)
- [ ] **Nếu có gửi email**: Truyền locale từ Controller → Service → Mail, set locale trong email view

## ✅ CHECKLIST KHI TẠO ADMIN PAGE

- [ ] Code bằng tiếng Anh trực tiếp trong Blade
- [ ] Sử dụng `__('Text')` để dịch (chuẩn Laravel)
- [ ] Thêm bản dịch vào `resources/lang/vi/vi.json` và `resources/lang/en/en.json` nếu cần
- [ ] Sử dụng `use` statement trong config files, không dùng full namespace string
- [ ] Test với cả 2 locale (en và vi)
- [ ] Cấu hình favicon dùng version để tránh cache: trong layout chính (vd `resources/views/layouts/app.blade.php`) thêm:
  ```blade
  @php $assetVersion = config('app.asset_version'); @endphp
  <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') . '?v=' . $assetVersion }}">
  ```

---

## 🚫 NHỮNG ĐIỀU KHÔNG ĐƯỢC LÀM

1. ❌ **KHÔNG** viết business logic trong Controller
2. ❌ **KHÔNG** trả về response format khác nhau
3. ❌ **KHÔNG** trả về Model trực tiếp, PHẢI dùng API Resource
4. ❌ **KHÔNG** dùng text tự do trong response message
5. ❌ **KHÔNG** tạo migration thiếu indexes
6. ❌ **KHÔNG** viết code lan man, mở rộng không cần thiết
7. ❌ **KHÔNG** tạo API mà không viết documentation (phải có bảng Request Parameters và Response Fields)
8. ❌ **KHÔNG** commit code chưa test
9. ❌ **KHÔNG** viết documentation thiếu format chuẩn
10. ❌ **KHÔNG** dùng full namespace string trong config files, PHẢI dùng `use` statement
11. ❌ **KHÔNG** tạo helper function tùy chỉnh, PHẢI dùng chuẩn Laravel (`__()`)
12. ❌ **KHÔNG** gửi email mà không truyền locale, PHẢI gửi email theo đúng ngôn ngữ client yêu cầu

---

## 📖 TÀI LIỆU THAM KHẢO

- Laravel Documentation: https://laravel.com/docs
- JWT Auth: https://jwt-auth.readthedocs.io/
- Database Schema: Xem file `b.sql`
- Plan chi tiết: Xem file `.cursor/plans/h-th-ng-t-v-xem-phim-7a39b762.plan.md`

---

## 💡 LƯU Ý QUAN TRỌNG

1. **Luôn tập trung vào vấn đề chính**, tránh viết code lan man
2. **Tái sử dụng code** qua Service pattern
3. **Thống nhất format response** cho tất cả API
4. **Viết documentation** sau mỗi API
5. **Test kỹ** trước khi commit

---

**Chúc các bạn code vui vẻ! 🚀**

