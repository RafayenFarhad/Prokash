<?php

namespace App\Services;

use App\Models\User;
use App\Models\Post;
use App\Models\Notification;
use App\Models\PostReport;
use App\Models\PostVote;
use App\Models\Comment;

class ReputationService
{
    public function calculateTrustScore(User $user)
    {
        $score = 50; // Base score for all users
        
        $totalPosts = $user->posts()->count();
        $verifiedPosts = $user->posts()->where('is_verified', true)->count();
        $emergencyPosts = $user->posts()->where('priority', 'emergency')->count();
        $highPriorityPosts = $user->posts()->where('priority', 'high')->count();
        
        $totalUpvotes = PostVote::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('vote_type', 'upvote')->count();
        $totalDownvotes = PostVote::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('vote_type', 'downvote')->count();
        
        $commentsCount = Comment::where('user_id', $user->id)->count();
        $helpfulResponses = 0;
        try {
            $helpfulResponses = $user->communityResponses()->where('status', 'accepted')->count();
        } catch (\Exception $e) {
            // Handle case where relationship doesn't exist or has issues
            $helpfulResponses = 0;
        }
        
        $spamReports = PostReport::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('reason', 'spam')->count();
        $totalReports = PostReport::whereIn('post_id', $user->posts()->pluck('id'))->count();
        
        $accountAgeDays = $user->created_at->diffInDays(now());
        $accountAgeBonus = min(20, $accountAgeDays / 30); // Max 20 points for 600+ days
        
        $score += ($verifiedPosts * 15);           // +15 per verified post
        $score += ($emergencyPosts * 10);          // +10 per emergency post
        $score += ($highPriorityPosts * 5);        // +5 per high priority post
        $score += ($totalPosts * 2);               // +2 per post
        $score += ($totalUpvotes * 3);             // +3 per upvote received
        $score += ($commentsCount * 1);            // +1 per comment
        $score += ($helpfulResponses * 20);        // +20 per accepted help response
        $score += $accountAgeBonus;                // Account age bonus
        
        $score -= ($totalDownvotes * 5);           // -5 per downvote received
        $score -= ($spamReports * 15);             // -15 per spam report
        $score -= ($totalReports * 5);             // -5 per any report
        
        if ($user->email_verified_at) {
            $score += 10;
        }
        
        return max(0, min(100, $score)); // Keep score between 0-100
    }

    public function updateReputation(User $user)
    {
        $oldScore = $user->trust_score;
        $oldLevel = $user->reputation_level;
        $oldBadges = json_decode($user->badges ?? '[]', true);
        
        $newScore = $this->calculateTrustScore($user);
        $newLevel = $this->getReputationLevel($newScore);
        $newBadges = $this->calculateBadges($user);

        $user->update([
            'trust_score' => $newScore,
            'reputation_level' => $newLevel,
            'badges' => json_encode($newBadges),
        ]);

        $this->sendReputationNotifications($user, $oldScore, $newScore, $oldLevel, $newLevel, $oldBadges, $newBadges);

        return [
            'trust_score' => $newScore,
            'reputation_level' => $newLevel,
            'badges' => $newBadges,
        ];
    }

    private function sendReputationNotifications(User $user, $oldScore, $newScore, $oldLevel, $newLevel, $oldBadges, $newBadges)
    {
        if ($newScore !== $oldScore) {
            $scoreDiff = $newScore - $oldScore;
            $isIncrease = $scoreDiff > 0;
            
            Notification::create([
                'user_id' => $user->id,
                'type' => $isIncrease ? 'score_increased' : 'score_decreased',
                'title' => $isIncrease ? '📈 Trust Score Increased!' : '📉 Trust Score Decreased',
                'message' => "Your trust score changed by " . ($isIncrease ? '+' : '') . "{$scoreDiff} points. Current score: {$newScore}/100",
                'data' => [
                    'old_score' => $oldScore,
                    'new_score' => $newScore,
                    'change' => $scoreDiff
                ]
            ]);
        }

        if ($newLevel !== $oldLevel) {
            $isUpgrade = $this->isLevelUpgrade($oldLevel, $newLevel);
            
            Notification::create([
                'user_id' => $user->id,
                'type' => $isUpgrade ? 'level_upgraded' : 'level_downgraded',
                'title' => $isUpgrade ? '🎉 Level Up!' : '📉 Level Down',
                'message' => "Your reputation level changed from {$oldLevel} to {$newLevel}!",
                'data' => [
                    'old_level' => $oldLevel,
                    'new_level' => $newLevel,
                    'trust_score' => $newScore
                ]
            ]);
        }

        $newlyEarnedBadges = array_diff($newBadges, $oldBadges);
        foreach ($newlyEarnedBadges as $badge) {
            $badgeInfo = $this->getBadgeInfo($badge);
            
            Notification::create([
                'user_id' => $user->id,
                'type' => 'badge_earned',
                'title' => '🏅 New Badge Earned!',
                'message' => "Congratulations! You've earned the '{$badgeInfo['name']}' badge: {$badgeInfo['description']}",
                'data' => [
                    'badge' => $badge,
                    'badge_info' => $badgeInfo
                ]
            ]);
        }
    }

    private function getReputationLevel($score)
    {
        if ($score >= 90) return 'diamond';
        if ($score >= 75) return 'platinum';
        if ($score >= 60) return 'gold';
        if ($score >= 40) return 'silver';
        return 'bronze';
    }

    private function calculateBadges(User $user)
    {
        $badges = [];

        // Basic badges
        if ($user->email_verified_at) {
            $badges[] = 'verified_user';
        }

        $totalPosts = $user->posts()->count();
        $verifiedPosts = $user->posts()->where('is_verified', true)->count();
        $emergencyPosts = $user->posts()->where('priority', 'emergency')->count();
        
        if ($totalPosts >= 10) $badges[] = 'contributor';
        if ($totalPosts >= 50) $badges[] = 'active_contributor';
        if ($totalPosts >= 100) $badges[] = 'super_contributor';
        
        if ($verifiedPosts >= 5) $badges[] = 'trusted_reporter';
        if ($verifiedPosts >= 20) $badges[] = 'expert_reporter';
        
        if ($emergencyPosts >= 3) $badges[] = 'first_responder';
        if ($emergencyPosts >= 10) $badges[] = 'emergency_expert';

        $totalUpvotes = PostVote::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('vote_type', 'upvote')->count();
        
        if ($totalUpvotes >= 25) $badges[] = 'community_favorite';
        if ($totalUpvotes >= 100) $badges[] = 'highly_valued';

        // Engagement badges
        $commentsCount = Comment::where('user_id', $user->id)->count();
        if ($commentsCount >= 50) $badges[] = 'conversationalist';
        if ($commentsCount >= 200) $badges[] = 'discussion_leader';

        // Time-based badges
        $accountAgeDays = $user->created_at->diffInDays(now());
        if ($accountAgeDays >= 30) $badges[] = 'member';
        if ($accountAgeDays >= 180) $badges[] = 'veteran';
        if ($accountAgeDays >= 365) $badges[] = 'legend';

        // Quality badges
        if ($user->trust_score >= 80) $badges[] = 'trusted_member';
        if ($user->trust_score >= 95) $badges[] = 'exemplary_citizen';

        $helpfulResponses = 0;
        try {
            $helpfulResponses = $user->communityResponses()->where('status', 'accepted')->count();
        } catch (\Exception $e) {
            $helpfulResponses = 0;
        }
        if ($helpfulResponses >= 5) $badges[] = 'community_helper';
        if ($helpfulResponses >= 20) $badges[] = 'guardian_angel';

        $spamReports = PostReport::whereIn('post_id', $user->posts()->pluck('id'))
            ->where('reason', 'spam')->count();
        if ($totalPosts >= 10 && $spamReports === 0) {
            $badges[] = 'clean_record';
        }

        return array_unique($badges);
    }

    public function getBadgeInfo($badge)
    {
        $badges = [
            'verified_user' => ['name' => 'Verified User', 'icon' => '✅', 'description' => 'Email verified', 'color' => 'text-green-600'],
            'member' => ['name' => 'Member', 'icon' => '👤', 'description' => '30+ days active', 'color' => 'text-blue-500'],
            
            'contributor' => ['name' => 'Contributor', 'icon' => '📝', 'description' => '10+ posts', 'color' => 'text-blue-600'],
            'active_contributor' => ['name' => 'Active Contributor', 'icon' => '📚', 'description' => '50+ posts', 'color' => 'text-purple-600'],
            'super_contributor' => ['name' => 'Super Contributor', 'icon' => '🌟', 'description' => '100+ posts', 'color' => 'text-yellow-500'],
            
            'trusted_reporter' => ['name' => 'Trusted Reporter', 'icon' => '🛡️', 'description' => '5+ verified posts', 'color' => 'text-blue-600'],
            'expert_reporter' => ['name' => 'Expert Reporter', 'icon' => '🎯', 'description' => '20+ verified posts', 'color' => 'text-indigo-600'],
            
            'first_responder' => ['name' => 'First Responder', 'icon' => '🚨', 'description' => '3+ emergency alerts', 'color' => 'text-red-600'],
            'emergency_expert' => ['name' => 'Emergency Expert', 'icon' => '🚑', 'description' => '10+ emergency alerts', 'color' => 'text-red-700'],
            
            'community_favorite' => ['name' => 'Community Favorite', 'icon' => '❤️', 'description' => '25+ upvotes received', 'color' => 'text-pink-600'],
            'highly_valued' => ['name' => 'Highly Valued', 'icon' => '💎', 'description' => '100+ upvotes received', 'color' => 'text-purple-700'],
            'community_helper' => ['name' => 'Community Helper', 'icon' => '🤝', 'description' => '5+ accepted help responses', 'color' => 'text-green-600'],
            'guardian_angel' => ['name' => 'Guardian Angel', 'icon' => '👼', 'description' => '20+ accepted help responses', 'color' => 'text-yellow-400'],
            
            'conversationalist' => ['name' => 'Conversationalist', 'icon' => '💬', 'description' => '50+ comments', 'color' => 'text-teal-600'],
            'discussion_leader' => ['name' => 'Discussion Leader', 'icon' => '🗣️', 'description' => '200+ comments', 'color' => 'text-orange-600'],
            
            'veteran' => ['name' => 'Veteran', 'icon' => '🏆', 'description' => '6+ months member', 'color' => 'text-yellow-600'],
            'legend' => ['name' => 'Legend', 'icon' => '👑', 'description' => '1+ year member', 'color' => 'text-yellow-500'],
            
            'trusted_member' => ['name' => 'Trusted Member', 'icon' => '🌟', 'description' => '80+ trust score', 'color' => 'text-blue-500'],
            'exemplary_citizen' => ['name' => 'Exemplary Citizen', 'icon' => '🏅', 'description' => '95+ trust score', 'color' => 'text-gold-500'],
            'clean_record' => ['name' => 'Clean Record', 'icon' => '🧹', 'description' => 'No spam reports', 'color' => 'text-green-500'],
        ];

        return $badges[$badge] ?? [
            'name' => ucfirst(str_replace('_', ' ', $badge)),
            'icon' => '🏅',
            'description' => 'Achievement unlocked',
            'color' => 'text-gray-600'
        ];
    }

    public function getLevelInfo($level)
    {
        $levels = [
            'bronze' => [
                'name' => 'Bronze',
                'icon' => '🥉',
                'color' => 'text-amber-600',
                'min_score' => 0,
                'max_score' => 39
            ],
            'silver' => [
                'name' => 'Silver',
                'icon' => '🥈',
                'color' => 'text-gray-500',
                'min_score' => 40,
                'max_score' => 59
            ],
            'gold' => [
                'name' => 'Gold',
                'icon' => '🥇',
                'color' => 'text-yellow-500',
                'min_score' => 60,
                'max_score' => 74
            ],
            'platinum' => [
                'name' => 'Platinum',
                'icon' => '💎',
                'color' => 'text-blue-500',
                'min_score' => 75,
                'max_score' => 89
            ],
            'diamond' => [
                'name' => 'Diamond',
                'icon' => '💠',
                'color' => 'text-purple-500',
                'min_score' => 90,
                'max_score' => 100
            ],
        ];

        return $levels[$level] ?? $levels['bronze'];
    }

    private function isLevelUpgrade(string $oldLevel, string $newLevel): bool
    {
        $levels = ['bronze' => 1, 'silver' => 2, 'gold' => 3, 'platinum' => 4, 'diamond' => 5];
        return ($levels[$newLevel] ?? 1) > ($levels[$oldLevel] ?? 1);
    }

    public function triggerReputationUpdate(User $user, string $activity, array $data = [])
    {
        $this->updateReputation($user);
        
        \Log::info("Reputation updated for user {$user->id} due to activity: {$activity}", $data);
    }
}
