<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PropertyType;
use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;

class MigrateLegacyHotelDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;
        $ptype = PropertyType::firstOrCreate(['name' => 'Hotel']);
        $property = Property::firstOrCreate(
            ['name' => 'Default Hotel'],
            [
                'property_type_id' => $ptype->id,
                'user_id' => $admin->id,
                'address' => 'Default Address',
                'city' => 'Default City',
                'country' => 'India',
                'status' => 'active',
            ]
        );
        Room::whereNull('property_id')->update(['property_id' => $property->id]);
        Booking::whereNull('property_id')->update(['property_id' => $property->id]);
    }
}
