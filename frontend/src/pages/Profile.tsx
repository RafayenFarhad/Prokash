import { useState, useEffect } from 'react';
import { useAuth } from '../context/AuthContext';
import type { Post } from '../types';
import api from '../services/api';
import PostCard from '../components/PostCard';
import { User, Mail, Calendar, Award, TrendingUp, Star, Shield } from 'lucide-react';

export default function Profile() {
  const { user } = useAuth();
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchUserPosts();
  }, []);

  const fetchUserPosts = async () => {
    try {
      const response = await api.get('/posts');
      // Filter posts by current user
      const userPosts = response.data.data?.filter((post: Post) => post.user_id === user?.id) || [];
      setPosts(userPosts);
    } catch (error) {
      console.error('Error fetching user posts:', error);
    } finally {
      setLoading(false);
    }
  };

  // Helper function to get reputation level info
  const getReputationLevelInfo = (level: string) => {
    const levels: Record<string, { name: string; icon: string; color: string; bgColor: string }> = {
      bronze: { name: 'Bronze', icon: '🥉', color: 'text-amber-700', bgColor: 'bg-amber-50 border-amber-300' },
      silver: { name: 'Silver', icon: '🥈', color: 'text-gray-600', bgColor: 'bg-gray-50 border-gray-300' },
      gold: { name: 'Gold', icon: '🥇', color: 'text-yellow-600', bgColor: 'bg-yellow-50 border-yellow-300' },
      platinum: { name: 'Platinum', icon: '💎', color: 'text-blue-600', bgColor: 'bg-blue-50 border-blue-300' },
      diamond: { name: 'Diamond', icon: '💠', color: 'text-purple-600', bgColor: 'bg-purple-50 border-purple-300' },
    };
    return levels[level] || levels.bronze;
  };

  // Helper function to get badge info
  const getBadgeInfo = (badge: string) => {
    const badges: Record<string, { name: string; icon: string; description: string; color: string }> = {
      verified_user: { name: 'Verified User', icon: '✅', description: 'Email verified', color: 'bg-green-100 text-green-700' },
      member: { name: 'Member', icon: '👤', description: '30+ days active', color: 'bg-blue-100 text-blue-700' },
      contributor: { name: 'Contributor', icon: '📝', description: '10+ posts', color: 'bg-blue-100 text-blue-700' },
      active_contributor: { name: 'Active Contributor', icon: '📚', description: '50+ posts', color: 'bg-purple-100 text-purple-700' },
      super_contributor: { name: 'Super Contributor', icon: '🌟', description: '100+ posts', color: 'bg-yellow-100 text-yellow-700' },
      trusted_reporter: { name: 'Trusted Reporter', icon: '🛡️', description: '5+ verified posts', color: 'bg-blue-100 text-blue-700' },
      expert_reporter: { name: 'Expert Reporter', icon: '🎯', description: '20+ verified posts', color: 'bg-indigo-100 text-indigo-700' },
      first_responder: { name: 'First Responder', icon: '🚨', description: '3+ emergency alerts', color: 'bg-red-100 text-red-700' },
      emergency_expert: { name: 'Emergency Expert', icon: '🚑', description: '10+ emergency alerts', color: 'bg-red-100 text-red-700' },
      community_favorite: { name: 'Community Favorite', icon: '❤️', description: '25+ upvotes received', color: 'bg-pink-100 text-pink-700' },
      highly_valued: { name: 'Highly Valued', icon: '💎', description: '100+ upvotes received', color: 'bg-purple-100 text-purple-700' },
      community_helper: { name: 'Community Helper', icon: '🤝', description: '5+ accepted responses', color: 'bg-green-100 text-green-700' },
      guardian_angel: { name: 'Guardian Angel', icon: '👼', description: '20+ accepted responses', color: 'bg-yellow-100 text-yellow-700' },
      conversationalist: { name: 'Conversationalist', icon: '💬', description: '50+ comments', color: 'bg-teal-100 text-teal-700' },
      discussion_leader: { name: 'Discussion Leader', icon: '🗣️', description: '200+ comments', color: 'bg-orange-100 text-orange-700' },
      veteran: { name: 'Veteran', icon: '🏆', description: '6+ months member', color: 'bg-yellow-100 text-yellow-700' },
      legend: { name: 'Legend', icon: '👑', description: '1+ year member', color: 'bg-yellow-100 text-yellow-700' },
      trusted_member: { name: 'Trusted Member', icon: '🌟', description: '80+ trust score', color: 'bg-blue-100 text-blue-700' },
      exemplary_citizen: { name: 'Exemplary Citizen', icon: '🏅', description: '95+ trust score', color: 'bg-yellow-100 text-yellow-700' },
      clean_record: { name: 'Clean Record', icon: '🧹', description: 'No spam reports', color: 'bg-green-100 text-green-700' },
    };
    return badges[badge] || { name: badge.replace(/_/g, ' '), icon: '🏅', description: 'Achievement unlocked', color: 'bg-gray-100 text-gray-700' };
  };

  // Parse badges from JSON string
  const getUserBadges = () => {
    if (!user?.badges) return [];
    try {
      return typeof user.badges === 'string' ? JSON.parse(user.badges) : user.badges;
    } catch {
      return [];
    }
  };

  if (!user) return null;

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        {/* Profile Header */}
        <div className="bg-white rounded-lg shadow-md p-8 mb-8">
          <div className="flex items-center space-x-6">
            <div className="bg-blue-100 rounded-full p-6">
              <User size={48} className="text-blue-600" />
            </div>
            <div className="flex-1">
              <h1 className="text-3xl font-bold text-gray-900">{user.name}</h1>
              <div className="flex items-center space-x-4 mt-2 text-gray-600">
                <div className="flex items-center">
                  <Mail size={16} className="mr-2" />
                  {user.email}
                </div>
                <div className="flex items-center">
                  <Calendar size={16} className="mr-2" />
                  Joined {new Date(user.created_at).toLocaleDateString()}
                </div>
              </div>
              <div className="mt-3">
                <span className={`px-3 py-1 rounded-full text-sm font-semibold ${
                  user.role === 'admin' 
                    ? 'bg-purple-100 text-purple-700' 
                    : 'bg-blue-100 text-blue-700'
                }`}>
                  {user.role === 'admin' ? 'Administrator' : 'User'}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Reputation & Stats Section */}
        <div className="grid md:grid-cols-2 gap-6 mb-8">
          {/* Reputation Card */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <div className="flex items-center justify-between mb-4">
              <h3 className="text-xl font-bold text-gray-900 flex items-center">
                <Shield className="mr-2 text-blue-600" size={24} />
                Reputation
              </h3>
            </div>
            
            {/* Trust Score */}
            <div className="mb-6">
              <div className="flex items-center justify-between mb-2">
                <span className="text-gray-600 font-medium">Trust Score</span>
                <span className="text-2xl font-bold text-blue-600">{user.trust_score?.toFixed(1) || '0.0'}</span>
              </div>
              <div className="w-full bg-gray-200 rounded-full h-3">
                <div 
                  className="bg-gradient-to-r from-blue-500 to-purple-600 h-3 rounded-full transition-all duration-500"
                  style={{ width: `${user.trust_score || 0}%` }}
                ></div>
              </div>
              <p className="text-sm text-gray-500 mt-1">Out of 100</p>
            </div>

            {/* Reputation Level */}
            <div className={`border-2 rounded-lg p-4 ${getReputationLevelInfo(user.reputation_level || 'bronze').bgColor}`}>
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-600 font-medium">Current Level</p>
                  <p className={`text-2xl font-bold ${getReputationLevelInfo(user.reputation_level || 'bronze').color}`}>
                    {getReputationLevelInfo(user.reputation_level || 'bronze').icon} {getReputationLevelInfo(user.reputation_level || 'bronze').name}
                  </p>
                </div>
                <TrendingUp className={getReputationLevelInfo(user.reputation_level || 'bronze').color} size={32} />
              </div>
            </div>
          </div>

          {/* User Stats Card */}
          <div className="bg-white rounded-lg shadow-md p-6">
            <h3 className="text-xl font-bold text-gray-900 mb-4 flex items-center">
              <Star className="mr-2 text-yellow-600" size={24} />
              Statistics
            </h3>
            <div className="space-y-4">
              <div className="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <span className="text-gray-700 font-medium">Total Posts</span>
                <span className="text-2xl font-bold text-blue-600">{posts.length}</span>
              </div>
              <div className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <span className="text-gray-700 font-medium">Total Likes</span>
                <span className="text-2xl font-bold text-green-600">
                  {posts.reduce((sum, post) => sum + (post.likes_count || 0), 0)}
                </span>
              </div>
              <div className="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                <span className="text-gray-700 font-medium">Total Comments</span>
                <span className="text-2xl font-bold text-purple-600">
                  {posts.reduce((sum, post) => sum + (post.comments_count || 0), 0)}
                </span>
              </div>
              <div className="flex items-center justify-between p-3 bg-orange-50 rounded-lg">
                <span className="text-gray-700 font-medium">Total Upvotes</span>
                <span className="text-2xl font-bold text-orange-600">
                  {posts.reduce((sum, post) => sum + (post.upvotes || 0), 0)}
                </span>
              </div>
            </div>
          </div>
        </div>

        {/* Badges & Achievements */}
        <div className="bg-white rounded-lg shadow-md p-6 mb-8">
          <h3 className="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <Award className="mr-2 text-yellow-600" size={24} />
            Badges & Achievements
          </h3>
          {getUserBadges().length > 0 ? (
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              {getUserBadges().map((badge: string) => {
                const badgeInfo = getBadgeInfo(badge);
                return (
                  <div 
                    key={badge}
                    className={`${badgeInfo.color} rounded-lg p-4 text-center hover:scale-105 transition-transform cursor-pointer`}
                    title={badgeInfo.description}
                  >
                    <div className="text-3xl mb-2">{badgeInfo.icon}</div>
                    <div className="font-semibold text-sm">{badgeInfo.name}</div>
                    <div className="text-xs opacity-75 mt-1">{badgeInfo.description}</div>
                  </div>
                );
              })}
            </div>
          ) : (
            <div className="text-center py-8 text-gray-500">
              <Award size={48} className="mx-auto mb-3 opacity-30" />
              <p>No badges earned yet. Keep contributing to earn badges!</p>
            </div>
          )}
        </div>

        {/* User Posts */}
        <div>
          <h2 className="text-2xl font-bold text-gray-900 mb-6">My Posts</h2>
          {loading ? (
            <div className="text-center py-12">
              <div className="text-xl text-gray-600">Loading posts...</div>
            </div>
          ) : posts.length === 0 ? (
            <div className="bg-white rounded-lg shadow-md p-12 text-center">
              <p className="text-gray-600 text-lg">You haven't created any posts yet.</p>
              <a href="/create-post" className="inline-block mt-4 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
                Create Your First Post
              </a>
            </div>
          ) : (
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
              {posts.map((post) => (
                <PostCard key={post.id} post={post} />
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
