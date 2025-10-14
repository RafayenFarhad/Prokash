import React, { useState, useEffect } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { toast } from 'react-hot-toast';
import api from '../services/api';

interface RegistrationData {
  user_id: number;
  email: string;
  name?: string;
}

const RegistrationVerification: React.FC = () => {
  const [otp, setOtp] = useState('');
  const [loading, setLoading] = useState(false);
  const [resending, setResending] = useState(false);
  const [timeLeft, setTimeLeft] = useState(600); // 10 minutes
  const navigate = useNavigate();
  const location = useLocation();

  // Get registration data from navigation state or URL params
  const registrationData = location.state as RegistrationData;
  const urlParams = new URLSearchParams(location.search);
  const userIdFromUrl = urlParams.get('user_id');
  const emailFromUrl = urlParams.get('email');

  // Use URL params as fallback if navigation state is missing
  const effectiveRegistrationData = registrationData || {
    user_id: userIdFromUrl ? parseInt(userIdFromUrl) : null,
    email: emailFromUrl || '',
  };

  useEffect(() => {
    // Redirect if no registration data
    if (!effectiveRegistrationData?.user_id) {
      toast.error('Invalid verification request. Please register again.');
      navigate('/register');
      return;
    }

    // Countdown timer
    const timer = setInterval(() => {
      setTimeLeft((prev) => {
        if (prev <= 1) {
          clearInterval(timer);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);

    return () => clearInterval(timer);
  }, [registrationData, navigate]);

  const formatTime = (seconds: number) => {
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
  };

  const handleVerifyOTP = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!otp || otp.length !== 6) {
      toast.error('Please enter a valid 6-digit code');
      return;
    }

    setLoading(true);

    try {
      const response = await api.post('/verify-registration', {
        user_id: effectiveRegistrationData.user_id,
        otp: otp,
      });

      // Store authentication token
      localStorage.setItem('token', response.data.token);
      localStorage.setItem('user', JSON.stringify(response.data.user));

      // Update API default headers for future requests
      api.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;

      toast.success('Registration completed successfully!');
      
      // Small delay to ensure token is stored
      setTimeout(() => {
        // Redirect to dashboard or home
        navigate('/dashboard');
      }, 500);
      
    } catch (error: any) {
      console.error('Verification error:', error);
      
      if (error.response?.data?.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error('Verification failed. Please try again.');
      }
    } finally {
      setLoading(false);
    }
  };

  const handleResendOTP = async () => {
    setResending(true);

    try {
      await api.post('/resend-registration-otp', {
        user_id: effectiveRegistrationData.user_id,
      });

      toast.success('New verification code sent to your email');
      setTimeLeft(600); // Reset timer to 10 minutes
      setOtp(''); // Clear current OTP
      
    } catch (error: any) {
      console.error('Resend error:', error);
      
      if (error.response?.data?.message) {
        toast.error(error.response.data.message);
      } else {
        toast.error('Failed to resend code. Please try again.');
      }
    } finally {
      setResending(false);
    }
  };

  const handleOtpChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const value = e.target.value.replace(/\D/g, ''); // Only digits
    if (value.length <= 6) {
      setOtp(value);
    }
  };

  if (!registrationData?.user_id) {
    return null; // Will redirect in useEffect
  }

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center p-4">
      <div className="max-w-md w-full bg-white rounded-2xl shadow-xl p-8">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="mx-auto w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mb-4">
            <svg className="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            </svg>
          </div>
          <h1 className="text-2xl font-bold text-gray-900 mb-2">
            Verify Your Email
          </h1>
          <p className="text-gray-600 text-center mb-6">
            We've sent a 6-digit verification code to{' '}
            <span className="font-medium text-blue-600">{effectiveRegistrationData.email}</span>
          </p>
        </div>

        {/* OTP Form */}
        <form onSubmit={handleVerifyOTP} className="space-y-6">
          <div>
            <label htmlFor="otp" className="block text-sm font-medium text-gray-700 mb-2">
              Verification Code
            </label>
            <input
              type="text"
              id="otp"
              value={otp}
              onChange={handleOtpChange}
              placeholder="Enter 6-digit code"
              className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-2xl font-mono tracking-widest"
              maxLength={6}
              required
            />
          </div>

          {/* Timer */}
          <div className="text-center">
            {timeLeft > 0 ? (
              <p className="text-sm text-gray-600">
                Code expires in: <span className="font-semibold text-red-600">{formatTime(timeLeft)}</span>
              </p>
            ) : (
              <p className="text-sm text-red-600 font-semibold">
                Code has expired. Please request a new one.
              </p>
            )}
          </div>

          {/* Verify Button */}
          <button
            type="submit"
            disabled={loading || otp.length !== 6}
            className="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {loading ? (
              <div className="flex items-center justify-center">
                <svg className="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                  <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Verifying...
              </div>
            ) : (
              'Verify & Complete Registration'
            )}
          </button>
        </form>

        {/* Resend Section */}
        <div className="mt-6 text-center">
          <p className="text-sm text-gray-600 mb-3">
            Didn't receive the code?
          </p>
          <button
            onClick={handleResendOTP}
            disabled={resending || timeLeft > 540} // Allow resend after 1 minute
            className="text-blue-600 hover:text-blue-800 font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {resending ? 'Sending...' : 'Resend Code'}
          </button>
        </div>

        {/* Help Text */}
        <div className="mt-8 p-4 bg-gray-50 rounded-lg">
          <h3 className="text-sm font-semibold text-gray-900 mb-2">
            📧 Check Your Email
          </h3>
          <ul className="text-xs text-gray-600 space-y-1">
            <li>• Check your inbox and spam folder</li>
            <li>• The code is valid for 10 minutes</li>
            <li>• Enter the 6-digit code exactly as received</li>
          </ul>
        </div>

        {/* Back to Registration */}
        <div className="mt-6 text-center">
          <button
            onClick={() => navigate('/register')}
            className="text-sm text-gray-500 hover:text-gray-700"
          >
            ← Back to Registration
          </button>
        </div>
      </div>
    </div>
  );
};

export default RegistrationVerification;
