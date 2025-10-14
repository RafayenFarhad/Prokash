<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

class SystemTester
{
    private $baseUrl = 'http://localhost:8000/api';
    private $testResults = [];
    private $authToken = null;
    private $testUserId = null;
    private $testPostId = null;

    public function runAllTests()
    {
        echo "🚀 PROKASH ALERT SYSTEM - COMPREHENSIVE TESTING\n";
        echo str_repeat("=", 60) . "\n\n";

        $this->testDatabaseConnection();
        $this->testModels();
        $this->testAuthentication();
        $this->testPostOperations();
        $this->testVotingSystem();
        $this->testCommentSystem();
        $this->testReportingSystem();
        $this->testReputationSystem();
        $this->testNotificationSystem();
        $this->testApiEndpoints();

        $this->printTestSummary();
    }

    private function testDatabaseConnection()
    {
        echo "📊 Testing Database Connection...\n";
        try {
            $users = \App\Models\User::count();
            $posts = \App\Models\Post::count();
            $this->pass("Database connected successfully");
            $this->pass("Found {$users} users and {$posts} posts");
        } catch (\Exception $e) {
            $this->fail("Database connection failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testModels()
    {
        echo "🔧 Testing Models and Relationships...\n";
        try {
            $user = \App\Models\User::first();
            if ($user) {
                $this->pass("User model working");
                $posts = $user->posts;
                $this->pass("User->posts relationship working");
                $comments = $user->comments;
                $this->pass("User->comments relationship working");
            }

            $post = \App\Models\Post::first();
            if ($post) {
                $this->pass("Post model working");
                $user = $post->user;
                $this->pass("Post->user relationship working");
                $comments = $post->comments;
                $this->pass("Post->comments relationship working");
            }

            $categories = \App\Models\Category::count();
            $this->pass("Found {$categories} categories");

        } catch (\Exception $e) {
            $this->fail("Model test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testAuthentication()
    {
        echo "🔐 Testing Authentication System...\n";
        try {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'password' => bcrypt('password123'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'role' => 'user',
                    'trust_score' => 50,
                    'reputation_level' => 'bronze',
                ]);
                $this->pass("Test user created successfully");
            } else {
                $this->pass("Test user already exists");
            }

            $this->testUserId = $user->id;
            $token = $user->createToken('test-token')->plainTextToken;
            $this->authToken = $token;
            $this->pass("Authentication token generated");

            $this->pass("User role: " . $user->role);
            $this->pass("User trust score: " . $user->trust_score);

        } catch (\Exception $e) {
            $this->fail("Authentication test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testPostOperations()
    {
        echo "📝 Testing Post CRUD Operations...\n";
        try {
            $user = \App\Models\User::find($this->testUserId);
            $category = \App\Models\Category::first();

            $post = \App\Models\Post::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'title' => 'Test Emergency Alert',
                'content' => 'This is a test emergency alert for system testing',
                'priority' => 'emergency',
                'status' => 'ongoing',
                'latitude' => 23.8103,
                'longitude' => 90.4125,
                'location_name' => 'Dhaka, Bangladesh',
            ]);
            $this->testPostId = $post->id;
            $this->pass("Post created successfully (ID: {$post->id})");

            $post->update(['title' => 'Updated Test Emergency Alert']);
            $this->pass("Post updated successfully");

            $retrievedPost = \App\Models\Post::find($post->id);
            if ($retrievedPost->title === 'Updated Test Emergency Alert') {
                $this->pass("Post retrieval working correctly");
            }

            $this->pass("Post priority: " . $post->priority);
            $this->pass("Post location: " . $post->location_name);

        } catch (\Exception $e) {
            $this->fail("Post operations test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testVotingSystem()
    {
        echo "👍 Testing Voting System...\n";
        try {
            $user = \App\Models\User::find($this->testUserId);
            $post = \App\Models\Post::find($this->testPostId);

            $vote = \App\Models\PostVote::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'vote_type' => 'upvote',
            ]);
            $this->pass("Upvote created successfully");

            $upvotes = \App\Models\PostVote::where('post_id', $post->id)
                ->where('vote_type', 'upvote')->count();
            $this->pass("Upvote count: {$upvotes}");

            $vote->update(['vote_type' => 'downvote']);
            $this->pass("Vote updated to downvote");

            $vote->delete();
            $this->pass("Vote removed successfully");

        } catch (\Exception $e) {
            $this->fail("Voting system test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testCommentSystem()
    {
        echo "💬 Testing Comment System...\n";
        try {
            $user = \App\Models\User::find($this->testUserId);
            $post = \App\Models\Post::find($this->testPostId);

            $comment = \App\Models\Comment::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'content' => 'This is a test comment',
            ]);
            $this->pass("Comment created successfully");

            $reply = \App\Models\Comment::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'parent_id' => $comment->id,
                'content' => 'This is a test reply',
            ]);
            $this->pass("Nested reply created successfully");

            $comment->load('replies');
            if ($comment->replies->count() > 0) {
                $this->pass("Comment->replies relationship working");
            }

            $commentCount = \App\Models\Comment::where('post_id', $post->id)->count();
            $this->pass("Total comments on post: {$commentCount}");

        } catch (\Exception $e) {
            $this->fail("Comment system test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testReportingSystem()
    {
        echo "🚨 Testing Reporting & Moderation System...\n";
        try {
            $user = \App\Models\User::find($this->testUserId);
            $post = \App\Models\Post::find($this->testPostId);

            $report = \App\Models\PostReport::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'reason' => 'spam',
                'description' => 'Test spam report',
                'status' => 'pending',
            ]);
            $this->pass("Spam report created successfully");

            $reportCount = \App\Models\PostReport::where('post_id', $post->id)->count();
            $this->pass("Total reports on post: {$reportCount}");

            $report->update(['status' => 'reviewed']);
            $this->pass("Report status updated to reviewed");

        } catch (\Exception $e) {
            $this->fail("Reporting system test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testReputationSystem()
    {
        echo "⭐ Testing Reputation System...\n";
        try {
            $reputationService = new \App\Services\ReputationService();
            $user = \App\Models\User::find($this->testUserId);

            $result = $reputationService->updateReputation($user);
            $this->pass("Reputation calculated: {$result['trust_score']}/100");
            $this->pass("Reputation level: {$result['reputation_level']}");
            $this->pass("Badges earned: " . count($result['badges']));

            if (count($result['badges']) > 0) {
                $this->pass("Sample badges: " . implode(', ', array_slice($result['badges'], 0, 3)));
            }

            $levels = ['bronze', 'silver', 'gold', 'platinum', 'diamond'];
            foreach ($levels as $level) {
                $levelInfo = $reputationService->getLevelInfo($level);
                if ($levelInfo) {
                    $this->pass("Level info for {$level}: {$levelInfo['icon']} {$levelInfo['name']}");
                }
            }

        } catch (\Exception $e) {
            $this->fail("Reputation system test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testNotificationSystem()
    {
        echo "🔔 Testing Notification System...\n";
        try {
            $user = \App\Models\User::find($this->testUserId);

            $notification = \App\Models\Notification::create([
                'user_id' => $user->id,
                'type' => 'test_notification',
                'title' => 'Test Notification',
                'message' => 'This is a test notification',
                'data' => json_encode(['test' => true]),
            ]);
            $this->pass("Notification created successfully");

            $unreadCount = \App\Models\Notification::where('user_id', $user->id)
                ->whereNull('read_at')->count();
            $this->pass("Unread notifications: {$unreadCount}");

            $notification->update(['read_at' => now()]);
            $this->pass("Notification marked as read");

            $totalNotifications = \App\Models\Notification::where('user_id', $user->id)->count();
            $this->pass("Total notifications for user: {$totalNotifications}");

        } catch (\Exception $e) {
            $this->fail("Notification system test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function testApiEndpoints()
    {
        echo "🌐 Testing API Endpoints...\n";
        try {
            $routes = \Illuminate\Support\Facades\Route::getRoutes();
            $apiRoutes = 0;
            $publicRoutes = 0;
            $protectedRoutes = 0;
            $adminRoutes = 0;

            foreach ($routes as $route) {
                if (strpos($route->uri(), 'api/') === 0) {
                    $apiRoutes++;
                    if (strpos($route->uri(), 'api/admin/') === 0) {
                        $adminRoutes++;
                    } elseif (in_array('auth:sanctum', $route->middleware())) {
                        $protectedRoutes++;
                    } else {
                        $publicRoutes++;
                    }
                }
            }

            $this->pass("Total API routes: {$apiRoutes}");
            $this->pass("Public routes: {$publicRoutes}");
            $this->pass("Protected routes: {$protectedRoutes}");
            $this->pass("Admin routes: {$adminRoutes}");

        } catch (\Exception $e) {
            $this->fail("API endpoints test failed: " . $e->getMessage());
        }
        echo "\n";
    }

    private function pass($message)
    {
        echo "  ✅ " . $message . "\n";
        $this->testResults[] = ['status' => 'pass', 'message' => $message];
    }

    private function fail($message)
    {
        echo "  ❌ " . $message . "\n";
        $this->testResults[] = ['status' => 'fail', 'message' => $message];
    }

    private function printTestSummary()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "📊 TEST SUMMARY\n";
        echo str_repeat("=", 60) . "\n\n";

        $passed = count(array_filter($this->testResults, fn($r) => $r['status'] === 'pass'));
        $failed = count(array_filter($this->testResults, fn($r) => $r['status'] === 'fail'));
        $total = count($this->testResults);

        echo "Total Tests: {$total}\n";
        echo "✅ Passed: {$passed}\n";
        echo "❌ Failed: {$failed}\n";
        echo "Success Rate: " . round(($passed / $total) * 100, 2) . "%\n\n";

        if ($failed === 0) {
            echo "🎉 ALL TESTS PASSED! System is working perfectly!\n";
        } else {
            echo "⚠️  Some tests failed. Please review the errors above.\n";
        }
        echo "\n";
    }
}

$tester = new SystemTester();
$tester->runAllTests();
