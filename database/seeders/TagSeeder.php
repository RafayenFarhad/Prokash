<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tag;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing tags first (using delete to avoid foreign key issues)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Tag::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $tags = [
            ['name' => 'Urgent', 'slug' => 'urgent'],
            ['name' => 'Verified', 'slug' => 'verified'],
            ['name' => 'Ongoing', 'slug' => 'ongoing'],
            ['name' => 'Resolved', 'slug' => 'resolved'],
            ['name' => 'Help Needed', 'slug' => 'help-needed'],
            ['name' => 'Update', 'slug' => 'update'],
            ['name' => 'Warning', 'slug' => 'warning'],
            ['name' => 'Breaking', 'slug' => 'breaking'],
            ['name' => 'Local', 'slug' => 'local'],
            ['name' => 'Citywide', 'slug' => 'citywide'],
            ['name' => 'Accident', 'slug' => 'accident'],
            ['name' => 'Road Closed', 'slug' => 'road-closed'],
            ['name' => 'Missing Person', 'slug' => 'missing-person'],
            ['name' => 'Found', 'slug' => 'found'],
            ['name' => 'Scam Alert', 'slug' => 'scam-alert'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
