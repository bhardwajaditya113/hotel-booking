<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\HostProfile;
use Carbon\Carbon;

class PropertySeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding Properties...');

        // Create Property Types if they don't exist
        $propertyTypes = [
            'Hotel',
            'Apartment',
            'Villa',
            'Resort',
            'Home',
            'Condo',
        ];

        foreach ($propertyTypes as $typeName) {
            PropertyType::firstOrCreate(['name' => $typeName]);
        }

        // Get or create a host user
        $host = User::firstOrCreate(
            ['email' => 'host@example.com'],
            [
                'name' => 'John Host',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]
        );

        // Create host profile
        HostProfile::firstOrCreate(
            ['user_id' => $host->id],
            [
                'display_name' => $host->name,
                'phone' => '+1234567890',
                'bio' => 'Experienced host with multiple properties',
                'verification_status' => 'verified',
                'is_superhost' => true,
            ]
        );

        // Create additional host users
        $hosts = [];
        for ($i = 1; $i <= 5; $i++) {
            $hostUser = User::firstOrCreate(
                ['email' => "host{$i}@example.com"],
                [
                    'name' => "Host User {$i}",
                    'password' => bcrypt('password'),
                    'role' => 'user',
                ]
            );
            HostProfile::firstOrCreate(
                ['user_id' => $hostUser->id],
                [
                    'display_name' => $hostUser->name,
                    'phone' => "+123456789{$i}",
                    'bio' => "Professional host with great properties",
                    'verification_status' => $i <= 3 ? 'verified' : 'pending',
                    'is_superhost' => $i <= 2,
                ]
            );
            $hosts[] = $hostUser;
        }

        // Get room types
        $roomTypes = RoomType::all();
        if ($roomTypes->isEmpty()) {
            $this->command->warn('No room types found. Please run MockDataSeeder first.');
            return;
        }

        // Cities and locations
        $cities = [
            ['city' => 'Mumbai', 'state' => 'Maharashtra', 'country' => 'India', 'lat' => 19.0760, 'lng' => 72.8777],
            ['city' => 'Delhi', 'state' => 'Delhi', 'country' => 'India', 'lat' => 28.6139, 'lng' => 77.2090],
            ['city' => 'Bangalore', 'state' => 'Karnataka', 'country' => 'India', 'lat' => 12.9716, 'lng' => 77.5946],
            ['city' => 'Goa', 'state' => 'Goa', 'country' => 'India', 'lat' => 15.2993, 'lng' => 74.1240],
            ['city' => 'Jaipur', 'state' => 'Rajasthan', 'country' => 'India', 'lat' => 26.9124, 'lng' => 75.7873],
        ];

        // Properties data
        $properties = [
            // Hotels
            [
                'name' => 'Grand Luxury Hotel',
                'listing_type' => 'hotel',
                'property_type_id' => PropertyType::where('name', 'Hotel')->first()->id,
                'user_id' => $host->id,
                'address' => '123 Marine Drive',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'zipcode' => '400001',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'description' => 'Experience luxury at its finest in the heart of Mumbai. Our 5-star hotel offers world-class amenities, fine dining, and exceptional service.',
                'amenities' => ['wifi', 'pool', 'spa', 'gym', 'restaurant', 'bar', 'parking', 'concierge'],
                'images' => ['property1.jpg', 'property1-2.jpg', 'property1-3.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => true,
                'instant_book_enabled' => true,
                'average_rating' => 4.8,
            ],
            [
                'name' => 'Metro City Hotel',
                'listing_type' => 'hotel',
                'property_type_id' => PropertyType::where('name', 'Hotel')->first()->id,
                'user_id' => $hosts[0]->id,
                'address' => '456 Connaught Place',
                'city' => 'Delhi',
                'state' => 'Delhi',
                'country' => 'India',
                'zipcode' => '110001',
                'latitude' => 28.6139,
                'longitude' => 77.2090,
                'description' => 'Modern hotel in the heart of Delhi with excellent connectivity and comfortable rooms.',
                'amenities' => ['wifi', 'gym', 'restaurant', 'parking'],
                'images' => ['property2.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => false,
                'instant_book_enabled' => true,
                'average_rating' => 4.5,
            ],
            [
                'name' => 'Tech Park Resort',
                'listing_type' => 'hotel',
                'property_type_id' => PropertyType::where('name', 'Resort')->first()->id,
                'user_id' => $hosts[1]->id,
                'address' => '789 IT Park Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country' => 'India',
                'zipcode' => '560001',
                'latitude' => 12.9716,
                'longitude' => 77.5946,
                'description' => 'Perfect for business travelers. Located near major tech parks with modern amenities.',
                'amenities' => ['wifi', 'business-center', 'gym', 'restaurant', 'parking'],
                'images' => ['property3.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => true,
                'instant_book_enabled' => false,
                'average_rating' => 4.6,
            ],
            // Unique Stays
            [
                'name' => 'Beachfront Villa Paradise',
                'listing_type' => 'unique_stay',
                'property_type_id' => PropertyType::where('name', 'Villa')->first()->id,
                'user_id' => $hosts[2]->id,
                'address' => 'Beach Road, Calangute',
                'city' => 'Goa',
                'state' => 'Goa',
                'country' => 'India',
                'zipcode' => '403516',
                'latitude' => 15.2993,
                'longitude' => 74.1240,
                'description' => 'Stunning beachfront villa with private pool, direct beach access, and breathtaking ocean views. Perfect for families and groups.',
                'amenities' => ['wifi', 'pool', 'kitchen', 'parking', 'beach-access', 'bbq'],
                'images' => ['property4.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => true,
                'instant_book_enabled' => true,
                'average_rating' => 4.9,
            ],
            [
                'name' => 'Cozy Downtown Apartment',
                'listing_type' => 'unique_stay',
                'property_type_id' => PropertyType::where('name', 'Apartment')->first()->id,
                'user_id' => $hosts[3]->id,
                'address' => '123 MG Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country' => 'India',
                'zipcode' => '560001',
                'latitude' => 12.9716,
                'longitude' => 77.5946,
                'description' => 'Beautifully furnished apartment in the heart of the city. Walking distance to restaurants, shopping, and entertainment.',
                'amenities' => ['wifi', 'kitchen', 'parking', 'washer'],
                'images' => ['property5.jpg'],
                'status' => 'active',
                'verification_status' => 'pending',
                'is_featured' => false,
                'instant_book_enabled' => true,
                'average_rating' => 4.3,
            ],
            [
                'name' => 'Heritage Haveli Stay',
                'listing_type' => 'unique_stay',
                'property_type_id' => PropertyType::where('name', 'Home')->first()->id,
                'user_id' => $hosts[4]->id,
                'address' => '456 Old City',
                'city' => 'Jaipur',
                'state' => 'Rajasthan',
                'country' => 'India',
                'zipcode' => '302001',
                'latitude' => 26.9124,
                'longitude' => 75.7873,
                'description' => 'Experience authentic Rajasthani hospitality in this beautifully restored heritage haveli. Traditional architecture meets modern comfort.',
                'amenities' => ['wifi', 'kitchen', 'parking', 'garden', 'traditional-decor'],
                'images' => ['property6.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => false,
                'instant_book_enabled' => false,
                'average_rating' => 4.7,
            ],
            [
                'name' => 'Modern City Condo',
                'listing_type' => 'unique_stay',
                'property_type_id' => PropertyType::where('name', 'Condo')->first()->id,
                'user_id' => $host->id,
                'address' => '789 High Street',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'country' => 'India',
                'zipcode' => '400001',
                'latitude' => 19.0760,
                'longitude' => 72.8777,
                'description' => 'Sleek modern condo with city views. Fully equipped kitchen, high-speed WiFi, and access to building amenities.',
                'amenities' => ['wifi', 'kitchen', 'parking', 'gym', 'pool'],
                'images' => ['property7.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => true,
                'instant_book_enabled' => true,
                'average_rating' => 4.6,
            ],
            [
                'name' => 'Luxury Beach Resort',
                'listing_type' => 'hotel',
                'property_type_id' => PropertyType::where('name', 'Resort')->first()->id,
                'user_id' => $hosts[0]->id,
                'address' => 'Beach Resort Road',
                'city' => 'Goa',
                'state' => 'Goa',
                'country' => 'India',
                'zipcode' => '403516',
                'latitude' => 15.2993,
                'longitude' => 74.1240,
                'description' => '5-star beachfront resort with multiple pools, spa, fine dining, and water sports. Perfect for a luxury vacation.',
                'amenities' => ['wifi', 'pool', 'spa', 'gym', 'restaurant', 'bar', 'beach-access', 'water-sports'],
                'images' => ['property8.jpg'],
                'status' => 'active',
                'verification_status' => 'verified',
                'is_featured' => true,
                'instant_book_enabled' => true,
                'average_rating' => 4.8,
            ],
        ];

        foreach ($properties as $propertyData) {
            $property = Property::firstOrCreate(
                ['name' => $propertyData['name'], 'city' => $propertyData['city']],
                array_merge($propertyData, [
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                    'cover_image' => $propertyData['images'][0] ?? 'no_image.jpg',
                ])
            );

            // Create rooms for each property
            $roomCount = $propertyData['listing_type'] === 'hotel' ? rand(3, 8) : rand(1, 3);
            for ($i = 0; $i < $roomCount; $i++) {
                $roomType = $roomTypes->random();
                $basePrice = $propertyData['listing_type'] === 'hotel' 
                    ? rand(2000, 8000) 
                    : rand(3000, 12000);

                Room::firstOrCreate(
                    [
                        'property_id' => $property->id,
                        'roomtype_id' => $roomType->id,
                    ],
                    [
                        'total_adult' => rand(2, 4),
                        'total_child' => rand(0, 2),
                        'room_capacity' => rand(2, 6),
                        'price' => $basePrice,
                        'size' => rand(300, 800),
                        'view' => ['City View', 'Ocean View', 'Garden View', 'Mountain View'][rand(0, 3)],
                        'bed_style' => ['King Bed', 'Queen Bed', 'Double Bed', 'Twin Beds'][rand(0, 3)],
                        'discount' => rand(0, 20),
                        'short_desc' => 'Comfortable and well-appointed room',
                        'description' => 'Beautiful room with modern amenities and comfortable furnishings.',
                        'status' => 1,
                        'image' => 'upload/roomimg/room' . rand(1, 5) . '.jpg',
                    ]
                );
            }

            $this->command->info("Created property: {$property->name}");
        }

        $this->command->info('Properties seeded successfully!');
    }
}

