<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class CleaningServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cleaningServices = [
            [
                'name' => 'Residential Cleaning',
                'description' => 'Professional cleaning services for residential properties.',
                'price' => 50.00, // Example price
            ],
            [
                'name' => 'Commercial Cleaning',
                'description' => 'Commercial cleaning services for businesses and offices.',
                'price' => 100.00, // Example price
            ],
            // Add more cleaning services as needed
        ];

        // Loop through the cleaning services array and insert them into the database
        foreach ($cleaningServices as $service) {
            Service::create($service);
        }

    }
}
