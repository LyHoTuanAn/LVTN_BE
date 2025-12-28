# 🎬 Movies API

API endpoints để quản lý và lấy thông tin phim.

---

## 1. Danh sách phim

### URL

`GET /api/movies`

### Method

`GET`

### Headers

**Bắt buộc** ✓

-   `X-Api-Key`: API key do server cấp
-   `Language`: `en` hoặc `vi` (mặc định: `en`)
-   `Accept`: `application/json`

### Query Parameters

| Trường              | Bắt buộc | Kiểu dữ liệu | Mô tả                          | Mặc định |
| ------------------- | -------- | ------------ | ------------------------------ | -------- |
| `search`            | ✗        | String       | Tìm kiếm theo tên phim         | -        |
| `status`            | ✗        | String       | Lọc theo trạng thái            | -        |
| `release_date_from` | ✗        | String       | Lọc từ ngày phát hành (Y-m-d)  | -        |
| `release_date_to`   | ✗        | String       | Lọc đến ngày phát hành (Y-m-d) | -        |
| `per_page`          | ✗        | Number       | Số lượng phim mỗi trang        | `15`     |

### Response Success

```json
{
    "success": true,
    "code": "MOVIES_FETCHED_SUCCESS",
    "message": "Movies fetched successfully",
    "data": [
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
            "created_at": "2025-12-25 16:21:27",
            "updated_at": "2025-12-25 16:21:27"
        }
    ]
}
```

---

## 2. Chi tiết phim

### URL

`GET /api/movies/{id}`

### Method

`GET`

### Headers

**Bắt buộc** ✓

-   `X-Api-Key`: API key do server cấp
-   `Language`: `en` hoặc `vi` (mặc định: `en`)
-   `Accept`: `application/json`

### Path Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả       |
| ------ | -------- | ------------ | ----------- |
| `id`   | ✓        | Number       | ID của phim |

### Response Success

```json
{
    "success": true,
    "code": "MOVIE_FETCHED_SUCCESS",
    "message": "Movie fetched successfully",
    "data": {
        "id": 1,
        "title": "Avatar: The Way of Water",
        "description": "Jake Sully sống cùng gia đình mới của mình trên hành tinh Pandora. Khi một mối đe dọa quen thuộc quay trở lại để hoàn thành những gì đã bắt đầu, Jake phải làm việc với Neytiri và đội quân của người Na'vi để bảo vệ hành tinh của họ.",
        "duration": 192,
        "release_date": "2025-12-15",
        "status": "NOW_SHOWING",
        "computed_status": "NOW_SHOWING",
        "status_label": "Đang chiếu",
        "genre": "Khoa học viễn tưởng, Hành động, Phiêu lưu",
        "age_classification": "T13",
        "language": "Tiếng Anh (Phụ đề Tiếng Việt)",
        "poster": {
            "id": 1,
            "file_name": "avatar_poster.webp",
            "file_path": "media/posters/2025/12/avatar_poster.webp",
            "url": "http://localhost:8000/storage/media/posters/2025/12/avatar_poster.webp",
            "mime_type": "image/webp",
            "size": 150000,
            "type": "image",
            "created_at": "2025-12-25 16:21:27",
            "updated_at": "2025-12-25 16:21:27"
        },
        "trailer": {
            "id": 2,
            "file_name": "avatar_trailer.mp4",
            "file_path": "media/trailers/2025/12/avatar_trailer.mp4",
            "url": "http://localhost:8000/storage/media/trailers/2025/12/avatar_trailer.mp4",
            "mime_type": "video/mp4",
            "size": 50000000,
            "type": "video",
            "created_at": "2025-12-25 16:21:27",
            "updated_at": "2025-12-25 16:21:27"
        },
        "directors": [
            {
                "id": 1,
                "name": "James Cameron",
                "avatar": {
                    "id": 10,
                    "file_name": "james_cameron.webp",
                    "url": "http://localhost:8000/storage/media/avatars/james_cameron.webp"
                },
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "actors": [
            {
                "id": 1,
                "name": "Sam Worthington",
                "avatar": {
                    "id": 11,
                    "file_name": "sam_worthington.webp",
                    "url": "http://localhost:8000/storage/media/avatars/sam_worthington.webp"
                },
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            },
            {
                "id": 2,
                "name": "Zoe Saldaña",
                "avatar": null,
                "created_at": "2025-12-25 16:21:27",
                "updated_at": "2025-12-25 16:21:27"
            }
        ],
        "showtimes": [
            {
                "id": 1,
                "date": "2025-12-26",
                "start_time": "10:00:00",
                "end_time": "13:12:00",
                "price": 120000,
                "status": "scheduled",
                "room": {
                    "id": 1,
                    "name": "Screen 1",
                    "seat_count": 100,
                    "cinema": {
                        "id": 1,
                        "name": "CGV Vincom Center",
                        "location": "TP. Hồ Chí Minh"
                    }
                },
                "created_at": "2025-12-25 17:00:00",
                "updated_at": "2025-12-25 17:00:00"
            }
        ],
        "reviews": [
            {
                "id": 1,
                "user": {
                    "id": 5,
                    "name": "Nguyễn Văn A"
                },
                "rating": 5,
                "comment": "Phim rất hay!",
                "created_at": "2025-12-25 18:00:00",
                "updated_at": "2025-12-25 18:00:00"
            }
        ],
        "created_at": "2025-12-25 16:21:27",
        "updated_at": "2025-12-25 16:21:27"
    }
}
```

### Response Error

```json
{
    "success": false,
    "code": "NOT_FOUND",
    "message": "Resource not found"
}
```

---

## 3. Lịch suất chiếu của phim

### URL

`GET /api/movies/{id}/showtimes`

### Method

`GET`

### Headers

**Bắt buộc** ✓

-   `X-Api-Key`: API key do server cấp
-   `Language`: `en` hoặc `vi` (mặc định: `en`)
-   `Accept`: `application/json`

### Path Parameters

| Trường | Bắt buộc | Kiểu dữ liệu | Mô tả       |
| ------ | -------- | ------------ | ----------- |
| `id`   | ✓        | Number       | ID của phim |

### Query Parameters

| Trường          | Bắt buộc | Kiểu dữ liệu | Mô tả                        | Mặc định |
| --------------- | -------- | ------------ | ---------------------------- | -------- |
| `date`          | ✗        | String       | Lọc theo ngày cụ thể (Y-m-d) | -        |
| `date_from`     | ✗        | String       | Lọc từ ngày                  | -        |
| `date_to`       | ✗        | String       | Lọc đến ngày                 | -        |
| `group_by_date` | ✗        | Boolean      | Nhóm showtimes theo ngày     | `false`  |

### Response Success (Không group)

```json
{
    "success": true,
    "code": "MOVIE_SHOWTIMES_FETCHED_SUCCESS",
    "message": "Lấy lịch chiếu phim thành công",
    "data": {
        "movie": {
            "id": 1,
            "title": "Avatar: The Way of Water"
        },
        "showtimes": [
            {
                "id": 1,
                "date": "2025-12-26",
                "start_time": "10:00:00",
                "end_time": "13:12:00",
                "price": 120000,
                "status": "scheduled",
                "room": {
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
                "created_at": "2025-12-25 17:00:00",
                "updated_at": "2025-12-25 17:00:00"
            },
            {
                "id": 2,
                "date": "2025-12-26",
                "start_time": "14:00:00",
                "end_time": "17:12:00",
                "price": 120000,
                "status": "scheduled",
                "room": {
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
                },
                "created_at": "2025-12-25 17:00:00",
                "updated_at": "2025-12-25 17:00:00"
            },
            {
                "id": 3,
                "date": "2025-12-27",
                "start_time": "19:00:00",
                "end_time": "22:12:00",
                "price": 150000,
                "status": "scheduled",
                "room": {
                    "id": 3,
                    "name": "Screen 3 - IMAX",
                    "seat_count": 150,
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
                "created_at": "2025-12-25 17:00:00",
                "updated_at": "2025-12-25 17:00:00"
            }
        ]
    }
}
```

### Response Success (Group by date)

**Request:** `GET /api/movies/1/showtimes?group_by_date=true`

```json
{
    "success": true,
    "code": "MOVIE_SHOWTIMES_FETCHED_SUCCESS",
    "message": "Lấy lịch chiếu phim thành công",
    "data": {
        "movie": {
            "id": 1,
            "title": "Avatar: The Way of Water"
        },
        "schedule": [
            {
                "date": "2025-12-26",
                "showtimes": [
                    {
                        "id": 1,
                        "date": "2025-12-26",
                        "start_time": "10:00:00",
                        "end_time": "13:12:00",
                        "price": 120000,
                        "status": "scheduled",
                        "room": {
                            "id": 1,
                            "name": "Screen 1",
                            "seat_count": 100,
                            "cinema": {
                                "id": 1,
                                "name": "CGV Vincom Center"
                            }
                        }
                    },
                    {
                        "id": 2,
                        "date": "2025-12-26",
                        "start_time": "14:00:00",
                        "end_time": "17:12:00",
                        "price": 120000,
                        "status": "scheduled",
                        "room": {
                            "id": 2,
                            "name": "Screen 2",
                            "seat_count": 80,
                            "cinema": {
                                "id": 1,
                                "name": "CGV Vincom Center"
                            }
                        }
                    }
                ]
            },
            {
                "date": "2025-12-27",
                "showtimes": [
                    {
                        "id": 3,
                        "date": "2025-12-27",
                        "start_time": "19:00:00",
                        "end_time": "22:12:00",
                        "price": 150000,
                        "status": "scheduled",
                        "room": {
                            "id": 3,
                            "name": "Screen 3 - IMAX",
                            "seat_count": 150,
                            "cinema": {
                                "id": 1,
                                "name": "CGV Vincom Center"
                            }
                        }
                    }
                ]
            }
        ]
    }
}
```

### Response Error

```json
{
    "success": false,
    "code": "NOT_FOUND",
    "message": "Resource not found"
}
```

---

## Response Fields

### Movie Object Fields

| Trường               | Bắt buộc | Kiểu dữ liệu | Mô tả                                                          |
| -------------------- | -------- | ------------ | -------------------------------------------------------------- |
| `id`                 | ✓        | Number       | ID của phim                                                    |
| `title`              | ✓        | String       | Tên phim                                                       |
| `description`        | ✓        | String       | Mô tả phim                                                     |
| `duration`           | ✓        | Number       | Thời lượng phim (phút)                                         |
| `release_date`       | ✓        | String       | Ngày phát hành (format: YYYY-MM-DD)                            |
| `status`             | ✓        | String       | Trạng thái trong DB                                            |
| `computed_status`    | ✓        | String       | Trạng thái tính toán: `COMING_SOON`, `UPCOMING`, `NOW_SHOWING` |
| `status_label`       | ✓        | String       | Nhãn trạng thái theo ngôn ngữ                                  |
| `genre`              | ✗        | String       | Thể loại phim                                                  |
| `age_classification` | ✓        | String       | Phân loại độ tuổi: `P`, `K`, `T13`, `T16`, `T18`, `C`          |
| `language`           | ✗        | String       | Ngôn ngữ phim                                                  |
| `poster`             | ✗        | Object       | Ảnh poster (MediaFile object)                                  |
| `trailer`            | ✗        | Object       | Video trailer (MediaFile object)                               |
| `directors`          | ✗        | Array        | Danh sách đạo diễn                                             |
| `actors`             | ✗        | Array        | Danh sách diễn viên                                            |
| `showtimes`          | ✗        | Array        | Danh sách suất chiếu                                           |
| `reviews`            | ✗        | Array        | Danh sách đánh giá                                             |
| `created_at`         | ✓        | String       | Thời gian tạo                                                  |
| `updated_at`         | ✓        | String       | Thời gian cập nhật                                             |

### Computed Status Logic

| Status        | Điều kiện                                        | Ý nghĩa    |
| ------------- | ------------------------------------------------ | ---------- |
| `NOW_SHOWING` | Có suất chiếu `ongoing` hoặc `scheduled` hôm nay | Đang chiếu |
| `UPCOMING`    | Có suất chiếu `scheduled` trong tương lai        | Sắp chiếu  |
| `COMING_SOON` | Không có suất chiếu nào                          | Sắp ra mắt |

### Age Classification

| Code  | Mô tả                            |
| ----- | -------------------------------- |
| `P`   | Phim dành cho mọi lứa tuổi       |
| `K`   | Trẻ em (cần có người lớn đi kèm) |
| `T13` | Phim cấm khán giả dưới 13 tuổi   |
| `T16` | Phim cấm khán giả dưới 16 tuổi   |
| `T18` | Phim cấm khán giả dưới 18 tuổi   |
| `C`   | Phim không được phép phổ biến    |

### Showtime Object Fields

| Trường       | Bắt buộc | Kiểu dữ liệu | Mô tả                                                        |
| ------------ | -------- | ------------ | ------------------------------------------------------------ |
| `id`         | ✓        | Number       | ID của suất chiếu                                            |
| `date`       | ✓        | String       | Ngày chiếu (format: YYYY-MM-DD)                              |
| `start_time` | ✓        | String       | Giờ bắt đầu (format: HH:mm:ss)                               |
| `end_time`   | ✓        | String       | Giờ kết thúc (format: HH:mm:ss)                              |
| `price`      | ✓        | Number       | Giá vé (VND)                                                 |
| `status`     | ✓        | String       | Trạng thái: `scheduled`, `ongoing`, `completed`, `cancelled` |
| `room`       | ✓        | Object       | Thông tin phòng chiếu (Room object)                          |
| `created_at` | ✓        | String       | Thời gian tạo                                                |
| `updated_at` | ✓        | String       | Thời gian cập nhật                                           |

---

## Error Codes

| Code                    | HTTP Status | Mô tả                           |
| ----------------------- | ----------- | ------------------------------- |
| `NOT_FOUND`             | 404         | Phim không tồn tại              |
| `VALIDATION_ERROR`      | 422         | Dữ liệu validation không hợp lệ |
| `INVALID_API_KEY`       | 401         | API key không hợp lệ            |
| `INTERNAL_SERVER_ERROR` | 500         | Lỗi server                      |

## Success Codes

| Code                              | HTTP Status | Mô tả                          |
| --------------------------------- | ----------- | ------------------------------ |
| `MOVIES_FETCHED_SUCCESS`          | 200         | Lấy danh sách phim thành công  |
| `MOVIE_FETCHED_SUCCESS`           | 200         | Lấy thông tin phim thành công  |
| `MOVIE_SHOWTIMES_FETCHED_SUCCESS` | 200         | Lấy lịch chiếu phim thành công |

---

## Postman Examples

### Get All Movies

```
GET {{base_url}}/api/movies?status=NOW_SHOWING&per_page=10
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

### Get Movie Details

```
GET {{base_url}}/api/movies/1
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

### Get Movie Showtimes

```
GET {{base_url}}/api/movies/1/showtimes?group_by_date=true
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

### Get Movie Showtimes for Specific Date

```
GET {{base_url}}/api/movies/1/showtimes?date=2025-12-26
Headers:
  X-Api-Key: {{api_key}}
  Language: vi
  Accept: application/json
```

---

## Notes

-   API này là **public route**, không cần authentication
-   Showtimes không bao gồm các suất chiếu đã hủy (`status = 'cancelled'`)
-   Showtimes được sắp xếp theo ngày và giờ bắt đầu
-   `computed_status` được tính toán tự động dựa trên các suất chiếu của phim
-   Tất cả dữ liệu trả về đã được format qua API Resources
