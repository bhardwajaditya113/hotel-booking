<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RoomType;
use App\Models\Room;
use App\Models\RoomNumber;
use App\Models\MultiImage;
use App\Models\Facility;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Gallery;
use Carbon\Carbon;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Mock Data...');

        // Seed Room Types
        $this->seedRoomTypes();
        
        // Seed Rooms
        $this->seedRooms();
        
        // Seed Room Numbers
        $this->seedRoomNumbers();
        
        // Seed Facilities
        $this->seedFacilities();
        
        // Seed Team Members
        $this->seedTeam();
        
        // Seed Testimonials
        $this->seedTestimonials();
        
        // Seed Blog Categories and Posts
        $this->seedBlog();
        
        // Seed Gallery
        $this->seedGallery();

        $this->command->info('Mock Data seeded successfully!');
    }

    private function seedRoomTypes(): void
    {
        $types = [
            ['name' => 'Standard Room'],
            ['name' => 'Deluxe Room'],
            ['name' => 'Suite'],
            ['name' => 'Executive Suite'],
            ['name' => 'Presidential Suite'],
        ];

        foreach ($types as $type) {
            RoomType::firstOrCreate(['name' => $type['name']], $type);
        }
        $this->command->info('Room Types seeded!');
    }

    private function seedRooms(): void
    {
        $rooms = [
            [
                'roomtype_id' => 1,
                'total_adult' => 2,
                'total_child' => 1,
                'room_capacity' => 3,
                'price' => 150,
                'size' => 350,
                'view' => 'City View',
                'bed_style' => 'King Bed',
                'discount' => 10,
                'short_desc' => 'A comfortable standard room perfect for couples or solo travelers.',
                'description' => 'Our Standard Room offers all the essential amenities for a comfortable stay. Features include a plush king-size bed, flat-screen TV, complimentary WiFi, and an ensuite bathroom with premium toiletries. Enjoy stunning city views from your private window.',
                'status' => 1,
                'image' => 'upload/roomimg/room1.jpg',
            ],
            [
                'roomtype_id' => 2,
                'total_adult' => 2,
                'total_child' => 2,
                'room_capacity' => 4,
                'price' => 250,
                'size' => 450,
                'view' => 'Ocean View',
                'bed_style' => 'Queen Bed',
                'discount' => 15,
                'short_desc' => 'Spacious deluxe room with ocean views and premium amenities.',
                'description' => 'Experience luxury in our Deluxe Room featuring breathtaking ocean views. This spacious room includes a queen-size bed, sitting area, mini-bar, 55-inch smart TV, and a marble bathroom with rain shower. Perfect for families or those seeking extra comfort.',
                'status' => 1,
                'image' => 'upload/roomimg/room2.jpg',
            ],
            [
                'roomtype_id' => 3,
                'total_adult' => 3,
                'total_child' => 2,
                'room_capacity' => 5,
                'price' => 400,
                'size' => 650,
                'view' => 'Garden View',
                'bed_style' => 'Double King Bed',
                'discount' => 20,
                'short_desc' => 'Elegant suite with separate living area and premium services.',
                'description' => 'Our Suite offers the ultimate in comfort and style. Featuring a separate living room, bedroom with king-size bed, fully stocked mini-bar, Nespresso machine, and a luxurious bathroom with jacuzzi tub. Includes access to our exclusive lounge.',
                'status' => 1,
                'image' => 'upload/roomimg/room3.jpg',
            ],
            [
                'roomtype_id' => 4,
                'total_adult' => 2,
                'total_child' => 2,
                'room_capacity' => 4,
                'price' => 600,
                'size' => 850,
                'view' => 'Panoramic View',
                'bed_style' => 'Super King Bed',
                'discount' => 0,
                'short_desc' => 'Executive suite with business amenities and butler service.',
                'description' => 'The Executive Suite is designed for discerning travelers. Features include a private office space, conference calling facilities, super king bed, walk-in closet, and dedicated butler service. Enjoy panoramic views and complimentary airport transfers.',
                'status' => 1,
                'image' => 'upload/roomimg/room4.jpg',
            ],
            [
                'roomtype_id' => 5,
                'total_adult' => 4,
                'total_child' => 2,
                'room_capacity' => 6,
                'price' => 1200,
                'size' => 1500,
                'view' => '360° Skyline View',
                'bed_style' => 'Master King Bed',
                'discount' => 5,
                'short_desc' => 'The ultimate luxury experience with private terrace and pool.',
                'description' => 'Our Presidential Suite represents the pinnacle of luxury. This expansive suite features multiple bedrooms, a private terrace with infinity pool, personal chef service, private cinema room, and 24/7 concierge. Experience unparalleled opulence with 360-degree skyline views.',
                'status' => 1,
                'image' => 'upload/roomimg/room5.jpg',
            ],
        ];

        foreach ($rooms as $room) {
            Room::firstOrCreate(
                ['roomtype_id' => $room['roomtype_id'], 'bed_style' => $room['bed_style']], 
                array_merge($room, ['created_at' => Carbon::now()])
            );
        }
        $this->command->info('Rooms seeded!');
    }

    private function seedRoomNumbers(): void
    {
        $rooms = Room::with('type')->get();
        foreach ($rooms as $room) {
            $baseNumber = 100 + ($room->id * 10);
            for ($i = 1; $i <= 5; $i++) {
                RoomNumber::firstOrCreate([
                    'rooms_id' => $room->id,
                    'room_no' => $baseNumber + $i,
                ], [
                    'room_type_id' => $room->roomtype_id,
                    'status' => 'Active',
                ]);
            }
        }
        $this->command->info('Room Numbers seeded!');
    }

    private function seedFacilities(): void
    {
        $facilities = [
            'Free WiFi',
            'Air Conditioning',
            'Flat Screen TV',
            'Mini Bar',
            'Room Service',
            'Safe Box',
            'Coffee Maker',
            'Hair Dryer',
            'Iron & Board',
            'Bathrobe & Slippers',
        ];

        $rooms = Room::all();
        foreach ($rooms as $room) {
            $numFacilities = min($room->id + 4, count($facilities));
            for ($i = 0; $i < $numFacilities; $i++) {
                Facility::firstOrCreate([
                    'rooms_id' => $room->id,
                    'facility_name' => $facilities[$i],
                ]);
            }
        }
        $this->command->info('Facilities seeded!');
    }

    private function seedTeam(): void
    {
        $team = [
            [
                'name' => 'John Smith',
                'postion' => 'General Manager',
                'facebook' => 'https://facebook.com',
                'image' => 'upload/team/team1.jpg',
            ],
            [
                'name' => 'Sarah Johnson',
                'postion' => 'Front Desk Manager',
                'facebook' => 'https://facebook.com',
                'image' => 'upload/team/team2.jpg',
            ],
            [
                'name' => 'Michael Chen',
                'postion' => 'Executive Chef',
                'facebook' => 'https://facebook.com',
                'image' => 'upload/team/team3.jpg',
            ],
            [
                'name' => 'Emily Davis',
                'postion' => 'Spa Director',
                'facebook' => 'https://facebook.com',
                'image' => 'upload/team/team4.jpg',
            ],
        ];

        foreach ($team as $member) {
            Team::firstOrCreate(['name' => $member['name']], $member);
        }
        $this->command->info('Team seeded!');
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'name' => 'Robert Wilson',
                'city' => 'New York',
                'message' => 'Absolutely amazing experience! The staff went above and beyond to make our anniversary special. The room was immaculate and the views were breathtaking. Will definitely return!',
                'image' => 'upload/testimonial/testi1.jpg',
            ],
            [
                'name' => 'Jennifer Lee',
                'city' => 'Los Angeles',
                'message' => 'Best hotel I have ever stayed at. The attention to detail is incredible. From the welcome drink to the turndown service, everything was perfect. Highly recommend!',
                'image' => 'upload/testimonial/testi2.jpg',
            ],
            [
                'name' => 'David Martinez',
                'city' => 'Chicago',
                'message' => 'Outstanding service and beautiful facilities. The spa was heavenly and the restaurant served the best food. My family had an unforgettable vacation here.',
                'image' => 'upload/testimonial/testi3.jpg',
            ],
            [
                'name' => 'Amanda Brown',
                'city' => 'Miami',
                'message' => 'This hotel exceeded all my expectations. The Presidential Suite was worth every penny. The private pool and butler service made me feel like royalty!',
                'image' => 'upload/testimonial/testi4.jpg',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::firstOrCreate(['name' => $testimonial['name']], $testimonial);
        }
        $this->command->info('Testimonials seeded!');
    }

    private function seedBlog(): void
    {
        // Blog Categories
        $categories = [
            ['category_name' => 'Travel Tips', 'category_slug' => 'travel-tips'],
            ['category_name' => 'Hotel News', 'category_slug' => 'hotel-news'],
            ['category_name' => 'Local Attractions', 'category_slug' => 'local-attractions'],
            ['category_name' => 'Food & Dining', 'category_slug' => 'food-dining'],
        ];

        foreach ($categories as $category) {
            BlogCategory::firstOrCreate(['category_slug' => $category['category_slug']], $category);
        }

        // Blog Posts
        $posts = [
            [
                'blogcat_id' => 1,
                'user_id' => 1,
                'post_titile' => '10 Essential Packing Tips for Your Hotel Stay',
                'post_slug' => '10-essential-packing-tips-for-your-hotel-stay',
                'short_descp' => 'Discover the must-have items and smart packing strategies that will make your hotel stay more comfortable and convenient.',
                'long_descp' => '<p>Packing for a hotel stay can be tricky. Here are our top 10 tips to ensure you have everything you need...</p><p>1. Always pack a portable charger<br>2. Bring your own toiletry bag<br>3. Pack versatile clothing<br>4. Don\'t forget entertainment<br>5. Include a first aid kit</p>',
                'post_image' => 'upload/blog/blog1.jpg',
            ],
            [
                'blogcat_id' => 2,
                'user_id' => 1,
                'post_titile' => 'Introducing Our New Spa & Wellness Center',
                'post_slug' => 'introducing-our-new-spa-wellness-center',
                'short_descp' => 'We are excited to announce the opening of our state-of-the-art spa facility featuring world-class treatments.',
                'long_descp' => '<p>We are thrilled to unveil our brand new Spa & Wellness Center, designed to provide guests with the ultimate relaxation experience...</p><p>Our spa features:<br>- 10 private treatment rooms<br>- Heated indoor pool<br>- Steam room and sauna<br>- Yoga and meditation studio</p>',
                'post_image' => 'upload/blog/blog2.jpg',
            ],
            [
                'blogcat_id' => 3,
                'user_id' => 1,
                'post_titile' => 'Top 5 Must-Visit Attractions Near Our Hotel',
                'post_slug' => 'top-5-must-visit-attractions-near-our-hotel',
                'short_descp' => 'Explore the best local attractions just minutes away from our hotel. From historic landmarks to beautiful parks.',
                'long_descp' => '<p>Our hotel is perfectly located near some of the city\'s most beloved attractions...</p><p>1. Central Park - 5 minute walk<br>2. Museum of Modern Art - 10 minute walk<br>3. Times Square - 15 minute walk<br>4. Empire State Building - 20 minute walk<br>5. Broadway Theater District - 12 minute walk</p>',
                'post_image' => 'upload/blog/blog3.jpg',
            ],
            [
                'blogcat_id' => 4,
                'user_id' => 1,
                'post_titile' => 'A Culinary Journey: Our Chef\'s New Seasonal Menu',
                'post_slug' => 'a-culinary-journey-our-chefs-new-seasonal-menu',
                'short_descp' => 'Executive Chef Michael Chen presents a stunning new seasonal menu featuring locally-sourced ingredients.',
                'long_descp' => '<p>Our Executive Chef has crafted an extraordinary new menu that celebrates the flavors of the season...</p><p>Highlights include:<br>- Farm-fresh salads with house vinaigrette<br>- Pan-seared sea bass with citrus glaze<br>- Wagyu beef tenderloin<br>- Artisanal cheese selection<br>- Decadent chocolate soufflé</p>',
                'post_image' => 'upload/blog/blog4.jpg',
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::firstOrCreate(
                ['post_slug' => $post['post_slug']], 
                array_merge($post, ['created_at' => Carbon::now()->subDays(rand(1, 30))])
            );
        }
        $this->command->info('Blog Categories and Posts seeded!');
    }

    private function seedGallery(): void
    {
        $images = [
            'upload/gallery/gallery1.jpg',
            'upload/gallery/gallery2.jpg',
            'upload/gallery/gallery3.jpg',
            'upload/gallery/gallery4.jpg',
            'upload/gallery/gallery5.jpg',
            'upload/gallery/gallery6.jpg',
        ];

        foreach ($images as $image) {
            Gallery::firstOrCreate(
                ['photo_name' => $image], 
                ['created_at' => Carbon::now()]
            );
        }
        $this->command->info('Gallery seeded!');
    }
}
