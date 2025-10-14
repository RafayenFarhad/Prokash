<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Auto-categorize a post
     */
    public function categorizePost(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $categoryId = $this->aiService->categorizePost($request->title, $request->content);

        return response()->json([
            'suggested_category_id' => $categoryId,
            'message' => $categoryId ? 'Category suggested by AI' : 'AI categorization unavailable',
        ]);
    }

    /**
     * Detect fake news in a post
     */
    public function detectFakeNews(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $analysis = $this->aiService->detectFakeNews($request->title, $request->content);

        return response()->json([
            'analysis' => $analysis,
            'recommendation' => $analysis['is_suspicious'] 
                ? 'This post may contain misinformation. Please verify before sharing.'
                : 'Content appears to be legitimate.',
        ]);
    }

    /**
     * Analyze post sentiment
     */
    public function analyzeSentiment(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $sentiment = $this->aiService->analyzeSentiment($request->title, $request->content);

        return response()->json($sentiment);
    }

    /**
     * Get post improvement suggestions
     */
    public function getPostSuggestions(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        $suggestions = $this->aiService->generatePostSuggestions($request->title, $request->content);

        return response()->json([
            'suggestions' => $suggestions,
            'count' => count($suggestions),
        ]);
    }

    /**
     * Generate automatic weather alerts
     */
    public function generateWeatherAlerts(Request $request)
    {
        $this->authorize('admin'); // Admin only

        $alerts = $this->aiService->generateWeatherAlerts();
        $createdAlerts = [];

        foreach ($alerts as $alertData) {
            $post = Post::create([
                'title' => $alertData['title'],
                'content' => $alertData['content'],
                'priority' => $alertData['priority'],
                'category_id' => $alertData['category_id'],
                'user_id' => 1, // System user
                'status' => 'ongoing',
            ]);

            $createdAlerts[] = $post;
        }

        return response()->json([
            'alerts_created' => count($createdAlerts),
            'alerts' => $createdAlerts,
            'message' => 'Weather alerts generated successfully',
        ]);
    }

    /**
     * Generate traffic incident alerts
     */
    public function generateTrafficAlerts(Request $request)
    {
        $this->authorize('admin'); // Admin only

        $incidents = $this->aiService->getTrafficIncidents();
        $createdAlerts = [];

        foreach ($incidents as $incidentData) {
            $post = Post::create([
                'title' => $incidentData['title'],
                'content' => $incidentData['content'],
                'priority' => $incidentData['priority'],
                'category_id' => $incidentData['category_id'],
                'latitude' => $incidentData['latitude'],
                'longitude' => $incidentData['longitude'],
                'location_name' => $incidentData['location_name'],
                'user_id' => 1, // System user
                'status' => 'ongoing',
            ]);

            $createdAlerts[] = $post;
        }

        return response()->json([
            'incidents_created' => count($createdAlerts),
            'incidents' => $createdAlerts,
            'message' => 'Traffic alerts generated successfully',
        ]);
    }

    /**
     * Analyze existing post for AI insights
     */
    public function analyzePost(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $analysis = [
            'category_suggestion' => $this->aiService->categorizePost($post->title, $post->content),
            'fake_news_check' => $this->aiService->detectFakeNews($post->title, $post->content),
            'sentiment' => $this->aiService->analyzeSentiment($post->title, $post->content),
            'suggestions' => $this->aiService->generatePostSuggestions($post->title, $post->content),
        ];

        return response()->json([
            'post_id' => $postId,
            'analysis' => $analysis,
            'recommendations' => $this->generateRecommendations($analysis),
        ]);
    }

    /**
     * Generate recommendations based on AI analysis
     */
    private function generateRecommendations($analysis)
    {
        $recommendations = [];

        // Category recommendation
        if ($analysis['category_suggestion'] && $analysis['category_suggestion'] !== request()->post->category_id) {
            $recommendations[] = 'Consider changing the category based on AI analysis';
        }

        // Fake news warning
        if ($analysis['fake_news_check']['is_suspicious'] && $analysis['fake_news_check']['confidence'] > 70) {
            $recommendations[] = 'This post may contain misinformation - review carefully';
        }

        // Sentiment-based recommendations
        if ($analysis['sentiment']['emotion'] === 'panicked') {
            $recommendations[] = 'Consider using calmer language to avoid panic';
        }

        // Add suggestions
        if (!empty($analysis['suggestions'])) {
            $recommendations = array_merge($recommendations, $analysis['suggestions']);
        }

        return $recommendations;
    }

    /**
     * Batch analyze multiple posts
     */
    public function batchAnalyze(Request $request)
    {
        $this->authorize('admin');

        $limit = $request->get('limit', 10);
        $posts = Post::latest()->limit($limit)->get();

        $results = [];
        foreach ($posts as $post) {
            $analysis = [
                'post_id' => $post->id,
                'title' => $post->title,
                'fake_news_score' => $this->aiService->detectFakeNews($post->title, $post->content),
                'sentiment' => $this->aiService->analyzeSentiment($post->title, $post->content),
            ];

            $results[] = $analysis;
        }

        return response()->json([
            'analyzed_posts' => count($results),
            'results' => $results,
        ]);
    }
}
