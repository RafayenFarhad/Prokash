<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\VerificationController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PostUpdateController;
use App\Http\Controllers\Api\ReputationController;
use App\Http\Controllers\Api\CommunityResponseController;
use App\Http\Controllers\Api\EmergencyBroadcastController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\Api\VoteController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-registration', [AuthController::class, 'verifyRegistration']);
Route::post('/resend-registration-otp', [AuthController::class, 'resendRegistrationOTP']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/email/send-otp', [EmailVerificationController::class, 'sendOTP']);
Route::post('/email/verify-otp', [EmailVerificationController::class, 'verifyOTP']);
Route::post('/email/resend-otp', [EmailVerificationController::class, 'resendOTP']);

Route::get('/stats', [AdminController::class, 'publicStats']);
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{id}', [PostController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/tags', [TagController::class, 'index']);
Route::get('/tags/{id}', [TagController::class, 'show']);
Route::get('/posts/{id}/updates', [PostUpdateController::class, 'index']);
Route::get('/reputation/leaderboard', [ReputationController::class, 'leaderboard']);
Route::get('/reputation/{userId}', [ReputationController::class, 'show']);
Route::get('/posts/{id}/responses', [CommunityResponseController::class, 'index']);
Route::post('/ai/categorize', [AIController::class, 'categorizePost']);
Route::post('/ai/detect-fake-news', [AIController::class, 'detectFakeNews']);
Route::post('/ai/analyze-sentiment', [AIController::class, 'analyzeSentiment']);
Route::post('/ai/suggestions', [AIController::class, 'getPostSuggestions']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);

    Route::get('/posts/{postId}/comments', [CommentController::class, 'index']);
    Route::post('/posts/{postId}/comments', [CommentController::class, 'store']);
    Route::put('/posts/{postId}/comments/{id}', [CommentController::class, 'update']);
    
    Route::post('/posts/{postId}/like', [LikeController::class, 'toggle']);
    Route::get('/posts/{postId}/like/check', [LikeController::class, 'check']);

    Route::post('/posts/{id}/verify', [VerificationController::class, 'toggle']);
    Route::get('/posts/{id}/verify/check', [VerificationController::class, 'check']);

    Route::post('/posts/{id}/report', [ReportController::class, 'store']);
    
    Route::post('/posts/{id}/vote', [VoteController::class, 'vote']);
    Route::get('/posts/{id}/user-vote', [VoteController::class, 'getUserVote']);

    // Post Updates routes
    Route::post('/posts/{id}/updates', [PostUpdateController::class, 'store']);

    // Reputation routes
    Route::get('/reputation', [ReputationController::class, 'show']);
    Route::put('/reputation/{userId}', [ReputationController::class, 'update']);

    // Community Response routes
    Route::post('/posts/{id}/responses', [CommunityResponseController::class, 'store']);
    Route::put('/posts/{postId}/responses/{responseId}', [CommunityResponseController::class, 'update']);
    Route::get('/community/my-offers', [CommunityResponseController::class, 'myOffers']);

    // AI Analysis routes
    Route::get('/ai/analyze/{postId}', [AIController::class, 'analyzePost']);

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);

    // Admin routes (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/admin/stats', [AdminController::class, 'stats']);
        Route::get('/admin/users', [AdminController::class, 'users']);
        Route::put('/admin/users/{userId}/role', [AdminController::class, 'updateUserRole']);
        Route::put('/admin/users/{userId}/toggle-status', [AdminController::class, 'toggleUserStatus']);
        Route::delete('/admin/users/{userId}', [AdminController::class, 'deleteUser']);
        // AI Auto-generation (Admin only)
        Route::post('/admin/ai/generate-weather-alerts', [AIController::class, 'generateWeatherAlerts']);
        Route::post('/admin/ai/generate-traffic-alerts', [AIController::class, 'generateTrafficAlerts']);
        Route::get('/admin/ai/batch-analyze', [AIController::class, 'batchAnalyze']);

        // Analytics Dashboard (Admin only)
        Route::get('/admin/analytics/dashboard', [AnalyticsController::class, 'dashboard']);
        Route::get('/admin/analytics/community-stats', [AnalyticsController::class, 'communityStats']);
        Route::get('/admin/posts', [AdminController::class, 'posts']);
        Route::delete('/admin/posts/{postId}', [AdminController::class, 'deletePost']);
        
        // Admin - Reports Management
        Route::get('/admin/reports', [ReportController::class, 'index']);
        Route::put('/admin/reports/{reportId}/status', [ReportController::class, 'updateStatus']);
        
        // Emergency Broadcast routes
        Route::post('/admin/emergency-broadcast', [EmergencyBroadcastController::class, 'store']);
        Route::get('/admin/emergency-broadcasts', [EmergencyBroadcastController::class, 'index']);
        Route::get('/admin/emergency-broadcasts/{id}', [EmergencyBroadcastController::class, 'show']);
        Route::get('/admin/emergency-broadcast-stats', [EmergencyBroadcastController::class, 'stats']);
        
        // Admin Reputation Management
        Route::get('/admin/reputation-stats', [ReputationController::class, 'adminStats']);
        Route::put('/admin/users/{id}/reputation', [ReputationController::class, 'updateUserReputation']);
        
        // Admin Notification Management
        Route::get('/admin/notifications', [NotificationController::class, 'adminIndex']);
        Route::get('/admin/notification-stats', [NotificationController::class, 'adminStats']);
        
        // Admin Post Verification
        Route::post('/admin/posts/{postId}/verify', [AdminController::class, 'togglePostVerification']);
        Route::get('/admin/verification-stats', [AdminController::class, 'verificationStats']);
    });
});
