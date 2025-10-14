import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import type { Post, Comment } from '../types';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';
import { Heart, MessageCircle, MapPin, Trash2, Edit, ThumbsUp, ThumbsDown, ShieldCheck, AlertTriangle, Flag } from 'lucide-react';
import ReputationBadge from '../components/ReputationBadge';
import CommunityResponse from '../components/CommunityResponse';

export default function PostDetail() {
  const { id } = useParams();
  const { user, isAuthenticated } = useAuth();
  const navigate = useNavigate();
  const [post, setPost] = useState<Post | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [newComment, setNewComment] = useState('');
  const [liked, setLiked] = useState(false);
  const [verified, setVerified] = useState<'upvote' | 'downvote' | null>(null);
  const [showReportModal, setShowReportModal] = useState(false);
  const [loading, setLoading] = useState(true);
  const [userVote, setUserVote] = useState<'upvote' | 'downvote' | null>(null);
  const [replyingTo, setReplyingTo] = useState<number | null>(null);
  const [replyContent, setReplyContent] = useState('');

  useEffect(() => {
    fetchPost();
    fetchComments();
    if (isAuthenticated) {
      checkLiked();
      checkVerified();
      checkUserVote();
    }
  }, [id]);

  const fetchPost = async () => {
    try {
      const response = await api.get(`/posts/${id}`);
      setPost(response.data);
    } catch (error) {
      console.error('Error fetching post:', error);
    } finally {
      setLoading(false);
    }
  };

  const fetchComments = async () => {
    try {
      const response = await api.get(`/posts/${id}/comments`);
      setComments(response.data);
    } catch (error) {
      console.error('Error fetching comments:', error);
    }
  };

  const checkLiked = async () => {
    try {
      const response = await api.get(`/posts/${id}/like/check`);
      setLiked(response.data.liked);
    } catch (error) {
      console.error('Error checking like:', error);
    }
  };

  const checkVerified = async () => {
    try {
      const response = await api.get(`/posts/${id}/verify/check`);
      if (response.data.verified) {
        setVerified(response.data.type);
      }
    } catch (error) {
      console.error('Error checking verification:', error);
    }
  };

  const handleVerify = async (type: 'upvote' | 'downvote') => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }
    try {
      await api.post(`/posts/${id}/verify`, { type });
      // Toggle off if same type, otherwise update
      setVerified(verified === type ? null : type);
      fetchPost();
    } catch (error) {
      console.error('Error toggling verification:', error);
    }
  };

  const handleReport = async (reason: string, description: string) => {
    try {
      await api.post(`/posts/${id}/report`, { reason, description });
      setShowReportModal(false);
      alert('Report submitted successfully');
    } catch (error: any) {
      alert(error.response?.data?.message || 'Failed to submit report');
    }
  };

  const handleLike = async () => {
    if (!isAuthenticated) {
      navigate('/login');
      return;
    }
    try {
      const response = await api.post(`/posts/${id}/like`);
      setLiked(response.data.liked);
      fetchPost();
    } catch (error) {
      console.error('Error toggling like:', error);
    }
  };

  const handleCommentSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post(`/posts/${id}/comments`, { content: newComment });
      setNewComment('');
      fetchComments();
      fetchPost();
    } catch (error) {
      console.error('Error posting comment:', error);
    }
  };

  const handleDeletePost = async () => {
    if (window.confirm('Are you sure you want to delete this post?')) {
      try {
        await api.delete(`/posts/${id}`);
        navigate('/posts');
      } catch (error) {
        console.error('Error deleting post:', error);
      }
    }
  };

  const checkUserVote = async () => {
    try {
      const response = await api.get(`/posts/${id}/user-vote`);
      setUserVote(response.data.vote_type);
    } catch (error) {
      console.error('Error checking user vote:', error);
    }
  };

  const handleVote = async (voteType: 'upvote' | 'downvote') => {
    if (!isAuthenticated) {
      alert('Please login to vote');
      return;
    }
    try {
      const response = await api.post(`/posts/${id}/vote`, { vote_type: voteType });
      setUserVote(response.data.user_vote);
      fetchPost(); // Refresh post to get updated vote counts
    } catch (error) {
      console.error('Error voting:', error);
    }
  };

  const handleReply = async (parentId: number) => {
    if (!replyContent.trim()) return;
    try {
      await api.post(`/posts/${id}/comments`, { 
        content: replyContent, 
        parent_id: parentId 
      });
      setReplyContent('');
      setReplyingTo(null);
      fetchComments();
    } catch (error) {
      console.error('Error posting reply:', error);
    }
  };


  if (loading) return <div className="min-h-screen flex items-center justify-center">Loading...</div>;
  if (!post) return <div className="min-h-screen flex items-center justify-center">Post not found</div>;

  const imageUrl = post.image ? `http://localhost:8000/storage/${post.image}` : null;
  const videoUrl = post.video ? `http://localhost:8000/storage/${post.video}` : null;

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'emergency': return 'bg-red-100 text-red-700 border-red-300';
      case 'high': return 'bg-orange-100 text-orange-700 border-orange-300';
      case 'medium': return 'bg-yellow-100 text-yellow-700 border-yellow-300';
      default: return 'bg-blue-100 text-blue-700 border-blue-300';
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4 max-w-4xl">
        <div className={`bg-white rounded-lg shadow-md overflow-hidden ${post.priority === 'emergency' ? 'border-4 border-red-500' : ''}`}>
          {/* Priority Badge */}
          {post.priority !== 'low' && (
            <div className={`px-6 py-3 ${getPriorityColor(post.priority)} border-b flex items-center justify-between`}>
              <div className="flex items-center">
                <AlertTriangle size={20} className="mr-2" />
                <span className="font-bold text-sm uppercase">{post.priority} Priority Alert</span>
              </div>
              {post.is_verified && (
                <div className="flex items-center text-green-700">
                  <ShieldCheck size={20} className="mr-1" />
                  <span className="text-sm font-bold">Community Verified</span>
                </div>
              )}
            </div>
          )}

          {imageUrl && <img src={imageUrl} alt={post.title} className="w-full h-96 object-cover" />}
          {videoUrl && (
            <div className="w-full bg-black">
              <video src={videoUrl} controls className="w-full max-h-96">
                Your browser does not support the video tag.
              </video>
            </div>
          )}
          
          <div className="p-8">
            <div className="flex justify-between items-start mb-4">
              <div className="flex-1">
                <span className="text-sm text-blue-600 font-semibold">{post.category?.name}</span>
                <h1 className="text-4xl font-bold text-gray-900 mt-2">{post.title}</h1>
              </div>
              {user && (user.id === post.user_id || user.role === 'admin') && (
                <div className="flex space-x-2">
                  <button 
                    onClick={() => navigate(`/edit-post/${post.id}`)} 
                    className="text-blue-600 hover:text-blue-700"
                  >
                    <Edit size={20} />
                  </button>
                  <button onClick={handleDeletePost} className="text-red-600 hover:text-red-700">
                    <Trash2 size={20} />
                  </button>
                </div>
              )}
            </div>

            <div className="flex items-center justify-between mb-6">
              <div className="flex items-center text-gray-600">
                <span className="font-medium">{post.user?.name}</span>
                <span className="mx-2">•</span>
                <span>{new Date(post.created_at).toLocaleDateString()}</span>
              </div>
              {post.user && (
                <ReputationBadge user={post.user} size="small" showDetails={true} />
              )}
            </div>

            <div className="prose max-w-none mb-6 whitespace-pre-wrap">{post.content}</div>

            {post.latitude && post.longitude && (
              <div className="flex items-center text-green-600 mb-6">
                <MapPin size={20} className="mr-2" />
                <span>{post.location_name || `${post.latitude}, ${post.longitude}`}</span>
              </div>
            )}

            {post.tags && post.tags.length > 0 && (
              <div className="flex flex-wrap gap-2 mb-6">
                {post.tags.map((tag) => (
                  <span key={tag.id} className="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">
                    #{tag.name}
                  </span>
                ))}
              </div>
            )}

            <div className="border-t border-b py-4 space-y-4">
              {/* Verification Section */}
              <div className="flex items-center justify-between">
                <div className="flex items-center space-x-4">
                  <button
                    onClick={() => handleVote('upvote')}
                    className={`flex items-center space-x-2 px-4 py-2 rounded-lg transition ${
                      userVote === 'upvote'
                        ? 'bg-green-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-green-100'
                    }`}
                  >
                    <ThumbsUp size={20} fill={userVote === 'upvote' ? 'currentColor' : 'none'} />
                    <span className="font-semibold">{post.upvotes || 0}</span>
                  </button>
                  <button
                    onClick={() => handleVote('downvote')}
                    className={`flex items-center space-x-2 px-4 py-2 rounded-lg transition ${
                      userVote === 'downvote'
                        ? 'bg-red-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-red-100'
                    }`}
                  >
                    <ThumbsDown size={20} fill={userVote === 'downvote' ? 'currentColor' : 'none'} />
                    <span className="font-semibold">{post.downvotes || 0}</span>
                  </button>
                  <div className="flex items-center space-x-2 text-gray-600">
                    <span className="text-sm">Score:</span>
                    <span className={`font-bold ${
                      post.verification_score > 0 ? 'text-green-600' : 
                      post.verification_score < 0 ? 'text-red-600' : 'text-gray-600'
                    }`}>
                      {post.verification_score > 0 ? '+' : ''}{post.verification_score}
                    </span>
                  </div>
                </div>
                <button
                  onClick={() => setShowReportModal(true)}
                  className="flex items-center space-x-2 text-gray-600 hover:text-red-600 transition"
                >
                  <Flag size={18} />
                  <span className="text-sm">Report</span>
                </button>
              </div>

              {/* Like and Comment Count */}
              <div className="flex items-center space-x-6">
                <button
                  onClick={handleLike}
                  className={`flex items-center space-x-2 ${liked ? 'text-red-600' : 'text-gray-600'} hover:text-red-600 transition`}
                >
                  <Heart size={20} fill={liked ? 'currentColor' : 'none'} />
                  <span>{post.likes_count || 0}</span>
                </button>
                <div className="flex items-center space-x-2 text-gray-600">
                  <MessageCircle size={20} />
                  <span>{post.comments_count || 0}</span>
                </div>
              </div>
            </div>

            {/* Comments Section */}
            <div className="mt-8">
              <h2 className="text-2xl font-bold mb-4">Comments</h2>
              
              {isAuthenticated ? (
                <form onSubmit={handleCommentSubmit} className="mb-6">
                  <textarea
                    className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows={3}
                    placeholder="Write a comment..."
                    value={newComment}
                    onChange={(e) => setNewComment(e.target.value)}
                    required
                  />
                  <button
                    type="submit"
                    className="mt-2 bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
                  >
                    Post Comment
                  </button>
                </form>
              ) : (
                <div className="mb-6 p-4 bg-gray-100 rounded-lg text-center">
                  <p className="text-gray-600">Please <a href="/login" className="text-blue-600 hover:underline">login</a> to comment</p>
                </div>
              )}

              <div className="space-y-4">
                {comments.length === 0 ? (
                  <p className="text-gray-500 text-center py-4">No comments yet. Be the first to comment!</p>
                ) : (
                  comments.map((comment) => (
                    <div key={comment.id} className="bg-gray-50 p-4 rounded-lg mb-4">
                      <div className="flex justify-between items-start mb-2">
                        <span className="font-semibold">{comment.user?.name}</span>
                        <span className="text-sm text-gray-500">
                          {new Date(comment.created_at).toLocaleDateString()}
                        </span>
                      </div>
                      <p className="text-gray-700 mb-3">{comment.content}</p>
                      
                      {/* Reply Button */}
                      {isAuthenticated && (
                        <button
                          onClick={() => setReplyingTo(replyingTo === comment.id ? null : comment.id)}
                          className="text-blue-600 hover:text-blue-800 text-sm font-medium"
                        >
                          Reply
                        </button>
                      )}

                      {/* Reply Form */}
                      {replyingTo === comment.id && (
                        <div className="mt-3 p-3 bg-white rounded border">
                          <textarea
                            value={replyContent}
                            onChange={(e) => setReplyContent(e.target.value)}
                            placeholder={`Reply to ${comment.user?.name}...`}
                            className="w-full p-2 border border-gray-300 rounded-md resize-none focus:outline-none focus:ring-2 focus:ring-blue-500"
                            rows={2}
                          />
                          <div className="flex justify-end space-x-2 mt-2">
                            <button
                              onClick={() => {
                                setReplyingTo(null);
                                setReplyContent('');
                              }}
                              className="px-3 py-1 text-gray-600 hover:text-gray-800"
                            >
                              Cancel
                            </button>
                            <button
                              onClick={() => handleReply(comment.id)}
                              className="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700"
                            >
                              Reply
                            </button>
                          </div>
                        </div>
                      )}

                      {/* Replies */}
                      {comment.replies && comment.replies.length > 0 && (
                        <div className="mt-4 ml-6 space-y-3">
                          {comment.replies.map((reply: any) => (
                            <div key={reply.id} className="bg-white p-3 rounded border-l-4 border-blue-200">
                              <div className="flex justify-between items-start mb-2">
                                <span className="font-semibold text-sm">{reply.user?.name}</span>
                                <span className="text-xs text-gray-500">
                                  {new Date(reply.created_at).toLocaleDateString()}
                                </span>
                              </div>
                              <p className="text-gray-700 text-sm">{reply.content}</p>
                            </div>
                          ))}
                        </div>
                      )}
                    </div>
                  ))
                )}
              </div>
            </div>

            {/* Community Response Section */}
            <CommunityResponse postId={post.id} />
          </div>
        </div>

        {/* Report Modal */}
        {showReportModal && (
          <ReportModal
            onClose={() => setShowReportModal(false)}
            onSubmit={handleReport}
          />
        )}
      </div>
    </div>
  );
}

// Report Modal Component
function ReportModal({ onClose, onSubmit }: { onClose: () => void; onSubmit: (reason: string, description: string) => void }) {
  const [reason, setReason] = useState('spam');
  const [description, setDescription] = useState('');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    onSubmit(reason, description);
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg max-w-md w-full p-6">
        <h2 className="text-2xl font-bold mb-4">Report Post</h2>
        <form onSubmit={handleSubmit}>
          <div className="mb-4">
            <label className="block text-gray-700 font-semibold mb-2">Reason *</label>
            <select
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={reason}
              onChange={(e) => setReason(e.target.value)}
              required
            >
              <option value="spam">Spam</option>
              <option value="false_info">False Information</option>
              <option value="inappropriate">Inappropriate Content</option>
              <option value="harassment">Harassment</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div className="mb-4">
            <label className="block text-gray-700 font-semibold mb-2">Description (Optional)</label>
            <textarea
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows={4}
              placeholder="Provide additional details..."
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              maxLength={500}
            />
            <p className="text-sm text-gray-500 mt-1">{description.length}/500</p>
          </div>
          <div className="flex space-x-3">
            <button
              type="submit"
              className="flex-1 bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 font-semibold"
            >
              Submit Report
            </button>
            <button
              type="button"
              onClick={onClose}
              className="flex-1 bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 font-semibold"
            >
              Cancel
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
