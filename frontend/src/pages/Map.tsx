import { useState, useEffect } from 'react';
import { MapContainer, TileLayer, Marker, Popup } from 'react-leaflet';
import type { Post } from '../types';
import api from '../services/api';
import { Link } from 'react-router-dom';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

// Fix for default marker icon
delete (L.Icon.Default.prototype as any)._getIconUrl;
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
});

export default function Map() {
  const [posts, setPosts] = useState<Post[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    fetchPosts();
  }, []);

  const fetchPosts = async () => {
    try {
      const response = await api.get('/posts', {
        params: { has_location: true },
      });
      const postsData = response.data.data || response.data;
      const postsWithLocation = postsData.filter(
        (post: Post) => post.latitude && post.longitude
      );
      setPosts(postsWithLocation);
      setError(null);
    } catch (error) {
      console.error('Error fetching posts:', error);
      setError('Failed to load posts. Please try again.');
    } finally {
      setLoading(false);
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-xl">Loading map...</div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <div className="text-xl text-red-600 mb-4">{error}</div>
          <button
            onClick={fetchPosts}
            className="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700"
          >
            Try Again
          </button>
        </div>
      </div>
    );
  }

  // Calculate center of all posts or use default (Dhaka, Bangladesh)
  const centerLat = posts.length > 0 
    ? posts.reduce((sum, post) => sum + (Number(post.latitude) || 0), 0) / posts.length 
    : 23.8103;
  const centerLng = posts.length > 0 
    ? posts.reduce((sum, post) => sum + (Number(post.longitude) || 0), 0) / posts.length 
    : 90.4125;

  // Ensure valid coordinates
  const validCenterLat = isNaN(centerLat) ? 23.8103 : centerLat;
  const validCenterLng = isNaN(centerLng) ? 90.4125 : centerLng;

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <h1 className="text-4xl font-bold text-gray-900 mb-8">Posts Map</h1>
        
        <div className="bg-white rounded-lg shadow-md overflow-hidden" style={{ height: '600px' }}>
          <MapContainer
            center={[validCenterLat, validCenterLng]}
            zoom={6}
            style={{ height: '100%', width: '100%' }}
          >
            <TileLayer
              attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
              url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
            />
            {posts.map((post) => (
              <Marker
                key={post.id}
                position={[post.latitude!, post.longitude!]}
              >
                <Popup>
                  <div className="p-2">
                    <h3 className="font-bold text-lg mb-2">{post.title}</h3>
                    <p className="text-sm text-gray-600 mb-2 line-clamp-2">{post.content}</p>
                    <div className="text-xs text-gray-500 mb-2">
                      By {post.user?.name} • {new Date(post.created_at).toLocaleDateString()}
                    </div>
                    <Link
                      to={`/posts/${post.id}`}
                      className="inline-block bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
                    >
                      View Post
                    </Link>
                  </div>
                </Popup>
              </Marker>
            ))}
          </MapContainer>
        </div>

        <div className="mt-8">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">
            {posts.length > 0 ? `Posts on Map (${posts.length})` : 'No Posts with Location Yet'}
          </h2>
          {posts.length > 0 ? (
            <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
              {posts.map((post) => (
                <Link
                  key={post.id}
                  to={`/posts/${post.id}`}
                  className="bg-white p-4 rounded-lg shadow hover:shadow-md transition"
                >
                  <h3 className="font-semibold text-gray-900 mb-1">{post.title}</h3>
                  <p className="text-sm text-gray-600 mb-2 line-clamp-2">{post.content}</p>
                  <div className="text-xs text-green-600">
                    📍 {post.location_name || `${post.latitude}, ${post.longitude}`}
                  </div>
                </Link>
              ))}
            </div>
          ) : (
            <div className="bg-white p-8 rounded-lg shadow text-center">
              <p className="text-gray-600 mb-4">
                No alerts with location data have been posted yet.
              </p>
              <Link
                to="/create-post"
                className="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition"
              >
                Create First Alert with Location
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
