import { useState, useEffect } from 'react';
import type { Post } from '../../types';
import api from '../../services/api';
import { Trash2, Search, Eye, ShieldCheck, ShieldOff } from 'lucide-react';
import { Link } from 'react-router-dom';

export default function PostsManagement() {
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [totalPosts, setTotalPosts] = useState(0);
  const [stats, setStats] = useState({ verified: 0, unverified: 0, emergency: 0 });

  useEffect(() => {
    fetchPosts();
    fetchStats();
  }, [search]);

  const fetchStats = async () => {
    try {
      const response = await api.get('/admin/stats');
      const data = response.data;
      setStats({
        verified: data.verified_posts || 0,
        unverified: data.total_posts - (data.verified_posts || 0),
        emergency: data.emergency_posts || 0
      });
    } catch (error) {
      console.error('Error fetching stats:', error);
    }
  };

  const fetchPosts = async () => {
    try {
      setLoading(true);
      const params: any = {};
      if (search) params.search = search;
      
      const response = await api.get('/admin/posts', { params });
      setPosts(response.data.data || response.data);
      setTotalPosts(response.data.total || (response.data.data?.length || response.data.length || 0));
    } catch (error) {
      console.error('Error fetching posts:', error);
    } finally {
      setLoading(false);
    }
  };

  const handleDeletePost = async (postId: number, postTitle: string) => {
    if (window.confirm(`Are you sure you want to delete post "${postTitle}"?`)) {
      try {
        await api.delete(`/admin/posts/${postId}`);
        fetchPosts();
      } catch (error) {
        console.error('Error deleting post:', error);
        alert('Failed to delete post');
      }
    }
  };

  const handleToggleVerification = async (postId: number, currentStatus: boolean, postTitle: string) => {
    const action = currentStatus ? 'unverify' : 'verify';
    const confirmMessage = currentStatus 
      ? `Remove verification from "${postTitle}"?`
      : `Verify "${postTitle}" as authentic?`;
    
    if (window.confirm(confirmMessage)) {
      try {
        const response = await api.post(`/admin/posts/${postId}/verify`);
        alert(response.data.message);
        fetchPosts(); // Refresh the list
      } catch (error: any) {
        console.error('Error toggling verification:', error);
        alert(error.response?.data?.message || `Failed to ${action} post`);
      }
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4 max-w-7xl">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">Post Moderation</h1>
          <p className="text-gray-600">Review, verify, and manage community posts</p>
        </div>

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
          <div className="bg-white rounded-lg shadow-md p-4">
            <div className="text-sm text-gray-600">Total Posts</div>
            <div className="text-2xl font-bold text-gray-900">{totalPosts}</div>
          </div>
          <div className="bg-white rounded-lg shadow-md p-4">
            <div className="text-sm text-gray-600">Verified</div>
            <div className="text-2xl font-bold text-green-600">
              {stats.verified}
            </div>
          </div>
          <div className="bg-white rounded-lg shadow-md p-4">
            <div className="text-sm text-gray-600">Unverified</div>
            <div className="text-2xl font-bold text-orange-600">
              {stats.unverified}
            </div>
          </div>
          <div className="bg-white rounded-lg shadow-md p-4">
            <div className="text-sm text-gray-600">Emergency</div>
            <div className="text-2xl font-bold text-red-600">
              {stats.emergency}
            </div>
          </div>
        </div>

        {/* Search */}
        <div className="bg-white rounded-lg shadow-md p-4 mb-6">
          <div className="relative">
            <Search className="absolute left-3 top-3 text-gray-400" size={20} />
            <input
              type="text"
              placeholder="Search posts by title or content..."
              className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
          </div>
        </div>

        {/* Posts List */}
        {loading ? (
          <div className="text-center py-20">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p className="mt-4 text-gray-600">Loading posts...</p>
          </div>
        ) : posts.length === 0 ? (
          <div className="text-center py-20 bg-white rounded-lg shadow-md">
            <div className="text-6xl mb-4">📭</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">No posts found</h3>
            <p className="text-gray-600">Try adjusting your search terms</p>
          </div>
        ) : (
          <div className="space-y-4">
            {posts.map((post) => (
              <div key={post.id} className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow">
                <div className="p-6">
                  <div className="flex items-start justify-between mb-4">
                    <div className="flex-1">
                      <div className="flex items-center gap-2 mb-2">
                        <h3 className="text-lg font-bold text-gray-900">{post.title}</h3>
                        {post.priority === 'emergency' && (
                          <span className="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded">
                            🚨 EMERGENCY
                          </span>
                        )}
                        {post.priority === 'high' && (
                          <span className="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded">
                            ⚠️ HIGH
                          </span>
                        )}
                      </div>
                      <p className="text-gray-600 text-sm line-clamp-2 mb-3">{post.content}</p>
                      
                      <div className="flex flex-wrap items-center gap-3 text-sm">
                        <div className="flex items-center text-gray-600">
                          <span className="font-medium mr-1">Author:</span>
                          {post.user?.name}
                        </div>
                        <span className="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                          {post.category?.name || 'Uncategorized'}
                        </span>
                        {post.is_verified ? (
                          <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <ShieldCheck size={14} className="mr-1" />
                            Verified
                          </span>
                        ) : (
                          <span className="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                            <ShieldOff size={14} className="mr-1" />
                            Unverified
                          </span>
                        )}
                        <div className="text-gray-600">
                          ❤️ {post.likes_count || 0} • 💬 {post.comments_count || 0}
                        </div>
                        <div className="text-gray-500">
                          📅 {new Date(post.created_at).toLocaleDateString()}
                        </div>
                      </div>
                    </div>
                  </div>

                  {/* Actions */}
                  <div className="flex items-center gap-2 pt-4 border-t">
                    <Link
                      to={`/posts/${post.id}`}
                      className="flex items-center px-4 py-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition text-sm font-medium"
                    >
                      <Eye size={16} className="mr-2" />
                      View
                    </Link>
                    <button
                      onClick={() => handleToggleVerification(post.id, post.is_verified, post.title)}
                      className={`flex items-center px-4 py-2 rounded-lg transition text-sm font-medium ${
                        post.is_verified
                          ? 'bg-orange-50 text-orange-600 hover:bg-orange-100'
                          : 'bg-green-50 text-green-600 hover:bg-green-100'
                      }`}
                    >
                      {post.is_verified ? (
                        <>
                          <ShieldOff size={16} className="mr-2" />
                          Unverify
                        </>
                      ) : (
                        <>
                          <ShieldCheck size={16} className="mr-2" />
                          Verify
                        </>
                      )}
                    </button>
                    <button
                      onClick={() => handleDeletePost(post.id, post.title)}
                      className="flex items-center px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition text-sm font-medium"
                    >
                      <Trash2 size={16} className="mr-2" />
                      Delete
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
