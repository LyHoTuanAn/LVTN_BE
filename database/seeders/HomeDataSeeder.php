<?php

namespace Database\Seeders;

use App\Models\Cinema;
use App\Models\Movie;
use App\Models\News;
use App\Models\Room;
use App\Models\Showtime;
use App\Models\MediaFile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class HomeDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Tạo fake data đầy đủ cho Home API với các phim ở các trạng thái khác nhau:
     * - NOW_SHOWING: Phim có suất chiếu hôm nay hoặc đang ongoing
     * - UPCOMING: Phim có suất chiếu trong tương lai
     * - COMING_SOON: Phim chưa có suất chiếu
     */
    public function run(): void
    {
        $this->command->info('🎬 Creating fake data for Home API...');

        // Lấy admin user hoặc tạo mới
        $admin = User::where('email', 'admin@gmail.com')->first();
        if (!$admin) {
            $admin = User::first();
        }
        
        // Tạo Cinema nếu chưa có
        $cinema = $this->createCinema($admin);
        
        // Tạo Rooms
        $rooms = $this->createRooms($cinema);
        
        // Tạo Media Files (poster images)
        $posters = $this->createPosterImages($admin);
        
        // Tạo Movies với các status khác nhau
        $this->createMovies($posters, $rooms);
        
        // Tạo News
        $this->createNews($admin, $posters);
        
        // Sync movie status
        $this->command->info('🔄 Syncing movie status...');
        app(\App\Services\Movie\MovieService::class)->syncAllMoviesStatus();
        
        $this->command->info('✅ Home data seeding completed!');
    }

    /**
     * Tạo Cinema
     */
    protected function createCinema(?User $admin): Cinema
    {
        return Cinema::firstOrCreate(
            ['name' => 'CGV Vincom Center'],
            [
                'user_id' => $admin?->id ?? 1,
                'location' => 'TP. Hồ Chí Minh',
                'address' => '72 Lê Thánh Tôn, Quận 1, TP.HCM',
                'phone' => '1900 6017',
            ]
        );
    }

    /**
     * Tạo Rooms
     */
    protected function createRooms(Cinema $cinema): array
    {
        $roomsData = [
            ['name' => 'Screen 1', 'seat_count' => 100],
            ['name' => 'Screen 2', 'seat_count' => 80],
            ['name' => 'Screen 3 - IMAX', 'seat_count' => 150],
            ['name' => 'Screen 4 - VIP', 'seat_count' => 40],
        ];

        $rooms = [];
        foreach ($roomsData as $data) {
            $rooms[] = Room::firstOrCreate(
                ['cinema_id' => $cinema->id, 'name' => $data['name']],
                ['seat_count' => $data['seat_count']]
            );
        }

        return $rooms;
    }

    /**
     * Tạo poster images (fake)
     */
    protected function createPosterImages(?User $user): array
    {
        $postersData = [
            [
                'file_name' => 'avatar_way_of_water_poster.webp',
                'file_path' => 'media/posters/2025/12/avatar_way_of_water_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 150000,
                'type' => 'image',
            ],
            [
                'file_name' => 'oppenheimer_poster.webp',
                'file_path' => 'media/posters/2025/12/oppenheimer_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 120000,
                'type' => 'image',
            ],
            [
                'file_name' => 'dune_part_two_poster.webp',
                'file_path' => 'media/posters/2025/12/dune_part_two_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 130000,
                'type' => 'image',
            ],
            [
                'file_name' => 'godzilla_kong_poster.webp',
                'file_path' => 'media/posters/2025/12/godzilla_kong_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 140000,
                'type' => 'image',
            ],
            [
                'file_name' => 'kung_fu_panda_4_poster.webp',
                'file_path' => 'media/posters/2025/12/kung_fu_panda_4_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 110000,
                'type' => 'image',
            ],
            [
                'file_name' => 'deadpool_wolverine_poster.webp',
                'file_path' => 'media/posters/2025/12/deadpool_wolverine_poster.webp',
                'mime_type' => 'image/webp',
                'size' => 160000,
                'type' => 'image',
            ],
        ];

        $posters = [];
        foreach ($postersData as $data) {
            $posters[] = MediaFile::firstOrCreate(
                ['file_path' => $data['file_path']],
                array_merge($data, ['user_id' => $user?->id])
            );
        }

        return $posters;
    }

    /**
     * Tạo Movies với các status khác nhau
     */
    protected function createMovies(array $posters, array $rooms): void
    {
        $today = Carbon::today();
        
        // === NOW_SHOWING: Phim có suất chiếu hôm nay hoặc ongoing ===
        $movie1 = Movie::firstOrCreate(
            ['title' => 'Avatar: The Way of Water'],
            [
                'description' => 'Jake Sully sống cùng gia đình mới của mình trên hành tinh Pandora. Khi một mối đe dọa quen thuộc quay trở lại để hoàn thành những gì đã bắt đầu, Jake phải làm việc với Neytiri và đội quân của người Na\'vi để bảo vệ hành tinh của họ.',
                'duration' => 192,
                'release_date' => $today->copy()->subDays(10),
                'status' => Movie::STATUS_COMING_SOON, // Will be updated by sync
                'genre' => 'Khoa học viễn tưởng, Hành động, Phiêu lưu',
                'age_classification' => 'T13',
                'language' => 'Tiếng Anh (Phụ đề Tiếng Việt)',
                'poster_id' => $posters[0]->id ?? null,
            ]
        );
        
        // Tạo suất chiếu hôm nay cho movie1 -> NOW_SHOWING
        $this->createShowtime($movie1, $rooms[0], $today, '10:00:00', '13:12:00', 120000, Showtime::STATUS_ONGOING);
        $this->createShowtime($movie1, $rooms[1], $today, '14:00:00', '17:12:00', 120000, Showtime::STATUS_SCHEDULED);
        $this->createShowtime($movie1, $rooms[2], $today, '19:00:00', '22:12:00', 150000, Showtime::STATUS_SCHEDULED);
        
        $movie2 = Movie::firstOrCreate(
            ['title' => 'Oppenheimer'],
            [
                'description' => 'Câu chuyện về nhà vật lý học J. Robert Oppenheimer và vai trò của ông trong việc phát triển bom nguyên tử. Bộ phim khám phá cuộc đời đầy phức tạp của ông từ những năm đầu nghiên cứu cho đến những hậu quả lịch sử mà phát minh của ông để lại.',
                'duration' => 180,
                'release_date' => $today->copy()->subDays(5),
                'status' => Movie::STATUS_COMING_SOON,
                'genre' => 'Tiểu sử, Lịch sử, Chính kịch',
                'age_classification' => 'T16',
                'language' => 'Tiếng Anh (Phụ đề Tiếng Việt)',
                'poster_id' => $posters[1]->id ?? null,
            ]
        );
        
        // Tạo suất chiếu hôm nay cho movie2 -> NOW_SHOWING  
        $this->createShowtime($movie2, $rooms[0], $today, '13:30:00', '16:30:00', 110000, Showtime::STATUS_SCHEDULED);
        $this->createShowtime($movie2, $rooms[3], $today, '20:00:00', '23:00:00', 180000, Showtime::STATUS_SCHEDULED);

        // === UPCOMING: Phim có suất chiếu trong tương lai ===
        $movie3 = Movie::firstOrCreate(
            ['title' => 'Dune: Part Two'],
            [
                'description' => 'Paul Atreides hợp nhất với người Fremen trong khi trên con đường chiến tranh trả thù chống lại những kẻ âm mưu tiêu diệt gia đình anh. Đối mặt với sự lựa chọn giữa tình yêu của đời mình và số phận của vũ trụ.',
                'duration' => 166,
                'release_date' => $today->copy()->addDays(3),
                'status' => Movie::STATUS_COMING_SOON,
                'genre' => 'Khoa học viễn tưởng, Phiêu lưu, Chính kịch',
                'age_classification' => 'T13',
                'language' => 'Tiếng Anh (Phụ đề Tiếng Việt)',
                'poster_id' => $posters[2]->id ?? null,
            ]
        );
        
        // Tạo suất chiếu tương lai cho movie3 -> UPCOMING
        $this->createShowtime($movie3, $rooms[0], $today->copy()->addDays(3), '10:00:00', '12:46:00', 130000);
        $this->createShowtime($movie3, $rooms[1], $today->copy()->addDays(3), '14:00:00', '16:46:00', 130000);
        $this->createShowtime($movie3, $rooms[2], $today->copy()->addDays(4), '19:00:00', '21:46:00', 160000);
        $this->createShowtime($movie3, $rooms[0], $today->copy()->addDays(5), '10:00:00', '12:46:00', 130000);

        $movie4 = Movie::firstOrCreate(
            ['title' => 'Godzilla x Kong: The New Empire'],
            [
                'description' => 'Hai sinh vật huyền thoại Godzilla và Kong phải hợp tác để đối đầu với một mối đe dọa chung có thể hủy diệt cả hai loài. Một cuộc phiêu lưu hoành tráng sâu trong lòng đất Hollow Earth.',
                'duration' => 115,
                'release_date' => $today->copy()->addDays(7),
                'status' => Movie::STATUS_COMING_SOON,
                'genre' => 'Hành động, Khoa học viễn tưởng',
                'age_classification' => 'T13',
                'language' => 'Tiếng Anh (Phụ đề Tiếng Việt)',
                'poster_id' => $posters[3]->id ?? null,
            ]
        );
        
        // Tạo suất chiếu tương lai cho movie4 -> UPCOMING
        $this->createShowtime($movie4, $rooms[1], $today->copy()->addDays(7), '09:00:00', '10:55:00', 100000);
        $this->createShowtime($movie4, $rooms[2], $today->copy()->addDays(7), '15:00:00', '16:55:00', 140000);
        $this->createShowtime($movie4, $rooms[0], $today->copy()->addDays(8), '19:00:00', '20:55:00', 100000);

        // === COMING_SOON: Phim chưa có suất chiếu ===
        Movie::firstOrCreate(
            ['title' => 'Kung Fu Panda 4'],
            [
                'description' => 'Po được chọn làm Lãnh đạo Tinh thần của Thung lũng Hòa bình, anh cần tìm và đào tạo một Chiến binh Rồng mới. Tuy nhiên, một phù thủy xấu xa xuất hiện với kế hoạch triệu hồi tất cả các phản diện mà Po đã đánh bại.',
                'duration' => 94,
                'release_date' => $today->copy()->addDays(30),
                'status' => Movie::STATUS_COMING_SOON,
                'genre' => 'Hoạt hình, Hành động, Hài hước',
                'age_classification' => 'P',
                'language' => 'Lồng tiếng Việt',
                'poster_id' => $posters[4]->id ?? null,
            ]
        );
        // Không tạo suất chiếu -> COMING_SOON

        Movie::firstOrCreate(
            ['title' => 'Deadpool & Wolverine'],
            [
                'description' => 'Deadpool kết hợp với Wolverine trong một cuộc phiêu lưu điên rồ xuyên đa vũ trụ. Bộ đôi kỳ lạ này phải hợp tác để đối đầu với một mối đe dọa chưa từng có.',
                'duration' => 127,
                'release_date' => $today->copy()->addDays(45),
                'status' => Movie::STATUS_COMING_SOON,
                'genre' => 'Hành động, Hài hước, Siêu anh hùng',
                'age_classification' => 'T18',
                'language' => 'Tiếng Anh (Phụ đề Tiếng Việt)',
                'poster_id' => $posters[5]->id ?? null,
            ]
        );
        // Không tạo suất chiếu -> COMING_SOON

        $this->command->info('   ✓ Created 6 movies with different statuses');
    }

    /**
     * Helper tạo Showtime
     */
    protected function createShowtime(
        Movie $movie, 
        Room $room, 
        Carbon $date, 
        string $startTime, 
        string $endTime, 
        float $price,
        string $status = Showtime::STATUS_SCHEDULED
    ): Showtime {
        return Showtime::firstOrCreate(
            [
                'movie_id' => $movie->id,
                'room_id' => $room->id,
                'date' => $date->format('Y-m-d'),
                'start_time' => $startTime,
            ],
            [
                'end_time' => $endTime,
                'price' => $price,
                'status' => $status,
            ]
        );
    }

    /**
     * Tạo News
     */
    protected function createNews(?User $author, array $thumbnails): void
    {
        $newsData = [
            [
                'title_vi' => 'Avatar 2 phá kỷ lục doanh thu phòng vé Việt Nam',
                'title_en' => 'Avatar 2 breaks box office record in Vietnam',
                'slug' => 'avatar-2-pha-ky-luc-doanh-thu-phong-ve-viet-nam',
                'summary_vi' => 'Bộ phim bom tấn Avatar: The Way of Water đã chính thức phá vỡ mọi kỷ lục doanh thu phòng vé tại Việt Nam chỉ sau 2 tuần công chiếu.',
                'summary_en' => 'The blockbuster Avatar: The Way of Water has officially broken all box office records in Vietnam after just 2 weeks of release.',
                'content_vi' => '<h2>Kỷ lục mới được thiết lập</h2>
<p>Avatar: The Way of Water đã chính thức trở thành bộ phim có doanh thu cao nhất lịch sử phòng vé Việt Nam.</p>',
                'content_en' => '<h2>New record set</h2>
<p>Avatar: The Way of Water has officially become the highest-grossing film in Vietnam box office history.</p>',
                'status' => 'published',
            ],
            [
                'title_vi' => 'Lịch chiếu phim Tết Nguyên Đán 2025',
                'title_en' => 'Lunar New Year 2025 Movie Schedule',
                'slug' => 'lich-chieu-phim-tet-nguyen-dan-2025',
                'summary_vi' => 'CGV công bố lịch chiếu phim đặc biệt trong dịp Tết Nguyên Đán 2025 với nhiều bộ phim bom tấn được mong đợi.',
                'summary_en' => 'CGV announces special movie schedule for Lunar New Year 2025 with many anticipated blockbusters.',
                'content_vi' => '<h2>Mùa phim Tết 2025 hứa hẹn bùng nổ</h2>
<p>Dịp Tết Nguyên Đán 2025, CGV sẽ trình chiếu nhiều bộ phim đặc sắc từ Hollywood và điện ảnh Việt Nam.</p>',
                'content_en' => '<h2>Tet 2025 movie season promises to explode</h2>
<p>During Lunar New Year 2025, CGV will screen many special films from Hollywood and Vietnamese cinema.</p>',
                'status' => 'published',
            ],
            [
                'title_vi' => 'Oppenheimer - Kiệt tác điện ảnh của Christopher Nolan',
                'title_en' => 'Oppenheimer - Christopher Nolan\'s Cinematic Masterpiece',
                'slug' => 'oppenheimer-kiet-tac-dien-anh-christopher-nolan',
                'summary_vi' => 'Oppenheimer là bộ phim tiểu sử gây tiếng vang lớn về nhà vật lý J. Robert Oppenheimer.',
                'summary_en' => 'Oppenheimer is a biographical film about physicist J. Robert Oppenheimer that has made a huge impact.',
                'content_vi' => '<h2>Một kiệt tác điện ảnh</h2>
<p>Christopher Nolan một lần nữa chứng minh tài năng của mình với Oppenheimer.</p>',
                'content_en' => '<h2>A cinematic masterpiece</h2>
<p>Christopher Nolan once again proves his talent with Oppenheimer.</p>',
                'status' => 'published',
            ],
        ];

        foreach ($newsData as $index => $data) {
            News::firstOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'author_id' => $author?->id,
                    'thumbnail_id' => $thumbnails[$index]->id ?? null,
                ])
            );
        }

        $this->command->info('   ✓ Created 3 news articles');
    }
}
