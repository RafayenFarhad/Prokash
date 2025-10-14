<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\VerificationOTP;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiry = now()->addMinutes(10); // OTP expires in 10 minutes

        // Store user data temporarily with OTP (user not created yet)
        $tempUser = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'email_verification_code' => $otp,
            'email_verification_code_expires_at' => $otpExpiry,
            'email_verified_at' => null, // Not verified yet
            'role' => 'user',
            'is_active' => false, // Account inactive until verified
        ]);

        // Send OTP email
        try {
            Mail::to($request->email)->send(new VerificationOTP($otp, $request->name));
            
            return response()->json([
                'message' => 'Registration initiated. Please check your email for verification code.',
                'email' => $request->email,
                'user_id' => $tempUser->id,
                'expires_in' => '10 minutes',
            ], 201);
            
        } catch (\Exception $e) {
            // If email fails, delete the temporary user
            $tempUser->delete();
            
            return response()->json([
                'message' => 'Failed to send verification email. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify registration OTP and activate account
     */
    public function verifyRegistration(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::find($request->user_id);

        // Log verification attempt
        \Log::info('OTP Verification Attempt', [
            'user_id' => $request->user_id,
            'provided_otp' => $request->otp,
            'stored_otp' => $user ? $user->email_verification_code : 'USER_NOT_FOUND',
            'user_email' => $user ? $user->email : 'N/A',
            'already_verified' => $user ? ($user->email_verified_at ? 'YES' : 'NO') : 'N/A'
        ]);

        // Check if user exists and is not already verified
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email already verified. You can login now.',
                'already_verified' => true,
            ], 200);
        }

        // Check if OTP is correct (with detailed debugging)
        $providedOtp = trim($request->otp);
        $storedOtp = trim($user->email_verification_code);
        
        \Log::info('OTP Comparison Details', [
            'user_id' => $user->id,
            'provided_otp' => $providedOtp,
            'provided_length' => strlen($providedOtp),
            'provided_type' => gettype($providedOtp),
            'stored_otp' => $storedOtp,
            'stored_length' => strlen($storedOtp),
            'stored_type' => gettype($storedOtp),
            'strict_match' => $storedOtp === $providedOtp ? 'YES' : 'NO',
            'loose_match' => $storedOtp == $providedOtp ? 'YES' : 'NO'
        ]);
        
        if ($storedOtp !== $providedOtp) {
            \Log::warning('Invalid OTP provided', [
                'user_id' => $user->id,
                'provided_otp' => $providedOtp,
                'expected_otp' => $storedOtp,
                'comparison_failed' => 'strict_comparison'
            ]);
            
            return response()->json([
                'message' => 'Invalid verification code.',
                'debug' => [
                    'provided' => $providedOtp,
                    'expected' => $storedOtp,
                    'lengths_match' => strlen($providedOtp) === strlen($storedOtp)
                ]
            ], 400);
        }

        // Check if OTP is expired
        if (now()->isAfter($user->email_verification_code_expires_at)) {
            \Log::warning('Expired OTP used', [
                'user_id' => $user->id,
                'expired_at' => $user->email_verification_code_expires_at,
                'current_time' => now()
            ]);
            
            return response()->json([
                'message' => 'Verification code has expired. Please request a new one.',
            ], 400);
        }

        // Use database transaction to ensure atomicity
        try {
            \DB::transaction(function () use ($user) {
                // Activate the user account with explicit field updates
                $updateResult = $user->update([
                    'email_verified_at' => now(),
                    'is_active' => true,
                    'email_verification_code' => null,
                    'email_verification_code_expires_at' => null,
                ]);

                \Log::info('Database update result', [
                    'user_id' => $user->id,
                    'update_success' => $updateResult ? 'YES' : 'NO'
                ]);
            });

            // Force refresh from database
            $user = $user->fresh();

            // Verify the update was successful
            if (!$user->email_verified_at) {
                \Log::error('Verification update failed - email_verified_at still null', [
                    'user_id' => $user->id,
                    'email' => $user->email
                ]);
                
                return response()->json([
                    'message' => 'Verification failed. Please try again.',
                ], 500);
            }

            // Create authentication token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Log successful verification
            \Log::info('User verified successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'is_active' => $user->is_active ? 'YES' : 'NO',
                'token_created' => 'YES'
            ]);

            return response()->json([
                'message' => 'Registration completed successfully!',
                'user' => $user,
                'token' => $token,
                'verified_at' => $user->email_verified_at,
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Verification transaction failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Verification failed due to system error. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Resend registration OTP
     */
    public function resendRegistrationOTP(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // Check if user exists and is not already verified
        if (!$user || $user->email_verified_at) {
            return response()->json([
                'message' => 'Invalid request.',
            ], 400);
        }

        // Generate new OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $otpExpiry = now()->addMinutes(10);

        $user->update([
            'email_verification_code' => $otp,
            'email_verification_code_expires_at' => $otpExpiry,
        ]);

        // Send new OTP email
        try {
            Mail::to($user->email)->send(new VerificationOTP($otp, $user->name));
            
            return response()->json([
                'message' => 'New verification code sent to your email.',
                'expires_in' => '10 minutes',
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to send verification email. Please try again.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if email is verified
        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Please verify your email address first.',
                'user_id' => $user->id,
                'email' => $user->email,
                'requires_verification' => true,
            ], 403);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Your account has been disabled.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful',
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * Get authenticated user
     */
    public function me(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            
            return response()->json([
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch user data'], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'user' => $user,
            'message' => 'Profile updated successfully',
        ]);
    }
}
