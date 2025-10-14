<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\PostVerification;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class BangladeshDataSeeder extends Seeder
{
    private $bangladeshiNames = [
        'Rahim Ahmed', 'Karim Rahman', 'Fatima Begum', 'Ayesha Khatun', 'Shakib Hassan',
        'Tamim Iqbal', 'Mushfiqur Rahman', 'Mahmudullah Riyad', 'Taskin Ahmed', 'Mustafizur Rahman',
        'Rubel Hossain', 'Mehidy Hasan', 'Liton Das', 'Soumya Sarkar', 'Sabbir Rahman',
        'Nasir Hossain', 'Anamul Haque', 'Imrul Kayes', 'Mominul Haque', 'Shamsur Rahman',
        'Nusrat Jahan', 'Salma Akter', 'Rupa Begum', 'Sharmin Sultana', 'Jahanara Alam',
        'Rafiqul Islam', 'Abdur Rahman', 'Khalid Mahmud', 'Jahangir Alam', 'Mizanur Rahman'
    ];

    private $bangladeshiLocations = [
        ['name' => 'Shahbag, Dhaka', 'lat' => 23.7389, 'lng' => 90.3950],
        ['name' => 'Dhanmondi, Dhaka', 'lat' => 23.7461, 'lng' => 90.3742],
        ['name' => 'Gulshan, Dhaka', 'lat' => 23.7808, 'lng' => 90.4161],
        ['name' => 'Mirpur, Dhaka', 'lat' => 23.8223, 'lng' => 90.3654],
        ['name' => 'Uttara, Dhaka', 'lat' => 23.8759, 'lng' => 90.3795],
        ['name' => 'Motijheel, Dhaka', 'lat' => 23.7330, 'lng' => 90.4172],
        ['name' => 'Banani, Dhaka', 'lat' => 23.7937, 'lng' => 90.4066],
        ['name' => 'Mohakhali, Dhaka', 'lat' => 23.7808, 'lng' => 90.3968],
        ['name' => 'Farmgate, Dhaka', 'lat' => 23.7574, 'lng' => 90.3887],
        ['name' => 'Badda, Dhaka', 'lat' => 23.7809, 'lng' => 90.4281],
        ['name' => 'Chittagong City', 'lat' => 22.3569, 'lng' => 91.7832],
        ['name' => 'Sylhet City', 'lat' => 24.8949, 'lng' => 91.8687],
        ['name' => 'Rajshahi City', 'lat' => 24.3745, 'lng' => 88.6042],
        ['name' => 'Khulna City', 'lat' => 22.8456, 'lng' => 89.5403],
        ['name' => 'Barisal City', 'lat' => 22.7010, 'lng' => 90.3535],
    ];

    private $bangladeshiPosts = [
        // Traffic Updates
        [
            'title' => 'Heavy traffic jam at Shahbag intersection', 
            'content' => 'Severe traffic congestion reported at Shahbag intersection due to ongoing construction work. Commuters are advised to use alternative routes via Karwan Bazar. Traffic police are on site managing the situation.', 
            'priority' => 'medium', 
            'category' => 'Traffic',
            'image' => 'https://images.unsplash.com/photo-1590674899484-d5640e854abe?w=800',
            'video' => null
        ],
        [
            'title' => 'Road accident on Dhaka-Chittagong Highway', 
            'content' => 'A major accident involving a bus and a truck has occurred near Daudkandi. Traffic is moving slowly. Emergency services are on site. Two lanes are blocked.', 
            'priority' => 'high', 
            'category' => 'Traffic',
            'image' => 'https://images.unsplash.com/photo-1449965408869-eaa3f722e40d?w=800',
            'video' => null
        ],
        [
            'title' => 'Flyover construction causing delays in Mohakhali', 
            'content' => 'The ongoing flyover construction at Mohakhali is causing significant traffic delays during peak hours. Please plan your journey accordingly. Expected completion in 3 months.', 
            'priority' => 'low', 
            'category' => 'Traffic',
            'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?w=800',
            'video' => null
        ],
        [
            'title' => 'Metro rail service disrupted at Mirpur', 
            'content' => 'Metro rail service temporarily suspended between Mirpur 10 and Mirpur 11 stations due to technical issues. Buses are being arranged for passengers. Service expected to resume in 2 hours.', 
            'priority' => 'high', 
            'category' => 'Traffic',
            'image' => 'https://images.unsplash.com/photo-1544620347-c4fd4a3d5957?w=800',
            'video' => null
        ],
        
        // Emergency Alerts
        [
            'title' => 'Fire breaks out in Chawkbazar', 
            'content' => 'A fire has broken out in a chemical warehouse in Chawkbazar area. 12 fire service units are working to control the blaze. Nearby residents are being evacuated. No casualties reported so far.', 
            'priority' => 'emergency', 
            'category' => 'Emergency',
            'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?w=800',
            'video' => null
        ],
        [
            'title' => 'Gas leak reported in Mohammadpur', 
            'content' => 'A gas leak has been reported in Mohammadpur Block C residential area. Titas Gas authority has been notified and repair team is on the way. Residents advised to stay alert and avoid using fire.', 
            'priority' => 'emergency', 
            'category' => 'Emergency',
            'image' => 'https://images.unsplash.com/photo-1584036561566-baf8f5f1b144?w=800',
            'video' => null
        ],
        [
            'title' => 'Building collapse in Savar', 
            'content' => 'A three-story building has partially collapsed in Savar Hemayetpur area. Rescue operations are underway. Fire service and army rescue teams are working. Several people feared trapped inside.', 
            'priority' => 'emergency', 
            'category' => 'Emergency',
            'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=800',
            'video' => null
        ],
        [
            'title' => 'Flood warning for low-lying areas', 
            'content' => 'Water Development Board has issued flood warning for low-lying areas in Dhaka. Turag and Buriganga rivers are flowing above danger level. Residents advised to move to safer places.', 
            'priority' => 'emergency', 
            'category' => 'Emergency',
            'image' => 'https://images.unsplash.com/photo-1547683905-f686c993aae5?w=800',
            'video' => null
        ],
        
        // Crime Reports
        [
            'title' => 'Pickpocketing incidents increase in New Market area', 
            'content' => 'Multiple pickpocketing incidents have been reported in New Market shopping area during evening hours. Shoppers are advised to be cautious and keep valuables secure. Police have increased patrolling.', 
            'priority' => 'medium', 
            'category' => 'Crime',
            'image' => 'https://images.unsplash.com/photo-1568605117036-5fe5e7bab0b7?w=800',
            'video' => null
        ],
        [
            'title' => 'Robbery attempt foiled in Banani', 
            'content' => 'Police successfully prevented a robbery attempt at a jewelry shop in Banani Road 11. Three suspects have been arrested. Stolen items worth 5 lakh taka recovered.', 
            'priority' => 'medium', 
            'category' => 'Crime',
            'image' => 'https://images.unsplash.com/photo-1590856029826-c7a73142bbf1?w=800',
            'video' => null
        ],
        [
            'title' => 'Cyber fraud alert in Dhaka', 
            'content' => 'Police cyber crime unit warns about increasing online shopping scams. Several fake e-commerce sites are operating. Citizens advised to verify before making online payments.', 
            'priority' => 'medium', 
            'category' => 'Crime',
            'image' => 'https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=800',
            'video' => null
        ],
        
        // Market Prices
        [
            'title' => 'Onion prices surge in Karwan Bazar', 
            'content' => 'Onion prices have increased significantly in Karwan Bazar. Currently selling at 80-90 Tk per kg due to supply shortage from India. Traders expect prices to stabilize next week.', 
            'priority' => 'low', 
            'category' => 'Market',
            'image' => 'https://images.unsplash.com/photo-1618512496248-a07fe83aa8cb?w=800',
            'video' => null
        ],
        [
            'title' => 'Fish prices drop at Kawran Bazar', 
            'content' => 'Good news for fish lovers! Hilsa fish prices have dropped to 1200-1400 Tk per kg at Kawran Bazar due to increased supply from Padma river. Rui and Katla also available at reasonable prices.', 
            'priority' => 'low', 
            'category' => 'Market',
            'image' => 'https://images.unsplash.com/photo-1534043464124-3be32fe000c9?w=800',
            'video' => null
        ],
        [
            'title' => 'Vegetable prices stable this week', 
            'content' => 'Vegetable prices remain stable at Mohakhali kitchen market. Tomatoes at 40 Tk/kg, potatoes at 30 Tk/kg, green chilies at 60 Tk/kg, and eggplant at 35 Tk/kg. Good supply from Jessore and Bogra.', 
            'priority' => 'low', 'category' => 'Market',
            'image' => 'https://images.unsplash.com/photo-1597362925123-77861d3fbac7?w=800',
            'video' => null
        ],
        [
            'title' => 'Beef prices increase before Eid', 
            'content' => 'Beef prices have gone up by 50 Tk per kg ahead of Eid-ul-Adha. Currently selling at 650-700 Tk per kg in Dhaka markets. Cattle traders cite increased demand and transportation costs.', 
            'priority' => 'low', 
            'category' => 'Market',
            'image' => 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=800',
            'video' => null
        ],
        
        // Weather Alerts
        [
            'title' => 'Heavy rainfall expected in Dhaka', 
            'content' => 'Bangladesh Meteorological Department has issued a warning for heavy rainfall in Dhaka and surrounding areas for the next 24 hours. Waterlogging expected in low-lying areas. Thunder and lightning likely.', 
            'priority' => 'high', 
            'category' => 'Weather',
            'image' => 'https://images.unsplash.com/photo-1527482797697-8795b05a13fe?w=800',
            'video' => null
        ],
        [
            'title' => 'Heatwave continues in northern districts', 
            'content' => 'Severe heatwave continues in Rajshahi, Rangpur, and surrounding districts. Temperature reaching 40°C. People advised to stay hydrated, avoid sun exposure between 11 AM to 3 PM.', 
            'priority' => 'medium', 
            'category' => 'Weather',
            'image' => 'https://images.unsplash.com/photo-1601134467661-3d775b999c8b?w=800',
            'video' => null
        ],
        [
            'title' => 'Cyclone warning for coastal areas', 
            'content' => 'A low pressure in Bay of Bengal may turn into cyclone. Coastal districts of Chittagong, Cox\'s Bazar, and Bhola are on alert. Fishing boats advised not to venture into deep sea.', 
            'priority' => 'emergency', 
            'category' => 'Weather',
            'image' => 'https://images.unsplash.com/photo-1527482937786-6608eea82c37?w=800',
            'video' => null
        ],
        
        // Community Events
        [
            'title' => 'Book fair at Bangla Academy starting tomorrow', 
            'content' => 'The annual Ekushey Book Fair will begin tomorrow at Bangla Academy and Suhrawardy Udyan. Over 500 publishers will participate. Entry is free. Special children\'s section and cultural programs daily.', 
            'priority' => 'low', 
            'category' => 'Events',
            'image' => 'https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=800',
            'video' => null
        ],
        [
            'title' => 'Free medical camp in Mirpur', 
            'content' => 'A free medical camp will be organized at Mirpur-10 community center this Friday from 9 AM to 5 PM. General health checkup, diabetes screening, blood pressure monitoring, and eye checkup available. Medicines will be provided free.', 
            'priority' => 'low', 
            'category' => 'Events',
            'image' => 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800',
            'video' => null
        ],
        [
            'title' => 'Pohela Boishakh celebrations at Ramna Batamul', 
            'content' => 'Traditional Pohela Boishakh celebrations will be held at Ramna Batamul starting 6 AM. Cultural programs, traditional food stalls, and Mangal Shobhajatra procession planned. Thousands expected to participate.', 
            'priority' => 'low', 
            'category' => 'Events',
            'image' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?w=800',
            'video' => null
        ],
        [
            'title' => 'Victory Day parade at National Parade Ground', 
            'content' => 'Grand Victory Day parade will be held at National Parade Ground on December 16. Armed forces, students, and various organizations will participate. President and Prime Minister will attend.', 
            'priority' => 'low', 
            'category' => 'Events',
            'image' => 'https://images.unsplash.com/photo-1569098644584-210bcd375b59?w=800',
            'video' => null
        ],
        
        // Public Notices
        [
            'title' => 'Power outage scheduled in Dhanmondi', 
            'content' => 'DESCO has announced a scheduled power outage in Dhanmondi area (Road 3-15) on Saturday from 10 AM to 4 PM for maintenance work. Residents requested to plan accordingly.', 
            'priority' => 'medium', 
            'category' => 'Public Notice',
            'image' => 'https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800',
            'video' => null
        ],
        [
            'title' => 'Water supply disruption in Gulshan', 
            'content' => 'WASA has announced water supply disruption in Gulshan 1 and 2 areas tomorrow from 9 AM to 3 PM due to pipeline repair work. Residents advised to store water in advance.', 
            'priority' => 'medium', 
            'category' => 'Public Notice',
            'image' => 'https://images.unsplash.com/photo-1625246333195-78d9c38ad449?w=800',
            'video' => null
        ],
        [
            'title' => 'Road closure for Dhaka Marathon', 
            'content' => 'Several roads in Dhaka will be closed on Sunday morning for Dhaka Marathon 2025. Shahbag to Manik Mia Avenue route will be blocked from 6 AM to 10 AM. Alternative routes suggested.', 
            'priority' => 'medium', 
            'category' => 'Public Notice',
            'image' => 'https://images.unsplash.com/photo-1452626038306-9aae5e071dd3?w=800',
            'video' => null
        ],
        
        // General Info
        [
            'title' => 'New metro rail station opens in Uttara', 
            'content' => 'A new metro rail station has been inaugurated at Uttara Sector 7. This will significantly reduce travel time for commuters in the area. Station features modern facilities and accessibility features.', 
            'priority' => 'low', 
            'category' => 'General',
            'image' => 'https://images.unsplash.com/photo-1554672407-fb0e3e3c8c87?w=800',
            'video' => null
        ],
        [
            'title' => 'COVID-19 vaccination drive at Dhaka Medical', 
            'content' => 'Dhaka Medical College Hospital is conducting a special COVID-19 vaccination drive this week. Booster doses available for all age groups. No prior appointment needed. Bring your NID card.', 
            'priority' => 'medium', 
            'category' => 'General',
            'image' => 'https://images.unsplash.com/photo-1631549916768-4119b2e5f926?w=800',
            'video' => null
        ],
        [
            'title' => 'Traffic rule enforcement campaign starts', 
            'content' => 'Dhaka Metropolitan Police has started a strict traffic rule enforcement campaign. Heavy fines for helmet violations, signal jumping, and driving without license. Mobile courts are operating.', 
            'priority' => 'medium', 
            'category' => 'General',
            'image' => 'https://images.unsplash.com/photo-1591768793355-74d04bb6608f?w=800',
            'video' => null
        ],
        [
            'title' => 'New flyover inaugurated at Banani', 
            'content' => 'The much-awaited Banani-Mohakhali flyover has been inaugurated today. The 2.5 km flyover will ease traffic congestion significantly. Construction took 3 years to complete.', 
            'priority' => 'low', 
            'category' => 'General',
            'image' => 'https://images.unsplash.com/photo-1569163139394-de4798aa62b6?w=800',
            'video' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ'
        ],
        [
            'title' => 'Street food festival at Hatirjheel', 
            'content' => 'A three-day street food festival is being organized at Hatirjheel amphitheater. Over 50 food stalls featuring traditional Bangladeshi cuisine. Live music and cultural performances every evening.', 
            'priority' => 'low', 
            'category' => 'Events',
            'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800',
            'video' => null
        ],
    ];

    private $bangladeshiComments = [
        'ধন্যবাদ তথ্যের জন্য! Very helpful information.',
        'আমিও এই এলাকায় আছি। পরিস্থিতি সত্যিই খারাপ।',
        'Thanks for the update. This is very important.',
        'কর্তৃপক্ষের এই বিষয়ে দ্রুত পদক্ষেপ নেওয়া উচিত।',
        'Confirmed! I just passed through this area.',
        'এই তথ্য শেয়ার করার জন্য অনেক ধন্যবাদ।',
        'Stay safe everyone! Be careful in this area.',
        'আমার পরিবারও এই এলাকায়। খুবই উদ্বিগ্ন।',
        'Good to know. Will avoid this route today.',
        'সবাই সতর্ক থাকুন এবং নিরাপদ থাকুন।',
        'Thanks for the heads up! Really appreciate it.',
        'আশা করি শীঘ্রই পরিস্থিতি স্বাভাবিক হবে।',
        'This is exactly what I needed to know. Thank you!',
        'আমি এখন সেখানে যাচ্ছিলাম। ধন্যবাদ সতর্ক করার জন্য।',
        'Very useful information for the community.',
    ];

    public function run()
    {
        $this->command->info('🇧🇩 Starting Bangladesh Data Seeder...');

        // Create or get existing users
        $this->command->info('Creating/fetching users...');
        $users = [];
        foreach ($this->bangladeshiNames as $index => $name) {
            $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
            
            // Check if user already exists
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'trust_score' => rand(50, 95),
                    'reputation_level' => ['bronze', 'silver', 'gold', 'platinum'][rand(0, 3)],
                    'created_at' => now()->subDays(rand(1, 90)),
                ]);
            }
            
            $users[] = $user;
        }
        $this->command->info('✅ Ready with ' . count($users) . ' users');

        // Get categories
        $categories = Category::all();
        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Create posts
        $this->command->info('Creating posts...');
        $posts = [];
        foreach ($this->bangladeshiPosts as $postData) {
            $location = $this->bangladeshiLocations[array_rand($this->bangladeshiLocations)];
            $category = $categories->where('name', $postData['category'])->first() 
                     ?? $categories->random();
            
            $post = Post::create([
                'user_id' => $users[array_rand($users)]->id,
                'category_id' => $category->id,
                'title' => $postData['title'],
                'content' => $postData['content'],
                'priority' => $postData['priority'],
                'image' => $postData['image'] ?? null,
                'video' => $postData['video'] ?? null,
                'latitude' => $location['lat'],
                'longitude' => $location['lng'],
                'location_name' => $location['name'],
                'is_verified' => false,
                'verification_score' => 0,
                'upvotes' => 0,
                'downvotes' => 0,
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
            $posts[] = $post;
        }
        $this->command->info('✅ Created ' . count($posts) . ' posts');

        // Create votes (upvotes and downvotes)
        $this->command->info('Creating votes...');
        $totalVotes = 0;
        foreach ($posts as $post) {
            // Each post gets 3-8 votes
            $numVotes = rand(3, 8);
            $votedUsers = collect($users)->random($numVotes);
            
            foreach ($votedUsers as $voter) {
                // 80% chance of upvote, 20% chance of downvote
                $voteType = rand(1, 100) <= 80 ? 'upvote' : 'downvote';
                
                PostVerification::create([
                    'user_id' => $voter->id,
                    'post_id' => $post->id,
                    'type' => $voteType,
                    'created_at' => $post->created_at->addMinutes(rand(5, 1440)),
                ]);
                $totalVotes++;
            }
            
            // Update post verification status
            $this->updatePostVerification($post);
        }
        $this->command->info('✅ Created ' . $totalVotes . ' votes');

        // Create comments
        $this->command->info('Creating comments...');
        $totalComments = 0;
        foreach ($posts as $post) {
            // Each post gets 2-5 comments
            $numComments = rand(2, 5);
            
            for ($i = 0; $i < $numComments; $i++) {
                Comment::create([
                    'user_id' => $users[array_rand($users)]->id,
                    'post_id' => $post->id,
                    'content' => $this->bangladeshiComments[array_rand($this->bangladeshiComments)],
                    'created_at' => $post->created_at->addMinutes(rand(10, 2880)),
                ]);
                $totalComments++;
            }
        }
        $this->command->info('✅ Created ' . $totalComments . ' comments');

        $this->command->info('🎉 Bangladesh Data Seeder completed successfully!');
        $this->command->info('📊 Summary:');
        $this->command->info('   - Users: ' . count($users));
        $this->command->info('   - Posts: ' . count($posts));
        $this->command->info('   - Votes: ' . $totalVotes);
        $this->command->info('   - Comments: ' . $totalComments);
        
        // Show verification stats
        $verifiedCount = Post::where('is_verified', true)->count();
        $verificationRate = count($posts) > 0 ? round(($verifiedCount / count($posts)) * 100) : 0;
        $this->command->info('   - Verified Posts: ' . $verifiedCount . ' (' . $verificationRate . '%)');
    }

    private function updatePostVerification($post)
    {
        $upvotes = $post->verifications()->where('type', 'upvote')->count();
        $downvotes = $post->verifications()->where('type', 'downvote')->count();
        $score = $upvotes - $downvotes;
        
        $post->update([
            'upvotes' => $upvotes,
            'downvotes' => $downvotes,
            'verification_score' => $score,
            'is_verified' => $score >= 3,
        ]);
    }
}
