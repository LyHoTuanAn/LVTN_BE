# Bulk Create Cinemas API

## Endpoint

```
POST /api/admin/cinemas/bulk
```

## Description

Tạo nhiều rạp chiếu cùng lúc. API này cho phép admin tạo nhiều rạp chiếu trong một request duy nhất. Tất cả rạp chiếu sẽ được tạo trong một transaction - nếu có lỗi ở bất kỳ rạp chiếu nào, tất cả sẽ được rollback.

## Authentication

-   **Required**: Yes
-   **Role**: admin

## Headers

| Header        | Required | Description                         |
| ------------- | -------- | ----------------------------------- |
| Authorization | Yes      | Bearer {token}                      |
| X-Api-Key     | Yes      | API key                             |
| Language      | No       | Ngôn ngữ (en hoặc vi), mặc định: en |
| Content-Type  | Yes      | application/json                    |

## Request Body

```json
{
    "cinemas": [
        {
            "name": "CGV Vincom Thủ Đức",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Vincom Plaza Thủ Đức, 216 Võ Văn Ngân, Bình Thọ, Thủ Đức, TP.HCM",
            "phone": "1900 6017",
            "user_id": 1
        },
        {
            "name": "CGV Aeon Mall Tân Phú",
            "location": "Hồ Chí Minh",
            "address": "Tầng 3, TTTM Aeon Mall Tân Phú, 30 Bờ Bao Tân Thắng, Sơn Kỳ, Tân Phú, TP.HCM",
            "phone": "1900 6017",
            "user_id": 1
        },
        {
            "name": "Lotte Cinema Nowzone",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Nowzone, 235 Nguyễn Văn Cừ, Quận 1, TP.HCM",
            "phone": "1900 2083"
        }
    ]
}
```

## Request Fields

### cinemas (array, required)

Danh sách các rạp chiếu cần tạo. Mỗi đối tượng cinema gồm:

| Field    | Type    | Required | Description                          |
| -------- | ------- | -------- | ------------------------------------ |
| name     | string  | Yes      | Tên rạp chiếu (không được trùng)     |
| location | string  | Yes      | Vị trí/Thành phố                     |
| address  | string  | Yes      | Địa chỉ chi tiết                     |
| phone    | string  | No       | Số điện thoại                        |
| user_id  | integer | No       | ID của người quản lý (admin/partner) |

## Response

### Success Response (201 Created)

```json
{
    "success": true,
    "code": "CINEMAS_CREATED_SUCCESS",
    "message": "3 cinemas created successfully",
    "data": [
        {
            "id": 1,
            "name": "CGV Vincom Thủ Đức",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Vincom Plaza Thủ Đức, 216 Võ Văn Ngân, Bình Thọ, Thủ Đức, TP.HCM",
            "phone": "1900 6017",
            "user": {
                "id": 1,
                "name": "Admin",
                "email": "admin@example.com"
            },
            "rooms": [],
            "created_at": "2024-12-30 11:30:00",
            "updated_at": "2024-12-30 11:30:00"
        },
        {
            "id": 2,
            "name": "CGV Aeon Mall Tân Phú",
            "location": "Hồ Chí Minh",
            "address": "Tầng 3, TTTM Aeon Mall Tân Phú, 30 Bờ Bao Tân Thắng, Sơn Kỳ, Tân Phú, TP.HCM",
            "phone": "1900 6017",
            "user": {
                "id": 1,
                "name": "Admin",
                "email": "admin@example.com"
            },
            "rooms": [],
            "created_at": "2024-12-30 11:30:00",
            "updated_at": "2024-12-30 11:30:00"
        },
        {
            "id": 3,
            "name": "Lotte Cinema Nowzone",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Nowzone, 235 Nguyễn Văn Cừ, Quận 1, TP.HCM",
            "phone": "1900 2083",
            "user": null,
            "rooms": [],
            "created_at": "2024-12-30 11:30:00",
            "updated_at": "2024-12-30 11:30:00"
        }
    ]
}
```

### Validation Error Response (422)

```json
{
    "success": false,
    "code": "VALIDATION_ERROR",
    "message": "Validation error",
    "errors": {
        "cinemas.0.name": ["The cinemas.0.name field is required."],
        "cinemas.1.location": ["The cinemas.1.location field is required."]
    }
}
```

### Cinema Validation Failed Response (422)

Khi có rạp chiếu trùng tên trong batch hoặc tên đã tồn tại trong database:

```json
{
    "success": false,
    "code": "CINEMA_VALIDATION_FAILED",
    "message": "Cinema validation failed",
    "errors": {
        "0": {
            "name": "Cinema with this name already exists"
        },
        "2": {
            "name": "Duplicate cinema name in batch"
        }
    }
}
```

### Server Error Response (500)

```json
{
    "success": false,
    "code": "CINEMA_CREATE_FAILED",
    "message": "Failed to create cinema",
    "errors": {
        "error": "Database connection error"
    }
}
```

## Notes

1. **Transaction**: Tất cả rạp chiếu được tạo trong một transaction. Nếu có lỗi, tất cả sẽ được rollback.
2. **Duplicate Check**: API sẽ kiểm tra:
    - Tên rạp chiếu không được trùng trong cùng một batch
    - Tên rạp chiếu không được trùng với rạp chiếu đã tồn tại trong database
3. **Order**: Kết quả trả về theo thứ tự tạo (cùng thứ tự với input)

## Example cURL

```bash
curl -X POST 'http://localhost:8000/api/admin/cinemas/bulk' \
  -H 'Authorization: Bearer your_jwt_token' \
  -H 'X-Api-Key: your_api_key' \
  -H 'Content-Type: application/json' \
  -H 'Language: vi' \
  -d '{
    "cinemas": [
        {
            "name": "CGV Vincom Thủ Đức",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Vincom Plaza Thủ Đức, 216 Võ Văn Ngân, Bình Thọ, Thủ Đức, TP.HCM",
            "phone": "1900 6017"
        },
        {
            "name": "Lotte Cinema Nowzone",
            "location": "Hồ Chí Minh",
            "address": "Tầng 5, TTTM Nowzone, 235 Nguyễn Văn Cừ, Quận 1, TP.HCM",
            "phone": "1900 2083"
        }
    ]
}'
```
