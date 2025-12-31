# API Yêu thích Phim (Favorite Movies)

API quản lý danh sách phim yêu thích của người dùng.

## Base URL

```
/api/favorites
```

## Headers bắt buộc

| Header        | Giá trị        | Mô tả                            |
| ------------- | -------------- | -------------------------------- |
| X-Api-Key     | your-api-key   | API key do server cấp            |
| Language      | vi hoặc en     | Ngôn ngữ response (mặc định: en) |
| Authorization | Bearer {token} | JWT token (bắt buộc)             |

---

## Endpoints

### 1. Lấy danh sách phim yêu thích

**GET** `/api/favorites`

Lấy danh sách tất cả phim yêu thích của user đang đăng nhập.

#### Query Parameters

| Tham số    | Kiểu    | Bắt buộc | Mô tả                                                               |
| ---------- | ------- | -------- | ------------------------------------------------------------------- |
| status     | string  | Không    | Filter theo trạng thái phim (COMING_SOON, UPCOMING, NOW_SHOWING)    |
| search     | string  | Không    | Tìm kiếm theo tên phim                                              |
| sort_by    | string  | Không    | Cột sắp xếp (created_at, title, release_date). Mặc định: created_at |
| sort_order | string  | Không    | Chiều sắp xếp (asc, desc). Mặc định: desc                           |
| per_page   | integer | Không    | Số phim mỗi trang. Mặc định: 15                                     |
| page       | integer | Không    | Trang hiện tại                                                      |

#### Response thành công

```json
{
    "success": true,
    "code": "FAVORITES_FETCHED_SUCCESS",
    "message": "Favorite movies fetched successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "title": "Avengers: Endgame",
                "description": "After the devastating events of Avengers: Infinity War...",
                "duration": 181,
                "release_date": "2019-04-26",
                "status": "NOW_SHOWING",
                "computed_status": "NOW_SHOWING",
                "status_label": "Đang chiếu",
                "genre": "Action, Adventure, Drama",
                "age_classification": "T13",
                "language": "English",
                "poster": {
                    "id": 1,
                    "url": "https://example.com/poster.jpg"
                },
                "trailer": null,
                "is_favorited": true,
                "favorited_at": "2024-12-31 09:00:00"
            }
        ],
        "links": {...},
        "meta": {
            "current_page": 1,
            "last_page": 5,
            "per_page": 15,
            "total": 72
        }
    }
}
```

---

### 2. Thêm phim vào danh sách yêu thích

**POST** `/api/favorites/{movieId}`

Thêm một phim vào danh sách yêu thích của user.

#### Path Parameters

| Tham số | Kiểu    | Mô tả                |
| ------- | ------- | -------------------- |
| movieId | integer | ID của phim cần thêm |

#### Response thành công (201)

```json
{
    "success": true,
    "code": "FAVORITE_ADDED_SUCCESS",
    "message": "Movie added to favorites successfully",
    "data": {
        "movie_id": 1,
        "is_favorited": true
    }
}
```

#### Response lỗi - Phim không tồn tại (404)

```json
{
    "success": false,
    "code": "MOVIE_NOT_FOUND",
    "message": "Movie not found",
    "errors": {}
}
```

#### Response lỗi - Phim đã được yêu thích (400)

```json
{
    "success": false,
    "code": "MOVIE_ALREADY_FAVORITED",
    "message": "Movie is already in your favorites",
    "errors": {}
}
```

---

### 3. Xóa phim khỏi danh sách yêu thích

**DELETE** `/api/favorites/{movieId}`

Xóa một phim khỏi danh sách yêu thích của user.

#### Path Parameters

| Tham số | Kiểu    | Mô tả               |
| ------- | ------- | ------------------- |
| movieId | integer | ID của phim cần xóa |

#### Response thành công (200)

```json
{
    "success": true,
    "code": "FAVORITE_REMOVED_SUCCESS",
    "message": "Movie removed from favorites successfully",
    "data": {
        "movie_id": 1,
        "is_favorited": false
    }
}
```

#### Response lỗi - Phim không trong danh sách yêu thích (404)

```json
{
    "success": false,
    "code": "FAVORITE_NOT_FOUND",
    "message": "Movie is not in your favorites",
    "errors": {}
}
```

---

## Mã lỗi

| Mã                      | HTTP Status | Mô tả                                   |
| ----------------------- | ----------- | --------------------------------------- |
| MOVIE_NOT_FOUND         | 404         | Phim không tồn tại                      |
| MOVIE_ALREADY_FAVORITED | 400         | Phim đã có trong danh sách yêu thích    |
| FAVORITE_NOT_FOUND      | 404         | Phim không có trong danh sách yêu thích |
| UNAUTHORIZED            | 401         | Chưa đăng nhập hoặc token hết hạn       |
| INVALID_API_KEY         | 401         | API key không hợp lệ                    |
