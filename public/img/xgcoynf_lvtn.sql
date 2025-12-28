CREATE TABLE `actor_movie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar_id` bigint(20) UNSIGNED DEFAULT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `showtime_id` bigint(20) UNSIGNED NOT NULL,
  `code` char(8) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `voucher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `voucher_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','canceled','completed') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `booking_seats` (
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `seat_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('lvtn-cache-illuminate:queue:restart', 'i:1766396134;', 2081756134);
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `cinemas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `director_movie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `avatar_id` bigint(20) UNSIGNED DEFAULT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `favorite_movies` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `media_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `folder_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `size` bigint(20) NOT NULL,
  `type` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `media_files` (`id`, `folder_id`, `user_id`, `file_name`, `file_path`, `mime_type`, `size`, `type`, `created_at`, `updated_at`) VALUES
(1, NULL, 6, 'totoro_1766173470_6945ab1e61e28.webp', 'media/avatars/2025/12/totoro_1766173470_6945ab1e61e28.webp', 'image/webp', 223412, 'image', '2025-12-19 12:44:30', '2025-12-19 12:44:30'),
(2, NULL, 6, 'totoro_1766173476_6945ab2441080.webp', 'media/avatars/2025/12/totoro_1766173476_6945ab2441080.webp', 'image/webp', 223412, 'image', '2025-12-19 12:44:36', '2025-12-19 12:44:36'),
(3, NULL, 6, 'scaled_IMG_20251210_151001_1766173487_6945ab2f3d4ab.webp', 'media/avatars/2025/12/scaled_IMG_20251210_151001_1766173487_6945ab2f3d4ab.webp', 'image/webp', 32272, 'image', '2025-12-19 12:44:47', '2025-12-19 12:44:47'),
(4, NULL, 6, 'scaled_IMG_20251210_150946_1766173511_6945ab4712c9d.webp', 'media/avatars/2025/12/scaled_IMG_20251210_150946_1766173511_6945ab4712c9d.webp', 'image/webp', 132534, 'image', '2025-12-19 12:45:11', '2025-12-19 12:45:11'),
(5, NULL, 6, 'scaled_IMG_20251210_150946_1766173820_6945ac7c707f9.webp', 'media/avatars/2025/12/scaled_IMG_20251210_150946_1766173820_6945ac7c707f9.webp', 'image/webp', 132534, 'image', '2025-12-19 12:50:20', '2025-12-19 12:50:20'),
(6, NULL, 6, 'scaled_IMG_20251210_150946_1766174012_6945ad3cbed7a.webp', 'media/avatars/2025/12/scaled_IMG_20251210_150946_1766174012_6945ad3cbed7a.webp', 'image/webp', 132534, 'image', '2025-12-19 12:53:32', '2025-12-19 12:53:32'),
(7, NULL, 6, 'totoro_1766174472_6945af0822d45.webp', 'media/avatars/2025/12/totoro_1766174472_6945af0822d45.webp', 'image/webp', 223412, 'image', '2025-12-19 13:01:12', '2025-12-19 13:01:12'),
(8, NULL, 6, 'totoro_1766174830_6945b06e16677.webp', 'media/avatars/2025/12/totoro_1766174830_6945b06e16677.webp', 'image/webp', 223412, 'image', '2025-12-19 13:07:10', '2025-12-19 13:07:10'),
(9, NULL, 6, 'scaled_FB_IMG_1766084608430_1766174883_6945b0a34d3b7.webp', 'media/avatars/2025/12/scaled_FB_IMG_1766084608430_1766174883_6945b0a34d3b7.webp', 'image/webp', 6282, 'image', '2025-12-19 13:08:03', '2025-12-19 13:08:03');
CREATE TABLE `media_folders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_11_15_170237_create_roles_table', 1),
(5, '2025_11_15_170238_create_permissions_table', 1),
(6, '2025_11_15_170239_create_role_permissions_table', 1),
(7, '2025_11_15_170251_add_role_fields_to_users_table', 1),
(8, '2025_11_15_170256_create_media_folders_table', 1),
(9, '2025_11_15_170257_create_media_files_table', 1),
(10, '2025_11_15_170258_create_movies_table', 1),
(11, '2025_11_15_170259_create_cinemas_table', 1),
(12, '2025_11_15_170300_create_rooms_table', 1),
(13, '2025_11_15_170302_create_seats_table', 1),
(14, '2025_11_15_170303_create_showtimes_table', 1),
(15, '2025_11_15_170305_create_bookings_table', 1),
(16, '2025_11_15_170306_create_booking_seats_table', 1),
(17, '2025_11_15_170308_create_reviews_table', 1),
(18, '2025_11_15_170310_create_favorite_movies_table', 1),
(19, '2025_11_15_170311_create_vouchers_table', 1),
(20, '2025_11_16_085040_create_otps_table', 1),
(21, '2025_11_17_174351_update_otps_remove_verified_and_create_password_reset_tokens', 1),
(22, '2025_11_18_021817_create_refresh_tokens_table', 2),
(24, '2025_11_18_022158_update_refresh_tokens_table_add_jti', 3),
(25, '2025_12_19_172315_merge_actors_directors_tables', 4),
(26, '2025_12_19_220000_add_movie_extended_fields', 4),
(27, '2025_12_19_223000_create_directors_actors_tables', 4),
(28, '2025_12_19_184433_add_date_of_birth_and_gender_to_users_table', 5);
CREATE TABLE `movies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` int(11) NOT NULL,
  `release_date` date NOT NULL,
  `status` enum('coming_soon','now_showing','ended') NOT NULL DEFAULT 'coming_soon',
  `genre` varchar(255) DEFAULT NULL,
  `age_classification` enum('P','K','T13','T16','T18','C') NOT NULL DEFAULT 'P',
  `language` varchar(100) DEFAULT NULL,
  `poster_id` bigint(20) UNSIGNED DEFAULT NULL,
  `trailer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `otp_code` varchar(6) NOT NULL,
  `type` enum('register','forgot_password') NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `otps` (`id`, `user_id`, `email`, `otp_code`, `type`, `expires_at`, `created_at`, `updated_at`) VALUES
(3, 2, 'lyhotuanan2004@gmail.com', '904785', 'register', '2025-11-29 23:07:36', '2025-11-29 23:02:36', '2025-11-29 23:02:36'),
(6, 3, 'lyhotuanan2019@gmail.com', '150925', 'register', '2025-11-29 23:21:46', '2025-11-29 23:16:46', '2025-11-29 23:16:46'),
(11, 4, 'nguyenvana@example.com', '569963', 'register', '2025-12-01 22:50:18', '2025-12-01 22:45:18', '2025-12-01 22:45:18'),
(15, 4, 'nguyenvana@example.com', '037944', 'forgot_password', '2025-12-02 03:12:38', '2025-12-02 03:07:38', '2025-12-02 03:07:38'),
(21, 6, 'nguyentuanbt298@gmail.com', '772410', 'forgot_password', '2025-12-04 01:20:15', '2025-12-04 01:15:15', '2025-12-04 01:15:15');
CREATE TABLE `password_reset_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `permissions` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Manage Movies', 'manage_movies', 'Quản lý phim', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(2, 'View Movies', 'view_movies', 'Xem danh sách phim', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(3, 'Manage Cinemas', 'manage_cinemas', 'Quản lý rạp chiếu phim', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(4, 'View Cinemas', 'view_cinemas', 'Xem danh sách rạp', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(5, 'Manage Showtimes', 'manage_showtimes', 'Quản lý suất chiếu', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(6, 'View Showtimes', 'view_showtimes', 'Xem suất chiếu', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(7, 'Manage Bookings', 'manage_bookings', 'Quản lý đặt vé', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(8, 'View Bookings', 'view_bookings', 'Xem đặt vé', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(9, 'Manage Roles', 'manage_roles', 'Quản lý vai trò', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(10, 'Manage Permissions', 'manage_permissions', 'Quản lý quyền hạn', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(11, 'Manage Users', 'manage_users', 'Quản lý người dùng', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(12, 'View Users', 'view_users', 'Xem người dùng', '2025-11-17 19:04:49', '2025-11-17 19:04:49');
CREATE TABLE `refresh_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `jti` varchar(100) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `refresh_tokens` (`id`, `user_id`, `jti`, `expires_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `media_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'Quản trị viên hệ thống', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(2, 'Partner', 'partner', 'Đối tác quản lý rạp phim', '2025-11-17 19:04:49', '2025-11-17 19:04:49'),
(3, 'Customer', 'customer', 'Khách hàng', '2025-11-17 19:04:49', '2025-11-17 19:04:49');
CREATE TABLE `role_permissions` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(3, 2),
(3, 4),
(3, 6),
(3, 8);
CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cinema_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `seat_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `seats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `row` varchar(5) NOT NULL,
  `number` int(11) NOT NULL,
  `type` enum('normal','vip','couple') NOT NULL DEFAULT 'normal',
  `status` enum('active','maintenance','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0lQHJd32MpXJvgxMa75M1yVCQ9npxlBOA1Ta5Jrn', NULL, '3.253.55.17', 'Pandalytics/2.0 (https://domainsbot.com/pandalytics/)', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3gzR01NU0ZueXNWdUxOTVpBT2NnMEJZT3d4RXRNZGpzeGtnU0dKWiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly92bnNwb3J0aWZ5LnNwYWNlIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1766388644),
('ba9hTL8lILMn64NMip1TcXKqoQziek84fdvhNJXp', NULL, '45.148.10.174', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQ0ZJaENrQ1VNV0RFOTdmVE9GMEJDdVFpZDBQc0E1M3lUR2Y5dTdCTSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjg6Imh0dHBzOi8vd3d3LnZuc3BvcnRpZnkuc3BhY2UiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1766394486),
('BrAbd9GOwXDjviJZ2XSGQTWhJj2PLu5vdiKthv0K', NULL, '49.51.52.250', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTjEwVXlIYVphak84NW5mMEM0a0VHM3VnRVRCSjRBZ0hSMGJOSUFlTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly93d3cudm5zcG9ydGlmeS5zcGFjZSI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1766396176),
('sghdofkvK27TqJdL9ov4IER7BMV9MuL3XI6hwBOA', NULL, '36.41.75.167', 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_2_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.0.3 Mobile/15E148 Safari/604.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUHVWa0RBZ0VwQngzYVcxM0U5eEFZZjdyUTVwMjVtMHdhOWdGY2c4UCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjM6Imh0dHA6Ly92bnNwb3J0aWZ5LnNwYWNlIjtzOjU6InJvdXRlIjtzOjk6ImRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1766388147);
CREATE TABLE `showtimes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('scheduled','ongoing','completed','cancelled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `avatar_id` bigint(20) UNSIGNED DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role_id`, `avatar_id`, `phone`, `address`, `date_of_birth`, `gender`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@gmail.com', '2025-11-17 19:06:30', '$2y$12$sh24IWWNfMrb4OiXhRT5a.v/lshqGl.dlo7ujY9XzC2nQ/fn0NpnG', 1, NULL, '0123456789', '123 Đường ABC, Quận 1, TP.HCM', NULL, NULL, NULL, '2025-11-17 19:06:15', '2025-11-17 19:07:46'),
(2, 'Nguyễn Văn A', 'lyhotuanan2004@gmail.com', NULL, '$2y$12$qZXBjv4NVraKCX0XUz9Bm.XiOk7hZKmB8b.MBuCYyOi54ybd7FEKu', 3, NULL, '0121456789', '123 Đường ABC, Quận 1, TP.HCM', NULL, NULL, NULL, '2025-11-29 23:02:36', '2025-11-29 23:02:36'),
(3, 'Nguyễn Văn A', 'lyhotuanan2019@gmail.com', NULL, '$2y$12$Mc8l6.88hdQANjbWR2ysX.1VJSoycSm4mUQA5APW.dT3myanJIUf2', 3, NULL, '0121456789', '123 Đường ABC, Quận 1, TP.HCM', NULL, NULL, NULL, '2025-11-29 23:03:36', '2025-11-29 23:03:36'),
(4, 'Nguyễn Văn A', 'nguyenvana@example.com', NULL, '$2y$12$syXEd82SZ0Lb9oZuf0Hz.O7Itly/hREnM0L2NJ0vc//S/vaw1Tc0m', 3, NULL, '0123456789', '123 Đường ABC, Quận 1, TP.HCM', NULL, NULL, NULL, '2025-12-01 06:53:52', '2025-12-01 06:53:52'),
(5, 'Nguyen Van B', 'vanthi11bt@gmail.com', '2025-12-01 09:22:11', '$2y$12$s2ekTXfLAUPFYrBLM6GjXuir4Y61.f.PKnd4t7wsXNuLzc1cRWaGC', 3, NULL, '0984831410', '495 Quoc lo 13', NULL, NULL, NULL, '2025-12-01 09:19:48', '2025-12-01 09:22:11'),
(6, 'Nguyễn Quốc Tuấn', 'nguyentuanbt298@gmail.com', '2025-12-01 09:28:21', '$2y$12$Rbw9SilKketuxIibh7OtUOcDLPv.5GeIx/KWZon0hgJnSTcRsnlqi', 3, 9, '0984831424', '495 Quốc Lộ 13', '2004-08-29', 'male', NULL, '2025-12-01 09:27:53', '2025-12-19 13:08:03'),
(7, 'LenBi', 'lenbi11bt@gmail.com', '2025-12-04 02:46:37', '$2y$12$ybOXvhbagA2FAWbJqk.Zb.8etlCfhb189iuKdWlqx/hHle7w2T.cG', 3, NULL, '0984831411', 'Hà lội', NULL, NULL, NULL, '2025-12-04 02:46:10', '2025-12-04 02:46:37'),
(8, 'Anh Long', 'longnguyen2k44@gmail.com', '2025-12-19 10:15:29', '$2y$12$EoIfY1Q/EFEPzP2bJqHAGOH51B2H.MAkRDXnEsDKW9YPpFyF/MlgC', 3, NULL, '0984831441', '495 quốc lộ 13', NULL, NULL, NULL, '2025-12-19 10:14:02', '2025-12-19 10:15:29');
CREATE TABLE `vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('percentage','fixed') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `applies_to` enum('all_users','specific_users','specific_movies') NOT NULL DEFAULT 'all_users',
  `only_for_user` varchar(255) DEFAULT NULL,
  `only_for_movie` varchar(255) DEFAULT NULL,
  `valid_from` datetime NOT NULL,
  `valid_to` datetime NOT NULL,
  `status` enum('active','expired','disabled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;