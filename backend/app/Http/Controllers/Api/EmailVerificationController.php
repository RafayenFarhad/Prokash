<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class EmailVerificationController extends Controller
{
    /**
     * Send OTP to email
     */
    public function sendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Generate 6-digit OTP
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10); // OTP expires in 10 minutes
        
        // Find or create user
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            $user = User::create([
                'email' => $request->email,
                'name' => 'User' . rand(1000, 9999),
                'password' => Hash::make(str()->random(16)),
                'email_verification_code' => $otp,
                'email_verification_code_expires_at' => $expiresAt,
                'email_verified_at' => null,
            ]);
        } else {
            $user->update([
                'email_verification_code' => $otp,
                'email_verification_code_expires_at' => $expiresAt,
            ]);
        }

        // Send email with OTP
        try {
            Mail::to($request->email)->send(new VerificationOTP($otp, $user->name));

            return response()->json([
                'message' => 'OTP sent to your email successfully',
                'email' => $request->email,
                'expires_in' => '10 minutes',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send OTP: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify OTP and login
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)
                    ->where('email_verification_code', $request->otp)
                    ->where('email_verification_code_expires_at', '>', now())
                    ->first();

        if (!$user) {
            return response()->json([
                'message' => 'Invalid or expired OTP',
            ], 401);
        }

        // Mark email as verified
        $user->update([
            'email_verified_at' => now(),
            'email_verification_code' => null,
            'email_verification_code_expires_at' => null,
        ]);

        // Create token
        $token = $user->createToken('email-auth')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Email verified successfully',
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        $user->update([
            'email_verification_code' => $otp,
            'email_verification_code_expires_at' => $expiresAt,
        ]);

        // Send email
        try {
            Mail::to($request->email)->send(new VerificationOTP($otp, $user->name));

            return response()->json([
                'message' => 'New OTP sent successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to resend OTP: ' . $e->getMessage(),
            ], 500);
        }
    }
}
