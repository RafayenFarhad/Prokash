<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmergencyBroadcast;
use App\Models\User;
use App\Models\Notification;
use App\Mail\EmergencyAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmergencyBroadcastController extends Controller
{
    /**
     * Send emergency broadcast to all users
     */
    public function store(Request $request)
    {
        // Only admins can send emergency broadcasts
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'severity' => 'required|in:low,medium,high,critical',
            'target_area' => 'nullable|string|max:255',
            'target_categories' => 'nullable|array',
            'send_email' => 'boolean',
            'send_sms' => 'boolean',
            'send_push' => 'boolean',
        ]);

        // Create the emergency broadcast record
        $broadcast = EmergencyBroadcast::create([
            'admin_id' => $request->user()->id,
            'title' => $request->title,
            'message' => $request->message,
            'severity' => $request->severity,
            'target_area' => $request->target_area,
            'target_categories' => $request->target_categories,
            'send_email' => $request->send_email ?? false,
            'send_sms' => $request->send_sms ?? false,
            'send_push' => $request->send_push ?? true,
        ]);

        // Get target users
        $usersQuery = User::where('is_active', true);
        
        // Filter by area if specified
        if ($request->target_area) {
            $usersQuery->where('location_name', 'LIKE', '%' . $request->target_area . '%');
        }

        $targetUsers = $usersQuery->get();
        $recipientsCount = 0;

        foreach ($targetUsers as $user) {
            // Create in-app notification if send_push is enabled
            if ($request->send_push ?? true) {
                Notification::create([
                    'user_id' => $user->id,
                    'type' => 'emergency_alert',
                    'title' => '🚨 ' . $request->title,
                    'message' => $request->message,
                    'data' => json_encode([
                        'broadcast_id' => $broadcast->id,
                        'severity' => $request->severity,
                        'admin_name' => $request->user()->name,
                    ]),
                ]);
                $recipientsCount++;
            }

            // Send email if enabled and user has email
            if ($request->send_email && $user->email) {
                try {
                    Mail::to($user->email)->send(new EmergencyAlert($broadcast, $user));
                } catch (\Exception $e) {
                    Log::error('Failed to send emergency email to ' . $user->email . ': ' . $e->getMessage());
                }
            }

            // SMS functionality would go here if implemented
            // if ($request->send_sms && $user->phone) {
            //     // Send SMS via SMS service
            // }
        }

        // Update broadcast with recipients count and sent timestamp
        $broadcast->update([
            'recipients_count' => $recipientsCount,
            'sent_at' => now(),
        ]);

        Log::info('Emergency broadcast sent', [
            'broadcast_id' => $broadcast->id,
            'admin_id' => $request->user()->id,
            'title' => $request->title,
            'recipients_count' => $recipientsCount,
            'severity' => $request->severity,
        ]);

        return response()->json([
            'message' => 'Emergency broadcast sent successfully',
            'broadcast' => $broadcast,
            'recipients_count' => $recipientsCount,
        ], 201);
    }

    /**
     * Get all emergency broadcasts (admin only)
     */
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $broadcasts = EmergencyBroadcast::with('admin:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($broadcasts);
    }

    /**
     * Get specific emergency broadcast
     */
    public function show(Request $request, $id)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $broadcast = EmergencyBroadcast::with('admin:id,name')->findOrFail($id);

        return response()->json($broadcast);
    }

    /**
     * Get emergency broadcast statistics
     */
    public function stats(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $stats = [
            'total_broadcasts' => EmergencyBroadcast::count(),
            'broadcasts_this_month' => EmergencyBroadcast::whereMonth('created_at', now()->month)->count(),
            'total_recipients' => EmergencyBroadcast::sum('recipients_count'),
            'by_severity' => EmergencyBroadcast::selectRaw('severity, COUNT(*) as count')
                ->groupBy('severity')
                ->pluck('count', 'severity'),
            'recent_broadcasts' => EmergencyBroadcast::with('admin:id,name')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }
}
