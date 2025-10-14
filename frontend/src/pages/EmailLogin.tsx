import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';
import { Mail, Shield, Clock, RefreshCw } from 'lucide-react';

export default function EmailLogin() {
  const [email, setEmail] = useState('');
  const [otp, setOTP] = useState('');
  const [step, setStep] = useState<'email' | 'otp'>('email');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');
  const [countdown, setCountdown] = useState(0);
  const navigate = useNavigate();
  const { login } = useAuth();

  const handleSendOTP = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      await api.post('/email/send-otp', { email });
      setStep('otp');
      setCountdown(60); // 60 second countdown for resend
      
      // Start countdown timer
      const timer = setInterval(() => {
        setCountdown((prev) => {
          if (prev <= 1) {
            clearInterval(timer);
            return 0;
          }
          return prev - 1;
        });
      }, 1000);
      
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to send OTP');
    } finally {
      setLoading(false);
    }
  };

  const handleVerifyOTP = async (e: React.FormEvent) => {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const response = await api.post('/email/verify-otp', { email, otp });
      login(response.data.user, response.data.token);
      navigate('/posts');
    } catch (err: any) {
      setError(err.response?.data?.message || 'Invalid or expired OTP');
    } finally {
      setLoading(false);
    }
  };

  const handleResendOTP = async () => {
    if (countdown > 0) return;
    
    setError('');
    setLoading(true);

    try {
      await api.post('/email/resend-otp', { email });
      setCountdown(60);
      
      // Start countdown timer
      const timer = setInterval(() => {
        setCountdown((prev) => {
          if (prev <= 1) {
            clearInterval(timer);
            return 0;
          }
          return prev - 1;
        });
      }, 1000);
      
    } catch (err: any) {
      setError(err.response?.data?.message || 'Failed to resend OTP');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center py-12 px-4">
      <div className="max-w-md w-full">
        {/* Header */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4">
            <Mail size={32} className="text-white" />
          </div>
          <h2 className="text-3xl font-bold text-gray-900">
            {step === 'email' ? 'Email Login' : 'Enter OTP'}
          </h2>
          <p className="text-gray-600 mt-2">
            {step === 'email' 
              ? 'Sign in with your email address' 
              : 'We sent a verification code to your email'}
          </p>
        </div>

        {/* Card */}
        <div className="bg-white rounded-2xl shadow-xl p-8">
          {error && (
            <div className="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg mb-6 flex items-start">
              <Shield size={20} className="mr-2 mt-0.5 flex-shrink-0" />
              <span>{error}</span>
            </div>
          )}

          {step === 'email' ? (
            <form onSubmit={handleSendOTP}>
              <div className="mb-6">
                <label className="block text-gray-700 font-semibold mb-2">
                  Email Address
                </label>
                <div className="relative">
                  <Mail size={20} className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                  <input
                    type="email"
                    className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="your@email.com"
                    value={email}
                    onChange={(e) => setEmail(e.target.value)}
                    required
                  />
                </div>
                <p className="text-sm text-gray-500 mt-2">
                  We'll send a 6-digit verification code to this email
                </p>
              </div>
              <button
                type="submit"
                disabled={loading}
                className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 transition"
              >
                {loading ? 'Sending...' : 'Send Verification Code'}
              </button>
            </form>
          ) : (
            <form onSubmit={handleVerifyOTP}>
              <div className="mb-6">
                <label className="block text-gray-700 font-semibold mb-2">
                  Enter 6-Digit Code
                </label>
                <input
                  type="text"
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-2xl tracking-widest font-mono"
                  placeholder="000000"
                  maxLength={6}
                  value={otp}
                  onChange={(e) => setOTP(e.target.value.replace(/\D/g, ''))}
                  required
                  autoFocus
                />
                <p className="text-sm text-gray-500 mt-2 text-center">
                  Code sent to {email}
                </p>
                
                {/* Expiry Warning */}
                <div className="bg-yellow-50 border border-yellow-200 p-3 rounded-lg mt-3 flex items-center">
                  <Clock size={16} className="mr-2 text-yellow-600" />
                  <span className="text-sm text-yellow-700">
                    Code expires in 10 minutes
                  </span>
                </div>
              </div>
              
              <button
                type="submit"
                disabled={loading || otp.length !== 6}
                className="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 disabled:opacity-50 transition mb-3"
              >
                {loading ? 'Verifying...' : 'Verify Code'}
              </button>
              
              {/* Resend Button */}
              <button
                type="button"
                onClick={handleResendOTP}
                disabled={countdown > 0 || loading}
                className="w-full bg-gray-100 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-200 disabled:opacity-50 transition mb-3 flex items-center justify-center"
              >
                <RefreshCw size={16} className="mr-2" />
                {countdown > 0 ? `Resend in ${countdown}s` : 'Resend Code'}
              </button>
              
              <button
                type="button"
                onClick={() => {
                  setStep('email');
                  setOTP('');
                  setError('');
                  setCountdown(0);
                }}
                className="w-full bg-gray-200 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-300 transition"
              >
                Change Email Address
              </button>
            </form>
          )}

          {/* Divider */}
          <div className="relative my-6">
            <div className="absolute inset-0 flex items-center">
              <div className="w-full border-t border-gray-300"></div>
            </div>
            <div className="relative flex justify-center text-sm">
              <span className="px-2 bg-white text-gray-500">Or</span>
            </div>
          </div>

          {/* Alternative Login */}
          <Link
            to="/login"
            className="block text-center text-blue-600 hover:text-blue-700 font-semibold"
          >
            Login with Email & Password
          </Link>
        </div>

        {/* Footer */}
        <p className="text-center text-gray-600 mt-6">
          Don't have an account?{' '}
          <Link to="/register" className="text-blue-600 hover:text-blue-700 font-semibold">
            Sign up
          </Link>
        </p>

        {/* Info Box */}
        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
          <h3 className="font-semibold text-blue-900 mb-2">📧 Email Verification Benefits:</h3>
          <ul className="text-sm text-blue-800 space-y-1">
            <li>• Secure login without passwords</li>
            <li>• Receive important emergency alerts</li>
            <li>• Verify your identity in the community</li>
            <li>• Free and easy to use</li>
          </ul>
        </div>
      </div>
    </div>
  );
}
