import { Link } from 'react-router-dom';
import type { Post } from '../types';
import { Heart, MessageCircle, MapPin, Calendar, ShieldCheck, AlertTriangle, ThumbsUp, ThumbsDown } from 'lucide-react';

interface PostCardProps {
  post: Post;
}

export default function PostCard({ post }: PostCardProps) {
  const API_URL = import.meta.env.VITE_API_URL?.replace('/api', '') || 'http://localhost:8000';
  
  // Handle both external URLs (http/https) and local storage paths
  const imageUrl = post.image 
    ? (post.image.startsWith('http') ? post.image : `${API_URL}/storage/${post.image}`)
    : null;
  const videoUrl = post.video 
    ? (post.video.startsWith('http') ? post.video : `${API_URL}/storage/${post.video}`)
    : null;

  const getPriorityColor = (priority: string) => {
    switch (priority) {
      case 'emergency': return 'bg-red-100 text-red-700 border-red-300';
      case 'high': return 'bg-orange-100 text-orange-700 border-orange-300';
      case 'medium': return 'bg-yellow-100 text-yellow-700 border-yellow-300';
      default: return 'bg-blue-100 text-blue-700 border-blue-300';
    }
  };

  return (
    <div className={`bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow ${post.priority === 'emergency' ? 'border-2 border-red-500' : ''}`}>
      {/* Priority Badge */}
      {post.priority !== 'low' && (
        <div className={`px-4 py-2 ${getPriorityColor(post.priority)} border-b flex items-center justify-between`}>
          <div className="flex items-center">
            <AlertTriangle size={16} className="mr-2" />
            <span className="font-semibold text-xs uppercase">{post.priority} Priority</span>
          </div>
          {post.is_verified && (
            <div className="flex items-center text-green-700">
              <ShieldCheck size={16} className="mr-1" />
              <span className="text-xs font-semibold">Verified</span>
            </div>
          )}
        </div>
      )}
      
      {/* Media Display - Image or Video */}
      {videoUrl ? (
        <video
          src={videoUrl}
          className="w-full h-48 object-cover"
          controls
          preload="metadata"
        >
          Your browser does not support the video tag.
        </video>
      ) : imageUrl ? (
        <img
          src={imageUrl}
          alt={post.title}
          className="w-full h-48 object-cover"
          onError={(e) => {
            console.error('Image failed to load:', imageUrl);
            e.currentTarget.style.display = 'none';
          }}
        />
      ) : null}
      <div className="p-6">
        <div className="flex items-center justify-between mb-2">
          <span className="text-sm text-blue-600 font-semibold">
            {post.category?.name || 'Uncategorized'}
          </span>
          <span className="text-xs text-gray-500 flex items-center">
            <Calendar size={14} className="mr-1" />
            {new Date(post.created_at).toLocaleDateString()}
          </span>
        </div>
        
        <Link to={`/posts/${post.id}`}>
          <h3 className="text-xl font-bold text-gray-900 mb-2 hover:text-blue-600">
            {post.title}
          </h3>
        </Link>
        
        <p className="text-gray-600 mb-4 line-clamp-3">{post.content}</p>
        
        <div className="flex items-center justify-between text-sm text-gray-500 mb-3">
          <div className="flex items-center space-x-4">
            <span className="flex items-center">
              <Heart size={16} className="mr-1" />
              {post.likes_count || 0}
            </span>
            <span className="flex items-center">
              <MessageCircle size={16} className="mr-1" />
              {post.comments_count || 0}
            </span>
            {post.latitude && post.longitude && (
              <span className="flex items-center text-green-600">
                <MapPin size={16} className="mr-1" />
                {post.location_name || 'Location'}
              </span>
            )}
          </div>
          <span className="text-gray-700 font-medium">{post.user?.name}</span>
        </div>
        
        {/* Verification Score */}
        <div className="flex items-center space-x-3 text-sm">
          <div className="flex items-center text-green-600">
            <ThumbsUp size={14} className="mr-1" />
            <span className="font-semibold">{post.upvotes || 0}</span>
          </div>
          <div className="flex items-center text-red-600">
            <ThumbsDown size={14} className="mr-1" />
            <span className="font-semibold">{post.downvotes || 0}</span>
          </div>
          {post.verification_score > 0 && (
            <span className="text-xs text-gray-600">
              Score: <span className="font-semibold text-green-600">+{post.verification_score}</span>
            </span>
          )}
        </div>
        
        {post.tags && post.tags.length > 0 && (
          <div className="mt-4 flex flex-wrap gap-2">
            {post.tags.map((tag) => (
              <span
                key={tag.id}
                className="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs"
              >
                #{tag.name}
              </span>
            ))}
          </div>
        )}
      </div>
    </div>
  );
}
