import { useState, useEffect } from 'react';
import type { AdminStats } from '../../types';
import api from '../../services/api';
import { Users, FileText, MessageCircle, TrendingUp, AlertTriangle, Award, Bell } from 'lucide-react';
import { Link } from 'react-router-dom';
import EmergencyBroadcast from '../../components/EmergencyBroadcast';

export default function Dashboard() {
  const [stats, setStats] = useState<AdminStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [showEmergencyBroadcast, setShowEmergencyBroadcast] = useState(false);

  useEffect(() => {
    fetchStats();
  }, []);

  const fetchStats = async () => {
    try {
      const response = await api.get('/admin/stats');
      setStats(response.data);
    } catch (error) {
      console.error('Error fetching stats:', error);
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-xl">Loading dashboard...</div>
      </div>
    );
  }

  if (!stats) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-xl text-red-600">Failed to load dashboard</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <h1 className="text-4xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

        {/* Stats Grid */}
        <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Total Users</p>
                <p className="text-3xl font-bold text-blue-600">{stats.total_users}</p>
                <p className="text-sm text-gray-500 mt-1">
                  +{stats.users_this_month} this month
                </p>
              </div>
              <div className="bg-blue-100 p-3 rounded-full">
                <Users className="text-blue-600" size={32} />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Total Posts</p>
                <p className="text-3xl font-bold text-green-600">{stats.total_posts}</p>
                <p className="text-sm text-gray-500 mt-1">
                  +{stats.posts_this_month} this month
                </p>
              </div>
              <div className="bg-green-100 p-3 rounded-full">
                <FileText className="text-green-600" size={32} />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Total Comments</p>
                <p className="text-3xl font-bold text-purple-600">{stats.total_comments}</p>
              </div>
              <div className="bg-purple-100 p-3 rounded-full">
                <MessageCircle className="text-purple-600" size={32} />
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex items-center justify-between">
              <div>
                <p className="text-gray-600 text-sm">Growth Rate</p>
                <p className="text-3xl font-bold text-orange-600">
                  {stats.users_this_month > 0 ? '+' : ''}{stats.users_this_month}
                </p>
                <p className="text-sm text-gray-500 mt-1">New users</p>
              </div>
              <div className="bg-orange-100 p-3 rounded-full">
                <TrendingUp className="text-orange-600" size={32} />
              </div>
            </div>
          </div>
        </div>

        {/* Quick Actions */}
        <div className="grid md:grid-cols-2 gap-6 mb-8">
          <Link
            to="/admin/users"
            className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition"
          >
            <h3 className="text-xl font-bold text-gray-900 mb-2">Manage Users</h3>
            <p className="text-gray-600">View and manage user accounts, roles, and permissions</p>
          </Link>

          <Link
            to="/admin/posts"
            className="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition"
          >
            <h3 className="text-xl font-bold text-gray-900 mb-2">Moderate Posts</h3>
            <p className="text-gray-600">Review and moderate user-generated content</p>
          </Link>
        </div>

        {/* Admin Actions */}
        <div className="grid md:grid-cols-3 gap-6 mb-8">
          <button
            onClick={() => setShowEmergencyBroadcast(true)}
            className="bg-red-600 hover:bg-red-700 text-white p-6 rounded-lg shadow-md transition-colors flex items-center space-x-4"
          >
            <AlertTriangle size={32} />
            <div className="text-left">
              <h3 className="text-lg font-semibold">Emergency Broadcast</h3>
              <p className="text-red-100 text-sm">Send alerts to all users</p>
            </div>
          </button>

          <Link
            to="/admin/reputation"
            className="bg-yellow-600 hover:bg-yellow-700 text-white p-6 rounded-lg shadow-md transition-colors flex items-center space-x-4"
          >
            <Award size={32} />
            <div className="text-left">
              <h3 className="text-lg font-semibold">Reputation System</h3>
              <p className="text-yellow-100 text-sm">Manage user reputation</p>
            </div>
          </Link>

          <Link
            to="/admin/notifications"
            className="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-md transition-colors flex items-center space-x-4"
          >
            <Bell size={32} />
            <div className="text-left">
              <h3 className="text-lg font-semibold">Notifications</h3>
              <p className="text-blue-100 text-sm">Manage notifications</p>
            </div>
          </Link>
        </div>

        {/* Recent Activity Grid */}
        <div className="grid md:grid-cols-2 gap-8">
          {/* Recent Users */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Recent Users</h2>
            <div className="space-y-3">
              {stats.recent_users.map((user) => (
                <div key={user.id} className="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                  <div className="bg-blue-100 p-2 rounded-full">
                    <Users className="text-blue-600" size={20} />
                  </div>
                  <div>
                    <p className="font-semibold text-gray-900">{user.name}</p>
                    <p className="text-sm text-gray-600">{user.email}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Recent Posts */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Recent Posts</h2>
            <div className="space-y-3">
              {stats.recent_posts.map((post) => (
                <div key={post.id} className="border-b pb-3">
                  <Link to={`/posts/${post.id}`} className="font-semibold text-gray-900 hover:text-blue-600">
                    {post.title}
                  </Link>
                  <p className="text-sm text-gray-600 mt-1">
                    By {post.user?.name} • {new Date(post.created_at).toLocaleDateString()}
                  </p>
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Emergency Broadcast Modal */}
        {showEmergencyBroadcast && (
          <EmergencyBroadcast onClose={() => setShowEmergencyBroadcast(false)} />
        )}
      </div>
    </div>
  );
}
