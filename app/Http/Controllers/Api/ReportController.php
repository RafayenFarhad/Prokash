<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostReport;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'reason' => 'required|in:spam,false_info,inappropriate,harassment,other',
            'description' => 'nullable|string|max:500',
        ]);

        $post = Post::findOrFail($postId);

        $existing = PostReport::where('user_id', $request->user()->id)
            ->where('post_id', $postId)
            ->first();

        if ($existing) {
            return response()->json(['message' => 'You have already reported this post'], 400);
        }

        $report = PostReport::create([
            'user_id' => $request->user()->id,
            'post_id' => $postId,
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        $this->sendReportNotifications($post, $report, $request->user());

        return response()->json(['message' => 'Post reported successfully']);
    }

    public function index(Request $request)
    {
        $reports = PostReport::with(['user', 'post'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->paginate(20);

        return response()->json($reports);
    }

    public function updateStatus(Request $request, $reportId)
    {
        $request->validate([
            'status' => 'required|in:pending,reviewed,resolved',
        ]);
        $report->update(['status' => $request->status]);

        return response()->json(['message' => 'Report status updated']);
    }

    public function report(Request $request, $postId)
    {
        return $this->store($request, $postId);
    }

    private function sendReportNotifications(Post $post, PostReport $report, User $reporter)
    {
        // Notify all admins
        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'post_reported',
                'title' => '🚨 Post Reported',
                'message' => "A post has been reported for {$report->reason}. Post: \"{$post->title}\" by {$post->user->name}",
                'data' => [
                    'post_id' => $post->id,
                    'report_id' => $report->id,
                    'reporter_id' => $reporter->id,
                    'reason' => $report->reason,
                    'post_title' => $post->title,
                    'post_author' => $post->user->name
                ]
            ]);
        }

        if ($post->user_id !== $reporter->id) {
            Notification::create([
                'user_id' => $post->user_id,
                'type' => 'post_reported_owner',
                'title' => '⚠️ Your Post Was Reported',
                'message' => "Your post \"{$post->title}\" has been reported for {$report->reason}. Our team will review it shortly.",
                'data' => [
                    'post_id' => $post->id,
                    'report_id' => $report->id,
                    'reason' => $report->reason,
                    'post_title' => $post->title
                ]
            ]);
        }

        if ($report->reason === 'spam') {
            $this->handleSpamReport($post, $report);
        }
    }

    private function handleSpamReport(Post $post, PostReport $report)
    {
        $spamReports = PostReport::where('post_id', $post->id)
            ->where('reason', 'spam')
            ->count();

        if ($spamReports >= 3) {
            $post->update(['status' => 'flagged']);
            
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'high_spam_reports',
                    'title' => '🚨 HIGH PRIORITY: Multiple Spam Reports',
                    'message' => "Post \"{$post->title}\" has received {$spamReports} spam reports and has been flagged for immediate review.",
                    'data' => [
                        'post_id' => $post->id,
                        'spam_count' => $spamReports,
                        'post_title' => $post->title,
                        'post_author' => $post->user->name
                    ]
                ]);
            }

            $this->reduceUserTrustScore($post->user, 5);
        }
    }

    private function reduceUserTrustScore(User $user, int $points)
    {
        $newScore = max(0, $user->trust_score - $points);
        $user->update(['trust_score' => $newScore]);

        $this->updateReputationLevel($user);

        Notification::create([
            'user_id' => $user->id,
            'type' => 'trust_score_reduced',
            'title' => '📉 Trust Score Reduced',
            'message' => "Your trust score has been reduced by {$points} points due to spam reports. Current score: {$newScore}",
            'data' => [
                'points_reduced' => $points,
                'new_score' => $newScore,
                'old_score' => $user->trust_score + $points
            ]
        ]);
    }

    private function updateReputationLevel(User $user)
    {
        $oldLevel = $user->reputation_level;
        
        if ($user->trust_score >= 90) {
            $newLevel = 'diamond';
        } elseif ($user->trust_score >= 75) {
            $newLevel = 'platinum';
        } elseif ($user->trust_score >= 60) {
            $newLevel = 'gold';
        } elseif ($user->trust_score >= 40) {
            $newLevel = 'silver';
        } else {
            $newLevel = 'bronze';
        }

        if ($oldLevel !== $newLevel) {
            $user->update(['reputation_level' => $newLevel]);
            
            $isUpgrade = $this->isLevelUpgrade($oldLevel, $newLevel);
            Notification::create([
                'user_id' => $user->id,
                'type' => $isUpgrade ? 'reputation_upgraded' : 'reputation_downgraded',
                'title' => $isUpgrade ? '🎉 Reputation Upgraded!' : '📉 Reputation Downgraded',
                'message' => "Your reputation level has changed from {$oldLevel} to {$newLevel}.",
                'data' => [
                    'old_level' => $oldLevel,
                    'new_level' => $newLevel,
                    'trust_score' => $user->trust_score
                ]
            ]);
        }
    }

    private function isLevelUpgrade(string $oldLevel, string $newLevel): bool
    {
        $levels = ['bronze' => 1, 'silver' => 2, 'gold' => 3, 'platinum' => 4, 'diamond' => 5];
        return $levels[$newLevel] > $levels[$oldLevel];
    }
}
