<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Http\Request;

class ReputationController extends Controller
{
    protected $reputationService;

    public function __construct(ReputationService $reputationService)
    {
        $this->reputationService = $reputationService;
    }

    /**
     * Get user reputation info
     */
    public function show(Request $request, $userId = null)
    {
        $user = $userId ? User::findOrFail($userId) : $request->user();
        
        $reputation = $this->reputationService->updateReputation($user);
        $levelInfo = $this->reputationService->getLevelInfo($reputation['reputation_level']);
        
        $badgeDetails = [];
        foreach ($reputation['badges'] as $badge) {
            $badgeDetails[] = $this->reputationService->getBadgeInfo($badge);
        }

        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'trust_score' => $reputation['trust_score'],
            'reputation_level' => $reputation['reputation_level'],
            'level_info' => $levelInfo,
            'badges' => $reputation['badges'],
            'badge_details' => $badgeDetails,
            'stats' => [
                'total_posts' => $user->posts()->count(),
                'verified_posts' => $user->posts()->where('is_verified', true)->count(),
                'total_upvotes' => $user->posts()->sum('upvotes'),
                'total_downvotes' => $user->posts()->sum('downvotes'),
            ]
        ]);
    }

    /**
     * Get leaderboard
     */
    public function leaderboard(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        $users = User::where('is_active', true)
            ->orderBy('trust_score', 'desc')
            ->limit($limit)
            ->get();

        $leaderboard = [];
        foreach ($users as $index => $user) {
            $reputation = $this->reputationService->updateReputation($user);
            $levelInfo = $this->reputationService->getLevelInfo($reputation['reputation_level']);
            
            $leaderboard[] = [
                'rank' => $index + 1,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'trust_score' => $reputation['trust_score'],
                'reputation_level' => $reputation['reputation_level'],
                'level_info' => $levelInfo,
                'badges_count' => count($reputation['badges']),
                'total_posts' => $user->posts()->count(),
            ];
        }

        return response()->json($leaderboard);
    }

    /**
     * Update user reputation manually (admin only)
     */
    public function update(Request $request, $userId)
    {
        $this->authorize('admin');
        
        $user = User::findOrFail($userId);
        $reputation = $this->reputationService->updateReputation($user);

        return response()->json([
            'message' => 'Reputation updated successfully',
            'reputation' => $reputation,
        ]);
    }

    /**
     * Get admin reputation statistics
     */
    public function adminStats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_users' => User::count(),
            'average_trust_score' => User::avg('trust_score') ?: 0,
            'reputation_distribution' => [
                'bronze' => User::where('reputation_level', 'bronze')->count(),
                'silver' => User::where('reputation_level', 'silver')->count(),
                'gold' => User::where('reputation_level', 'gold')->count(),
                'platinum' => User::where('reputation_level', 'platinum')->count(),
                'diamond' => User::where('reputation_level', 'diamond')->count(),
            ],
            'recent_changes' => User::where('updated_at', '>=', now()->subDays(7))
                ->orderBy('updated_at', 'desc')
                ->limit(10)
                ->get(['id', 'name', 'trust_score', 'reputation_level', 'updated_at'])
        ];

        return response()->json($stats);
    }

    /**
     * Update user reputation (admin only)
     */
    public function updateUserReputation(Request $request, $userId)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'trust_score' => 'required|integer|min:0|max:100',
            'reputation_level' => 'required|in:bronze,silver,gold,platinum,diamond',
            'badges' => 'nullable|string'
        ]);

        $user = User::findOrFail($userId);
        
        $user->update([
            'trust_score' => $request->trust_score,
            'reputation_level' => $request->reputation_level,
            'badges' => $request->badges
        ]);

        return response()->json([
            'message' => 'User reputation updated successfully',
            'user' => $user
        ]);
    }
}
