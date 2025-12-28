# 🏠 Home API

API endpoint để lấy dữ liệu cho màn hình home của mobile app.

## URL

`GET /api/home`

## Method

`GET`

## Headers

**Bắt buộc** ✓

-   `X-Api-Key`: API key do server cấp
-   `Language`: `en` hoặc `vi` (mặc định: `en`)
-   `Accept`: `application/json`

## Query Parameters

| Trường              | Bắt buộc | Kiểu dữ liệu | Mô tả                    | Mặc định |
| ------------------- | -------- | ------------ | ------------------------ | -------- |
| `now_showing_limit` | ✗        | Number       | Số lượng phim đang chiếu | `10`     |
| `upcoming_limit`    | ✗        | Number       | Số lượng phim sắp chiếu  | `10`     |
| `coming_soon_limit` | ✗        | Number       | Số lượng phim sắp ra mắt | `10`     |
| `room_limit`        | ✗        | Number       | Số lượng phòng chiếu     | `10`     |
| `news_limit`        | ✗        | Number       | Số lượng tin tức         | `10`     |

### Chi tiết các trường

#### `now_showing_limit`

-   **Bắt buộc**: Không
-   **Kiểu**: Number
-   **Mô tả**: Số lượng phim đang chiếu (có suất chiếu `ongoing` hoặc `scheduled` hôm nay)
-   **Mặc định**: `10`
-   **Giá trị hợp lệ**: 1-50

#### `upcoming_limit`

-   **Bắt buộc**: Không
-   **Kiểu**: Number
-   **Mô tả**: Số lượng phim sắp chiếu (có suất chiếu `scheduled` trong tương lai)
-   **Mặc định**: `10`
-   **Giá trị hợp lệ**: 1-50

#### `coming_soon_limit`

-   **Bắt buộc**: Không
-   **Kiểu**: Number
-   **Mô tả**: Số lượng phim sắp ra mắt (chưa có suất chiếu)
-   **Mặc định**: `10`
-   **Giá trị hợp lệ**: 1-50

#### `room_limit`

-   **Bắt buộc**: Không
-   **Kiểu**: Number
-   **Mô tả**: Số lượng phòng chiếu
-   **Mặc định**: `10`
-   **Giá trị hợp lệ**: 1-50

#### `news_limit`

-   **Bắt buộc**: Không
-   **Kiểu**: Number
-   **Mô tả**: Số lượng tin tức đã xuất bản (`status = 'published'`)
-   **Mặc định**: `10`
-   **Giá trị hợp lệ**: 1-50

## Response Success

```json
{
    "success": true,
    "code": "HOME_DATA_FETCHED_SUCCESS",
    "message": "Home data fetched successfully",
    "data": {
        "now_showing": [
            {
                "id": 1,
                "title": "Avatar: The Way of Water",
                "description": "Jake Sully sống cùng gia đình mới...",
                "duration": 192,
                "release_date": "2025-12-15",
                "status": "NOW_SHOWING",
                "computed_status": "NOW_SHOWING",
                "status_label": "Đang chiếu",
                "genre": "Khoa học viễn tưởng, Hành động",
                "age_classification": "T13",
                "language": "Tiếng Anh (Phụ đề Tiếng Việt)",
                "poster": {
                    "id": 1,
                    "file_name": "avatar_poster.webp",
                    "url": "http://localhost:8000/storage/media/posters/avatar_poster.webp",
                    "mime_type": "image/webp",
                    "size": 150000,
                    "type": "image"
                },
                "showtimes": [
                    {
                        "id": 1,
                        "date": "2025-12-26",
                        "start_time": "10:00:00",
                        "end_time": "13:12:00",
                        "price": 120000,
                        "status": "ongoing"
                    }
                ],
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "coming_soon": [
            {
                "id": 5,
                "title": "Kung Fu Panda 4",
                "description": "Po được chọn làm Lãnh đạo Tinh thần...",
                "duration": 94,
                "release_date": "2026-01-25",
                "status": "COMING_SOON",
                "computed_status": "COMING_SOON",
                "status_label": "Sắp ra mắt",
                "genre": "Hoạt hình, Hành động, Hài hước",
                "age_classification": "P",
                "language": "Lồng tiếng Việt",
                "poster": {
                    "id": 5,
                    "file_name": "kungfupanda4_poster.webp",
                    "url": "http://localhost:8000/storage/media/posters/kungfupanda4_poster.webp"
                },
                "showtimes": [],
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "upcoming": [
            {
                "id": 3,
                "title": "Dune: Part Two",
                "description": "Paul Atreides hợp nhất với người Fremen...",
                "duration": 166,
                "release_date": "2025-12-28",
                "status": "UPCOMING",
                "computed_status": "UPCOMING",
                "status_label": "Sắp chiếu",
                "genre": "Khoa học viễn tưởng, Phiêu lưu",
                "age_classification": "T13",
                "language": "Tiếng Anh (Phụ đề Tiếng Việt)",
                "poster": {
                    "id": 3,
                    "file_name": "dune2_poster.webp",
                    "url": "http://localhost:8000/storage/media/posters/dune2_poster.webp"
                },
                "showtimes": [
                    {
                        "id": 5,
                        "date": "2025-12-28",
                        "start_time": "10:00:00",
                        "end_time": "12:46:00",
                        "price": 130000,
                        "status": "scheduled"
                    }
                ],
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "rooms": [
            {
                "id": 1,
                "name": "Screen 1",
                "seat_count": 100,
                "cinema": {
                    "id": 1,
                    "name": "CGV Vincom Center",
                    "location": "TP. Hồ Chí Minh",
                    "address": "72 Lê Thánh Tôn, Quận 1, TP.HCM",
                    "phone": "1900 6017"
                },
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            },
            {
                "id": 2,
                "name": "Screen 2",
                "seat_count": 80,
                "cinema": {
                    "id": 1,
                    "name": "CGV Vincom Center",
                    "location": "TP. Hồ Chí Minh",
                    "address": "72 Lê Thánh Tôn, Quận 1, TP.HCM",
                    "phone": "1900 6017"
                },
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "news": [
            {
                "id": 1,
                "title": "Avatar 2 phá kỷ lục doanh thu phòng vé Việt Nam",
                "slug": "avatar-2-pha-ky-luc-doanh-thu-phong-ve-viet-nam",
                "summary": "Bộ phim bom tấn Avatar: The Way of Water đã chính thức phá vỡ mọi kỷ lục...",
                "content": "<h2>Kỷ lục mới được thiết lập</h2><p>...</p>",
                "status": "published",
                "thumbnail": {
                    "id": 20,
                    "file_name": "news-thumbnail.webp",
                    "url": "http://localhost:8000/storage/media/news/news-thumbnail.webp"
                },
                "author": {
                    "id": 1,
                    "name": "Admin"
                },
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ]
    }
}
```

## Response Fields

| Trường             | Bắt buộc | Kiểu dữ liệu | Mô tả                                              |
| ------------------ | -------- | ------------ | -------------------------------------------------- |
| `success`          | ✓        | Boolean      | Trạng thái thành công (`true`)                     |
| `code`             | ✓        | String       | Mã response (`HOME_DATA_FETCHED_SUCCESS`)          |
| `message`          | ✓        | String       | Thông báo theo ngôn ngữ client yêu cầu             |
| `data`             | ✓        | Object       | Dữ liệu trang chủ                                  |
| `data.now_showing` | ✓        | Array        | Danh sách phim đang chiếu                          |
| `data.coming_soon` | ✓        | Array        | Danh sách phim sắp ra mắt (chưa có suất chiếu)     |
| `data.upcoming`    | ✓        | Array        | Danh sách phim sắp chiếu (có suất chiếu tương lai) |
| `data.rooms`       | ✓        | Array        | Danh sách phòng chiếu                              |
| `data.news`        | ✓        | Array        | Danh sách tin tức đã xuất bản                      |

### Movie Status Logic

| Status        | Điều kiện                                               | Ý nghĩa    |
| ------------- | ------------------------------------------------------- | ---------- |
| `NOW_SHOWING` | Có suất chiếu `ongoing` hoặc `scheduled` hôm nay        | Đang chiếu |
| `UPCOMING`    | Có suất chiếu `scheduled` trong tương lai (sau hôm nay) | Sắp chiếu  |
| `COMING_SOON` | Không có suất chiếu nào                                 | Sắp ra mắt |

### Movie Object Fields

| Trường               | Bắt buộc | Kiểu dữ liệu | Mô tả                                                   |
| -------------------- | -------- | ------------ | ------------------------------------------------------- |
| `id`                 | ✓        | Number       | ID của phim                                             |
| `title`              | ✓        | String       | Tên phim                                                |
| `description`        | ✓        | String       | Mô tả phim                                              |
| `duration`           | ✓        | Number       | Thời lượng phim (phút)                                  |
| `release_date`       | ✓        | String       | Ngày phát hành (format: YYYY-MM-DD)                     |
| `status`             | ✓        | String       | Trạng thái DB: `COMING_SOON`, `UPCOMING`, `NOW_SHOWING` |
| `computed_status`    | ✓        | String       | Trạng thái tính toán realtime                           |
| `status_label`       | ✓        | String       | Nhãn trạng thái theo ngôn ngữ                           |
| `genre`              | ✗        | String       | Thể loại phim                                           |
| `age_classification` | ✓        | String       | Phân loại độ tuổi                                       |
| `language`           | ✗        | String       | Ngôn ngữ phim                                           |
| `poster`             | ✗        | Object       | Thông tin poster (MediaFile object)                     |
| `showtimes`          | ✗        | Array        | Danh sách suất chiếu                                    |
| `created_at`         | ✓        | String       | Thời gian tạo                                           |
| `updated_at`         | ✓        | String       | Thời gian cập nhật                                      |

### Room Object Fields

| Trường       | Bắt buộc | Kiểu dữ liệu | Mô tả                               |
| ------------ | -------- | ------------ | ----------------------------------- |
| `id`         | ✓        | Number       | ID của phòng                        |
| `name`       | ✓        | String       | Tên phòng                           |
| `seat_count` | ✓        | Number       | Số lượng ghế                        |
| `cinema`     | ✓        | Object       | Thông tin rạp chiếu (Cinema object) |
| `created_at` | ✓        | String       | Thời gian tạo                       |
| `updated_at` | ✓        | String       | Thời gian cập nhật                  |

### News Object Fields

| Trường       | Bắt buộc | Kiểu dữ liệu | Mô tả                                  |
| ------------ | -------- | ------------ | -------------------------------------- |
| `id`         | ✓        | Number       | ID của tin tức                         |
| `title`      | ✓        | String       | Tiêu đề (theo ngôn ngữ client)         |
| `slug`       | ✓        | String       | URL slug                               |
| `summary`    | ✓        | String       | Tóm tắt (theo ngôn ngữ client)         |
| `content`    | ✓        | String       | Nội dung đầy đủ (theo ngôn ngữ client) |
| `status`     | ✓        | String       | Trạng thái: `draft`, `published`       |
| `thumbnail`  | ✗        | Object       | Ảnh thumbnail (MediaFile object)       |
| `author`     | ✗        | Object       | Thông tin tác giả (User object)        |
| `created_at` | ✓        | String       | Thời gian tạo                          |
| `updated_at` | ✓        | String       | Thời gian cập nhật                     |

## Response Error

### Validation Error

```json
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "room_limit": "The room limit must be between 1 and 50."
    }
}
```

### Error Codes

| Code                    | HTTP Status | Mô tả                           |
| ----------------------- | ----------- | ------------------------------- |
| `VALIDATION_ERROR`      | 422         | Dữ liệu validation không hợp lệ |
| `INVALID_API_KEY`       | 401         | API key không hợp lệ            |
| `INTERNAL_SERVER_ERROR` | 500         | Lỗi server                      |

## Success Codes

| Code                        | HTTP Status | Mô tả                            |
| --------------------------- | ----------- | -------------------------------- |
| `HOME_DATA_FETCHED_SUCCESS` | 200         | Lấy dữ liệu trang chủ thành công |

## Postman

### Environment Variables

-   `base_url`: `http://localhost:8000`
-   `api_key`: `your-api-key-here`

### Example Request

```
GET {{base_url}}/api/home?now_showing_limit=5&upcoming_limit=5&coming_soon_limit=5&room_limit=10&news_limit=5
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

## Notes

-   API này là **public route**, không cần authentication
-   **now_showing**: Phim có suất chiếu `ongoing` hoặc `scheduled` hôm nay
-   **upcoming**: Phim có suất chiếu `scheduled` trong tương lai (sau hôm nay)
-   **coming_soon**: Phim chưa có suất chiếu nào
-   News chỉ lấy tin tức có `status = 'published'`
-   Dữ liệu được sắp xếp theo:
    -   Now showing: `release_date` DESC (phim mới phát hành trước)
    -   Upcoming: `release_date` ASC (sắp chiếu trước)
    -   Coming soon: `release_date` ASC (sắp ra mắt trước)
    -   Rooms: `name` ASC (theo tên)
    -   News: `created_at` DESC (mới nhất trước)
-   News title, summary, content sẽ được trả về theo ngôn ngữ client yêu cầu (vi hoặc en)
-   Tất cả dữ liệu trả về đã được format qua API Resources
