# API Tìm Kiếm Phim (Movies Search)

## Thông tin chung

| Thuộc tính         | Giá trị                                 |
| ------------------ | --------------------------------------- |
| **Endpoint**       | `GET /api/movies/search`                |
| **Mô tả**          | Tìm kiếm phim với nhiều bộ lọc nâng cao |
| **Authentication** | Không yêu cầu                           |
| **Rate Limit**     | Không                                   |

## Headers bắt buộc

| Header      | Mô tả                 | Ví dụ          |
| ----------- | --------------------- | -------------- |
| `X-Api-Key` | API key do server cấp | `your-api-key` |
| `Language`  | Ngôn ngữ (en hoặc vi) | `vi`           |

---

## Query Parameters

| Parameter            | Kiểu    | Bắt buộc | Mô tả                                                                                     |
| -------------------- | ------- | -------- | ----------------------------------------------------------------------------------------- |
| `keyword`            | string  | Không    | Từ khóa tìm kiếm (tìm trong title và description)                                         |
| `genre`              | string  | Không    | Thể loại phim (Action, Comedy, Drama, Horror, Romance, Sci-Fi, Animation, ...)            |
| `status`             | string  | Không    | Trạng thái phim: `COMING_SOON`, `UPCOMING`, `NOW_SHOWING`                                 |
| `age_classification` | string  | Không    | Phân loại độ tuổi: `P`, `K`, `T13`, `T16`, `T18`, `C`                                     |
| `duration_min`       | integer | Không    | Thời lượng tối thiểu (phút)                                                               |
| `duration_max`       | integer | Không    | Thời lượng tối đa (phút)                                                                  |
| `release_year`       | integer | Không    | Năm phát hành                                                                             |
| `sort_by`            | string  | Không    | Sắp xếp theo: `title`, `release_date`, `duration`, `created_at`. Mặc định: `release_date` |
| `sort_order`         | string  | Không    | Thứ tự sắp xếp: `asc`, `desc`. Mặc định: `desc`                                           |
| `per_page`           | integer | Không    | Số phim mỗi trang. Mặc định: 15                                                           |
| `page`               | integer | Không    | Trang hiện tại. Mặc định: 1                                                               |

### Giải thích các giá trị

#### Status (Trạng thái)

| Giá trị       | Mô tả                                                         |
| ------------- | ------------------------------------------------------------- |
| `COMING_SOON` | Sắp ra mắt - Chưa có suất chiếu                               |
| `UPCOMING`    | Sắp chiếu - Có suất chiếu trong tương lai (sau hôm nay)       |
| `NOW_SHOWING` | Đang chiếu - Có suất đang diễn ra hoặc còn suất trong hôm nay |

#### Age Classification (Phân loại độ tuổi)

| Giá trị | Mô tả                               |
| ------- | ----------------------------------- |
| `P`     | Mọi lứa tuổi (All Ages)             |
| `K`     | Trẻ em (cần có phụ huynh hướng dẫn) |
| `T13`   | Từ 13 tuổi trở lên                  |
| `T16`   | Từ 16 tuổi trở lên                  |
| `T18`   | Từ 18 tuổi trở lên                  |
| `C`     | Phim cấm                            |

---

## Ví dụ Request

### 1. Tìm kiếm theo từ khóa

```http
GET /api/movies/search?keyword=avengers
X-Api-Key: your-api-key
Language: vi
```

### 2. Tìm phim đang chiếu + thể loại Action

```http
GET /api/movies/search?status=NOW_SHOWING&genre=Action
X-Api-Key: your-api-key
Language: vi
```

### 3. Tìm phim phù hợp cho trẻ em, thời lượng dưới 120 phút

```http
GET /api/movies/search?age_classification=P&duration_max=120&sort_by=title&sort_order=asc
X-Api-Key: your-api-key
Language: vi
```

### 4. Tìm phim ra mắt năm 2024

```http
GET /api/movies/search?release_year=2024&per_page=20
X-Api-Key: your-api-key
Language: vi
```

### 5. Kết hợp nhiều bộ lọc

```http
GET /api/movies/search?keyword=marvel&genre=Action&status=NOW_SHOWING&age_classification=T13&duration_min=90&duration_max=180&release_year=2024&sort_by=release_date&sort_order=desc&per_page=10
X-Api-Key: your-api-key
Language: vi
```

---

## Response

### Response thành công (200 OK)

```json
{
    "success": true,
    "code": "MOVIES_SEARCH_SUCCESS",
    "message": "Tìm kiếm phim thành công",
    "data": [
        {
            "id": 1,
            "title": "Avengers: Endgame",
            "description": "Sau các sự kiện tàn khốc của Avengers: Infinity War...",
            "duration": 181,
            "release_date": "2019-04-22",
            "status": "NOW_SHOWING",
            "computed_status": "NOW_SHOWING",
            "status_label": "Đang chiếu",
            "genre": "Action",
            "age_classification": "T13",
            "language": "English",
            "poster": {
                "id": 123,
                "url": "https://example.com/storage/movies/avengers-endgame-poster.jpg",
                "thumbnail_url": "https://example.com/storage/movies/thumbnails/avengers-endgame-poster.jpg"
            },
            "trailer": {
                "id": 124,
                "url": "https://example.com/storage/movies/avengers-endgame-trailer.mp4"
            },
            "is_favorited": false,
            "created_at": "2024-01-15 10:30:00",
            "updated_at": "2024-01-20 15:45:00"
        },
        {
            "id": 2,
            "title": "Avengers: Infinity War",
            "description": "Thanos đã thu thập đủ các viên đá vô cực...",
            "duration": 149,
            "release_date": "2018-04-25",
            "status": "UPCOMING",
            "computed_status": "UPCOMING",
            "status_label": "Sắp chiếu",
            "genre": "Action",
            "age_classification": "T13",
            "language": "English",
            "poster": {
                "id": 125,
                "url": "https://example.com/storage/movies/infinity-war-poster.jpg",
                "thumbnail_url": "https://example.com/storage/movies/thumbnails/infinity-war-poster.jpg"
            },
            "trailer": null,
            "is_favorited": true,
            "created_at": "2024-01-10 08:00:00",
            "updated_at": "2024-01-18 12:30:00"
        }
    ],
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 3,
        "per_page": 15,
        "to": 15,
        "total": 42
    },
    "links": {
        "first": "http://localhost/api/movies/search?page=1",
        "last": "http://localhost/api/movies/search?page=3",
        "prev": null,
        "next": "http://localhost/api/movies/search?page=2"
    }
}
```

### Response không tìm thấy kết quả (200 OK)

```json
{
    "success": true,
    "code": "MOVIES_SEARCH_SUCCESS",
    "message": "Tìm kiếm phim thành công",
    "data": [],
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "per_page": 15,
        "to": null,
        "total": 0
    },
    "links": {
        "first": "http://localhost/api/movies/search?page=1",
        "last": "http://localhost/api/movies/search?page=1",
        "prev": null,
        "next": null
    }
}
```

### Response lỗi API Key không hợp lệ (401 Unauthorized)

```json
{
    "success": false,
    "code": "INVALID_API_KEY",
    "message": "API key không hợp lệ"
}
```

---

## Success Codes

| Code                    | Mô tả                    |
| ----------------------- | ------------------------ |
| `MOVIES_SEARCH_SUCCESS` | Tìm kiếm phim thành công |

## Error Codes

| Code              | HTTP Status | Mô tả                |
| ----------------- | ----------- | -------------------- |
| `INVALID_API_KEY` | 401         | API key không hợp lệ |
| `SERVER_ERROR`    | 500         | Lỗi server           |

---

## Ghi chú

1. **Tìm kiếm keyword**: Tìm kiếm không phân biệt hoa thường, hỗ trợ tìm kiếm một phần của từ
2. **Status filter**: Lọc theo trạng thái được tính toán dựa trên suất chiếu (showtimes), không phải giá trị cố định trong database
3. **Phân trang**: API trả về thông tin phân trang trong `meta` và `links`
4. **Sắp xếp mặc định**: Theo ngày phát hành giảm dần (phim mới nhất trước)
5. **is_favorited**: Chỉ trả về `true` nếu user đã đăng nhập và đã thêm phim vào yêu thích
