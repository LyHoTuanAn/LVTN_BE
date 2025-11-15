SET NAMES utf8mb4;                    -- Hỗ trợ UTF-8 full (emoji, tiếng Việt)
SET FOREIGN_KEY_CHECKS = 0;           -- Tạm tắt kiểm tra khóa ngoại khi tạo bảng

/* ======================================================
   ROLES – PERMISSIONS – USERS
   ====================================================== */

CREATE TABLE roles (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID vai trò
    name VARCHAR(50) NOT NULL,              -- Tên vai trò
    slug VARCHAR(50) NOT NULL UNIQUE,        -- Định danh duy nhất (admin, partner, customer)
    description TEXT,                        -- Mô tả vai trò
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    INDEX idx_roles_slug (slug),
    INDEX idx_roles_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE permissions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID quyền
    name VARCHAR(100) NOT NULL,             -- Tên quyền
    slug VARCHAR(100) NOT NULL UNIQUE,       -- Định danh quyền (manage_movies, manage_bookings…)
    description TEXT,                       -- Ghi chú
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    INDEX idx_permissions_slug (slug),
    INDEX idx_permissions_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE role_permissions (
    role_id BIGINT NOT NULL,                -- FK: role
    permission_id BIGINT NOT NULL,          -- FK: permission
    PRIMARY KEY (role_id, permission_id),   -- Một quyền chỉ được gán 1 lần vào role
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_role_permissions_role_id (role_id),
    INDEX idx_role_permissions_permission_id (permission_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo users trước (không có avatar_id ban đầu để tránh circular dependency)
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID user
    name VARCHAR(100) NOT NULL,             -- Tên user
    email VARCHAR(150) NOT NULL UNIQUE,      -- Email đăng nhập
    password VARCHAR(255) NOT NULL,         -- Mật khẩu (hash)
    role_id BIGINT NOT NULL,                -- FK: vai trò
    avatar_id BIGINT NULL,                  -- FK: avatar (media_files) - thêm sau
    phone VARCHAR(20),                      -- Số điện thoại
    address VARCHAR(255),                   -- Địa chỉ
    email_verified_at TIMESTAMP NULL,       -- Thời gian xác thực email
    remember_token VARCHAR(100) NULL,       -- Token nhớ đăng nhập
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_users_role_id (role_id),
    INDEX idx_users_email (email),
    INDEX idx_users_avatar_id (avatar_id),
    INDEX idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ======================================================
   MEDIA SYSTEM
   ====================================================== */

CREATE TABLE media_folders (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID thư mục
    name VARCHAR(150) NOT NULL,            -- Tên thư mục
    parent_id BIGINT NULL,                 -- Thư mục cha (nếu có)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    FOREIGN KEY (parent_id) REFERENCES media_folders(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_media_folders_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media_files (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID file
    folder_id BIGINT NULL,                 -- FK: thư mục
    user_id BIGINT NOT NULL,                -- FK: user upload
    file_name VARCHAR(255) NOT NULL,       -- Tên file
    file_path VARCHAR(255) NOT NULL,       -- Đường dẫn file
    mime_type VARCHAR(100) NOT NULL,       -- Kiểu file (jpg/png/mp4…)
    size BIGINT NOT NULL,                  -- Dung lượng (bytes)
    type VARCHAR(50) NOT NULL,             -- Loại (image | video)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    FOREIGN KEY (folder_id) REFERENCES media_folders(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_media_files_folder_id (folder_id),
    INDEX idx_media_files_user_id (user_id),
    INDEX idx_media_files_type (type),
    INDEX idx_media_files_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm foreign key cho avatar_id sau khi đã tạo media_files
ALTER TABLE users ADD FOREIGN KEY (avatar_id) REFERENCES media_files(id) ON DELETE SET NULL ON UPDATE CASCADE;

/* ======================================================
   MOVIES – CINEMAS – ROOMS – SEATS
   ====================================================== */

CREATE TABLE movies (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID phim
    title VARCHAR(255) NOT NULL,           -- Tên phim
    description TEXT,                       -- Nội dung tóm tắt
    duration INT NOT NULL,                  -- Thời lượng (phút)
    release_date DATE NOT NULL,            -- Ngày chiếu
    status ENUM('coming_soon', 'now_showing', 'ended') NOT NULL DEFAULT 'coming_soon',  -- Trạng thái: Sắp chiếu / Đang chiếu / Đã kết thúc
    poster_id BIGINT NULL,                  -- FK: ảnh poster
    trailer_id BIGINT NULL,                 -- FK: video trailer
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    deleted_at TIMESTAMP NULL,              -- Soft delete
    FOREIGN KEY (poster_id) REFERENCES media_files(id) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (trailer_id) REFERENCES media_files(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_movies_status (status),
    INDEX idx_movies_release_date (release_date),
    INDEX idx_movies_title (title),
    INDEX idx_movies_poster_id (poster_id),
    INDEX idx_movies_trailer_id (trailer_id),
    INDEX idx_movies_created_at (created_at),
    INDEX idx_movies_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cinemas (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID rạp chiếu
    user_id BIGINT NOT NULL,                -- FK: Partner quản lý rạp
    name VARCHAR(150) NOT NULL,            -- Tên rạp
    location VARCHAR(255) NOT NULL,         -- Thành phố / khu vực
    address VARCHAR(255),                   -- Địa chỉ chi tiết
    phone VARCHAR(20),                      -- Hotline
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    deleted_at TIMESTAMP NULL,              -- Soft delete
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_cinemas_user_id (user_id),
    INDEX idx_cinemas_location (location),
    INDEX idx_cinemas_name (name),
    INDEX idx_cinemas_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rooms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID phòng chiếu
    cinema_id BIGINT NOT NULL,              -- FK: rạp
    name VARCHAR(100) NOT NULL,             -- Tên phòng (Ví dụ: Room 1)
    seat_count INT NOT NULL DEFAULT 0,      -- Tổng số ghế
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    deleted_at TIMESTAMP NULL,              -- Soft delete
    UNIQUE KEY uk_rooms_cinema_name (cinema_id, name),  -- Rạp không được có 2 phòng trùng tên
    FOREIGN KEY (cinema_id) REFERENCES cinemas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_rooms_cinema_id (cinema_id),
    INDEX idx_rooms_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seats (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID ghế
    room_id BIGINT NOT NULL,                -- FK: phòng chiếu
    row VARCHAR(5) NOT NULL,                -- Hàng ghế (A, B, C…)
    number INT NOT NULL,                    -- Số ghế
    type ENUM('normal', 'vip', 'couple') NOT NULL DEFAULT 'normal',  -- Loại: Thường / VIP / Đôi
    status ENUM('active', 'maintenance', 'disabled') NOT NULL DEFAULT 'active',  -- Trạng thái: Hoạt động / Bảo trì / Vô hiệu
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    UNIQUE KEY uk_seats_room_row_number (room_id, row, number),  -- Ghế duy nhất trong phòng
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_seats_room_id (room_id),
    INDEX idx_seats_type (type),
    INDEX idx_seats_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ======================================================
   SHOWTIMES – BOOKINGS – BOOKING SEATS
   ====================================================== */

CREATE TABLE showtimes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID suất chiếu
    movie_id BIGINT NOT NULL,               -- FK: phim
    room_id BIGINT NOT NULL,                -- FK: phòng
    date DATE NOT NULL,                     -- Ngày chiếu
    start_time TIME NOT NULL,               -- Giờ bắt đầu
    end_time TIME NOT NULL,                 -- Giờ kết thúc
    price DECIMAL(10,2) NOT NULL,          -- Giá vé (VND)
    status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') NOT NULL DEFAULT 'scheduled',  -- Trạng thái: Đã lên lịch / Đang chiếu / Hoàn thành / Đã hủy
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    deleted_at TIMESTAMP NULL,              -- Soft delete
    UNIQUE KEY uk_showtimes_room_date_start (room_id, date, start_time),  -- Không trùng suất trong cùng phòng
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_showtimes_movie_id (movie_id),
    INDEX idx_showtimes_room_id (room_id),
    INDEX idx_showtimes_date (date),
    INDEX idx_showtimes_status (status),
    INDEX idx_showtimes_date_start_time (date, start_time),
    INDEX idx_showtimes_movie_date (movie_id, date),
    INDEX idx_showtimes_deleted_at (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID booking
    user_id BIGINT NOT NULL,                -- FK: user đặt vé
    showtime_id BIGINT NOT NULL,            -- FK: suất chiếu
    code CHAR(8) NOT NULL UNIQUE,           -- Mã đặt vé random (8 ký tự)
    is_paid BOOLEAN NOT NULL DEFAULT 0,      -- Đã thanh toán hay chưa
    voucher_id BIGINT NULL,                 -- FK: voucher sử dụng
    voucher_amount DECIMAL(10,2) NOT NULL DEFAULT 0,  -- Số tiền giảm giá (VND)
    price DECIMAL(10,2) NOT NULL,          -- Tổng tiền trước giảm (VND)
    total_price DECIMAL(10,2) NOT NULL,    -- Tổng tiền sau giảm (VND)
    status ENUM('pending', 'confirmed', 'canceled', 'completed') NOT NULL DEFAULT 'pending',  -- Trạng thái: Chờ xác nhận / Đã xác nhận / Đã hủy / Hoàn thành
    payment_method VARCHAR(50),            -- Phương thức thanh toán (Tiền mặt / Ví / Thẻ…)
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_bookings_user_id (user_id),
    INDEX idx_bookings_showtime_id (showtime_id),
    INDEX idx_bookings_code (code),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_is_paid (is_paid),
    INDEX idx_bookings_created_at (created_at),
    INDEX idx_bookings_user_status (user_id, status),
    INDEX idx_bookings_voucher_id (voucher_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_seats (
    booking_id BIGINT NOT NULL,            -- FK: booking
    seat_id BIGINT NOT NULL,                -- FK: ghế
    PRIMARY KEY (booking_id, seat_id),      -- Mỗi ghế chỉ được đặt 1 lần trong booking
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_booking_seats_booking_id (booking_id),
    INDEX idx_booking_seats_seat_id (seat_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ======================================================
   REVIEWS – FAVORITES
   ====================================================== */

CREATE TABLE reviews (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,   -- ID review
    user_id BIGINT NOT NULL,                -- FK: user đánh giá
    movie_id BIGINT NOT NULL,               -- FK: phim
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),  -- Số sao (1–5)
    comment TEXT,                           -- Nội dung đánh giá
    media_id BIGINT NULL,                   -- FK: ảnh/video kèm review
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian tạo
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,               -- Thời gian cập nhật
    UNIQUE KEY uk_reviews_user_movie (user_id, movie_id),  -- Mỗi user chỉ review 1 lần/phim
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (media_id) REFERENCES media_files(id) ON DELETE SET NULL ON UPDATE CASCADE,
    INDEX idx_reviews_user_id (user_id),
    INDEX idx_reviews_movie_id (movie_id),
    INDEX idx_reviews_rating (rating),
    INDEX idx_reviews_movie_rating (movie_id, rating),
    INDEX idx_reviews_created_at (created_at),
    INDEX idx_reviews_media_id (media_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE favorite_movies (
    user_id BIGINT NOT NULL,                -- FK: user
    movie_id BIGINT NOT NULL,               -- FK: phim
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,              -- Thời gian thêm vào yêu thích
    PRIMARY KEY (user_id, movie_id),        -- Không được yêu thích 2 lần
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_favorite_movies_user_id (user_id),
    INDEX idx_favorite_movies_movie_id (movie_id),
    INDEX idx_favorite_movies_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* ======================================================
   VOUCHERS (MERGED VERSION — NO RELATION TABLES)
   ====================================================== */

CREATE TABLE vouchers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,        -- ID voucher

    code VARCHAR(50) NOT NULL UNIQUE,            -- Mã voucher duy nhất (áp dụng khi user nhập)
    name VARCHAR(255) NOT NULL,                  -- Tên hiển thị của voucher

    type ENUM('percentage','fixed') NOT NULL,    -- Giảm theo % hoặc giảm số tiền
    amount DECIMAL(10,2) NOT NULL,               -- Giá trị giảm

    usage_limit INT NULL,                        -- Số lượt sử dụng tối đa (null = không giới hạn)
    used_count INT DEFAULT 0,                    -- Số lượt đã dùng

    applies_to ENUM(                             -- Voucher áp dụng cho đối tượng nào
        'all_users',                             -- Áp dụng cho tất cả user
        'specific_users',                        -- Chỉ áp dụng cho user trong only_for_user
        'specific_movies'                        -- Chỉ áp dụng cho movie trong only_for_movie
    ) NOT NULL DEFAULT 'all_users',

    only_for_user VARCHAR(255) NULL,             -- CSV list User IDs (VD: "1,2,5")
    only_for_movie VARCHAR(255) NULL,            -- CSV list Movie IDs (VD: "3,4,10")

    valid_from DATETIME NOT NULL,                -- Thời điểm bắt đầu hiệu lực
    valid_to DATETIME NOT NULL,                  -- Thời điểm hết hiệu lực

    status ENUM('active','expired','disabled')   -- Trạng thái voucher
        NOT NULL DEFAULT 'active',

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Recommended Indexes
    INDEX idx_vouchers_code (code),
    INDEX idx_vouchers_status (status),
    INDEX idx_vouchers_valid_range (valid_from, valid_to),
    INDEX idx_vouchers_type (type)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm foreign key cho voucher_id trong bookings
ALTER TABLE bookings ADD FOREIGN KEY (voucher_id) REFERENCES vouchers(id) ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;                 -- Bật lại kiểm tra khóa ngoại

