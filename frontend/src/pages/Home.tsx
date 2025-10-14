import { Link } from 'react-router-dom';
import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import api from '../services/api';
import { 
  ArrowRight, 
  MapPin, 
  AlertTriangle, 
  Shield, 
  Zap, 
  Users, 
  TrendingUp, 
  Bell,
  CheckCircle,
  MessageSquare,
  Award,
  Eye,
  Clock
} from 'lucide-react';

interface Stats {
  total_users: number;
  total_posts: number;
  total_comments: number;
  verification_rate: number;
  avg_response_time: number;
}

export default function Home() {
  const { isAuthenticated } = useAuth();
  const [stats, setStats] = useState<Stats>({
    total_users: 0,
    total_posts: 0,
    total_comments: 0,
    verification_rate: 0,
    avg_response_time: 0,
  });
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await api.get('/stats');
      setStats(response.data);
    } catch (error) {
      console.error('Error fetching stats:', error);
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <div className="bg-gradient-to-br from-red-600 via-orange-600 to-yellow-500 text-white relative overflow-hidden">
        <div className="absolute inset-0 bg-black opacity-10"></div>
        <div className="container mx-auto px-4 py-24 relative z-10">
          <div className="max-w-4xl mx-auto text-center">
            <div className="inline-block mb-4 px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
              🚨 Community-Driven Alert System
            </div>
            <h1 className="text-5xl md:text-7xl font-bold mb-6 leading-tight">
              Stay Informed.<br />Stay Safe.
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-white/90 max-w-2xl mx-auto">
              Real-time community alerts for traffic, emergencies, crime, market prices, and local events. 
              Powered by AI moderation and reputation-based trust.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center mb-8">
              {isAuthenticated ? (
                <>
                  <Link
                    to="/posts"
                    className="bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center justify-center shadow-lg"
                  >
                    <Bell className="mr-2" size={20} />
                    View Alerts
                    <ArrowRight className="ml-2" size={20} />
                  </Link>
                  <Link
                    to="/create-post"
                    className="bg-red-600 border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-700 transition shadow-lg"
                  >
                    <AlertTriangle className="mr-2 inline" size={20} />
                    Create Alert
                  </Link>
                </>
              ) : (
                <>
                  <Link
                    to="/register"
                    className="bg-white text-orange-600 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition flex items-center justify-center shadow-lg"
                  >
                    Get Started Free
                    <ArrowRight className="ml-2" size={20} />
                  </Link>
                  <Link
                    to="/login"
                    className="bg-transparent border-2 border-white text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-orange-600 transition"
                  >
                    Login
                  </Link>
                </>
              )}
            </div>
            <div className="flex flex-wrap justify-center gap-6 text-sm">
              <div className="flex items-center">
                <CheckCircle className="mr-2" size={16} />
                <span>AI-Powered Moderation</span>
              </div>
              <div className="flex items-center">
                <Shield className="mr-2" size={16} />
                <span>Trust-Based System</span>
              </div>
              <div className="flex items-center">
                <MapPin className="mr-2" size={16} />
                <span>Location-Based Alerts</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Alert Types Section */}
      <div className="py-16 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">What Can You Report?</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Share real-time updates about various situations in your community
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="bg-red-50 border-2 border-red-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">🚨</div>
              <h3 className="text-lg font-bold mb-2 text-red-700">Emergency Alerts</h3>
              <p className="text-gray-600 text-sm">Fire, accidents, medical emergencies, natural disasters</p>
            </div>
            <div className="bg-blue-50 border-2 border-blue-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">🚗</div>
              <h3 className="text-lg font-bold mb-2 text-blue-700">Traffic Updates</h3>
              <p className="text-gray-600 text-sm">Road closures, congestion, accidents, construction</p>
            </div>
            <div className="bg-purple-50 border-2 border-purple-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">🚔</div>
              <h3 className="text-lg font-bold mb-2 text-purple-700">Crime Reports</h3>
              <p className="text-gray-600 text-sm">Theft, suspicious activity, safety concerns</p>
            </div>
            <div className="bg-green-50 border-2 border-green-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">💰</div>
              <h3 className="text-lg font-bold mb-2 text-green-700">Market Prices</h3>
              <p className="text-gray-600 text-sm">Product prices, deals, market updates</p>
            </div>
            <div className="bg-yellow-50 border-2 border-yellow-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">🎉</div>
              <h3 className="text-lg font-bold mb-2 text-yellow-700">Community Events</h3>
              <p className="text-gray-600 text-sm">Local gatherings, festivals, meetings</p>
            </div>
            <div className="bg-orange-50 border-2 border-orange-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">⚠️</div>
              <h3 className="text-lg font-bold mb-2 text-orange-700">Public Notices</h3>
              <p className="text-gray-600 text-sm">Utility outages, service disruptions</p>
            </div>
            <div className="bg-teal-50 border-2 border-teal-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">🌤️</div>
              <h3 className="text-lg font-bold mb-2 text-teal-700">Weather Alerts</h3>
              <p className="text-gray-600 text-sm">Storms, floods, extreme conditions</p>
            </div>
            <div className="bg-indigo-50 border-2 border-indigo-200 p-6 rounded-lg hover:shadow-lg transition">
              <div className="text-4xl mb-3">ℹ️</div>
              <h3 className="text-lg font-bold mb-2 text-indigo-700">General Info</h3>
              <p className="text-gray-600 text-sm">News, announcements, helpful tips</p>
            </div>
          </div>
        </div>
      </div>

      {/* Features Section */}
      <div className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">Powerful Features</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Built with cutting-edge technology to ensure accuracy and trust
            </p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <MapPin className="text-blue-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">Location-Based Alerts</h3>
              <p className="text-gray-600">
                Get alerts relevant to your area. View incidents on an interactive map and filter by distance.
              </p>
            </div>

            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <Shield className="text-purple-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">Trust & Reputation System</h3>
              <p className="text-gray-600">
                Earn reputation points and badges. Trusted users' reports are prioritized and highlighted.
              </p>
            </div>

            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <Zap className="text-green-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">AI-Powered Moderation</h3>
              <p className="text-gray-600">
                Automatic spam detection and content moderation using OpenAI to ensure quality alerts.
              </p>
            </div>

            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <Bell className="text-red-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">Real-Time Notifications</h3>
              <p className="text-gray-600">
                Get instant notifications for emergency alerts and updates in your area.
              </p>
            </div>

            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-yellow-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <TrendingUp className="text-yellow-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">Upvote & Verify</h3>
              <p className="text-gray-600">
                Community verification through upvotes/downvotes. Verified alerts get priority visibility.
              </p>
            </div>

            <div className="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
              <div className="bg-teal-100 w-16 h-16 rounded-full flex items-center justify-center mb-4">
                <MessageSquare className="text-teal-600" size={32} />
              </div>
              <h3 className="text-xl font-bold mb-3 text-gray-900">Community Discussion</h3>
              <p className="text-gray-600">
                Comment, discuss, and share additional information with nested replies and reactions.
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* How It Works Section */}
      <div className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold text-gray-900 mb-4">How It Works</h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Simple steps to start contributing to your community's safety
            </p>
          </div>
          <div className="grid md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="bg-gradient-to-br from-blue-500 to-blue-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg">
                1
              </div>
              <h3 className="text-lg font-bold mb-2 text-gray-900">Sign Up Free</h3>
              <p className="text-gray-600">Create your account in seconds with email verification</p>
            </div>
            <div className="text-center">
              <div className="bg-gradient-to-br from-purple-500 to-purple-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg">
                2
              </div>
              <h3 className="text-lg font-bold mb-2 text-gray-900">Create Alert</h3>
              <p className="text-gray-600">Share what's happening with location, photos, and priority level</p>
            </div>
            <div className="text-center">
              <div className="bg-gradient-to-br from-orange-500 to-orange-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg">
                3
              </div>
              <h3 className="text-lg font-bold mb-2 text-gray-900">Community Verifies</h3>
              <p className="text-gray-600">Others upvote, comment, and verify the accuracy of alerts</p>
            </div>
            <div className="text-center">
              <div className="bg-gradient-to-br from-green-500 to-green-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl font-bold shadow-lg">
                4
              </div>
              <h3 className="text-lg font-bold mb-2 text-gray-900">Earn Reputation</h3>
              <p className="text-gray-600">Build trust score and unlock badges as a valued contributor</p>
            </div>
          </div>
        </div>
      </div>

      {/* Stats Section */}
      <div className="py-16 bg-gradient-to-br from-blue-600 to-purple-700 text-white">
        <div className="container mx-auto px-4">
          {loading ? (
            <div className="text-center">
              <div className="animate-pulse">
                <div className="text-xl">Loading statistics...</div>
              </div>
            </div>
          ) : (
            <div className="grid md:grid-cols-4 gap-8 text-center">
              <div className="transform hover:scale-105 transition-transform">
                <div className="text-5xl font-bold mb-2">
                  <Users className="inline mb-2" size={48} />
                </div>
                <div className="text-3xl font-bold mb-1">
                  {stats.total_users.toLocaleString()}+
                </div>
                <div className="text-blue-100">Active Users</div>
              </div>
              <div className="transform hover:scale-105 transition-transform">
                <div className="text-5xl font-bold mb-2">
                  <Bell className="inline mb-2" size={48} />
                </div>
                <div className="text-3xl font-bold mb-1">
                  {stats.total_posts.toLocaleString()}+
                </div>
                <div className="text-blue-100">Alerts Shared</div>
              </div>
              <div className="transform hover:scale-105 transition-transform">
                <div className="text-5xl font-bold mb-2">
                  <Eye className="inline mb-2" size={48} />
                </div>
                <div className="text-3xl font-bold mb-1">
                  {stats.verification_rate}%
                </div>
                <div className="text-blue-100">Verification Rate</div>
              </div>
              <div className="transform hover:scale-105 transition-transform">
                <div className="text-5xl font-bold mb-2">
                  <Clock className="inline mb-2" size={48} />
                </div>
                <div className="text-3xl font-bold mb-1">
                  {stats.avg_response_time < 60 
                    ? `${stats.avg_response_time}min` 
                    : `${Math.round(stats.avg_response_time / 60)}h`}
                </div>
                <div className="text-blue-100">Avg Response Time</div>
              </div>
            </div>
          )}
        </div>
      </div>

      {/* Trust & Safety Section */}
      <div className="py-20 bg-gray-50">
        <div className="container mx-auto px-4">
          <div className="max-w-4xl mx-auto">
            <div className="text-center mb-12">
              <Shield className="mx-auto mb-4 text-blue-600" size={64} />
              <h2 className="text-4xl font-bold text-gray-900 mb-4">Built on Trust & Safety</h2>
              <p className="text-xl text-gray-600">
                Our reputation system ensures reliable information
              </p>
            </div>
            <div className="grid md:grid-cols-3 gap-6">
              <div className="bg-white p-6 rounded-lg shadow-md text-center">
                <Award className="mx-auto mb-3 text-yellow-600" size={40} />
                <h3 className="font-bold text-lg mb-2">Reputation Levels</h3>
                <p className="text-gray-600 text-sm">Bronze to Diamond levels based on contributions</p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow-md text-center">
                <TrendingUp className="mx-auto mb-3 text-green-600" size={40} />
                <h3 className="font-bold text-lg mb-2">Trust Score</h3>
                <p className="text-gray-600 text-sm">Dynamic scoring based on accuracy and activity</p>
              </div>
              <div className="bg-white p-6 rounded-lg shadow-md text-center">
                <CheckCircle className="mx-auto mb-3 text-blue-600" size={40} />
                <h3 className="font-bold text-lg mb-2">Verified Badges</h3>
                <p className="text-gray-600 text-sm">Earn badges for achievements and milestones</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* CTA Section */}
      <div className="bg-gradient-to-r from-orange-600 to-red-600 text-white py-20">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">Join Your Community Today</h2>
          <p className="text-xl md:text-2xl mb-8 text-white/90 max-w-2xl mx-auto">
            Be part of a safer, more informed community. Start sharing and receiving real-time alerts now.
          </p>
          {!isAuthenticated && (
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                to="/register"
                className="bg-white text-orange-600 px-10 py-5 rounded-lg font-bold text-lg hover:bg-gray-100 transition inline-flex items-center justify-center shadow-xl"
              >
                Get Started Free
                <ArrowRight className="ml-2" size={24} />
              </Link>
              <Link
                to="/posts"
                className="bg-transparent border-2 border-white text-white px-10 py-5 rounded-lg font-bold text-lg hover:bg-white hover:text-orange-600 transition"
              >
                View Live Alerts
              </Link>
            </div>
          )}
          <p className="mt-6 text-white/80">No credit card required • Free forever</p>
        </div>
      </div>
    </div>
  );
}
