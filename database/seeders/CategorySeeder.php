<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing categories first
        Category::truncate();
        
        $categories = [
            [
                'name' => 'Traffic & Transportation',
                'slug' => 'traffic-transportation',
                'description' => 'Traffic jams, accidents, road conditions, fare updates, public transport alerts'
            ],
            [
                'name' => 'Crime & Safety',
                'slug' => 'crime-safety',
                'description' => 'Fraud alerts, missing persons, harassment reports, safety warnings, crime incidents'
            ],
            [
                'name' => 'Price & Market Updates',
                'slug' => 'price-market',
                'description' => 'Daily goods prices, fuel prices, market rates, price changes'
            ],
            [
                'name' => 'Lost & Found',
                'slug' => 'lost-found',
                'description' => 'Lost items, found items, missing pets, lost documents'
            ],
            [
                'name' => 'Local Services & Help',
                'slug' => 'local-services',
                'description' => 'Blood donation requests, power outages, water supply issues, emergency help'
            ],
            [
                'name' => 'Emergency Alerts',
                'slug' => 'emergency',
                'description' => 'Urgent emergencies, natural disasters, critical situations requiring immediate attention'
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
