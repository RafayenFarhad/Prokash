import React, { useState, useEffect } from 'react';
import { toast } from 'react-hot-toast';
import api from '../services/api';
import { useAuth } from '../context/AuthContext';

interface CommunityResponseData {
  id: number;
  user: {
    id: number;
    name: string;
    trust_score: number;
    reputation_level: string;
  };
  response_type: string;
  message: string;
  resources: string[];
  contact_method: string;
  contact_info: string;
  status: string;
  created_at: string;
}

interface CommunityResponseProps {
  postId: number;
}

const CommunityResponse: React.FC<CommunityResponseProps> = ({ postId }) => {
  const { isAuthenticated } = useAuth();
  const [responses, setResponses] = useState<CommunityResponseData[]>([]);
  const [showForm, setShowForm] = useState(false);
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    response_type: 'help',
    message: '',
    resources: '',
    contact_method: 'app',
    contact_info: ''
  });

  useEffect(() => {
    fetchResponses();
  }, [postId]);

  const fetchResponses = async () => {
    try {
      const response = await api.get(`/posts/${postId}/responses`);
      setResponses(response.data || []);
    } catch (error) {
      console.error('Error fetching community responses:', error);
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!isAuthenticated) {
      toast.error('Please login to offer help');
      return;
    }

    setLoading(true);
    try {
      const resourcesArray = formData.resources
        .split(',')
        .map(r => r.trim())
        .filter(Boolean);

      await api.post(`/posts/${postId}/responses`, {
        ...formData,
        resources: resourcesArray
      });

      toast.success('Your offer to help has been submitted!');
      setShowForm(false);
      setFormData({
        response_type: 'help',
        message: '',
        resources: '',
        contact_method: 'app',
        contact_info: ''
      });
      fetchResponses();
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Failed to submit response');
    } finally {
      setLoading(false);
    }
  };

  const getResponseTypeIcon = (type: string) => {
    switch (type) {
      case 'help': return '🤝';
      case 'resources': return '📦';
      case 'information': return 'ℹ️';
      case 'support': return '💪';
      default: return '❤️';
    }
  };

  const getReputationColor = (level: string) => {
    switch (level.toLowerCase()) {
      case 'bronze': return 'text-amber-600';
      case 'silver': return 'text-gray-600';
      case 'gold': return 'text-yellow-600';
      case 'platinum': return 'text-blue-600';
      case 'diamond': return 'text-purple-600';
      default: return 'text-gray-500';
    }
  };

  return (
    <div className="mt-6 border-t pt-6">
      <div className="flex items-center justify-between mb-4">
        <h3 className="text-lg font-semibold text-gray-900 flex items-center">
          🤝 Community Response ({responses.length})
        </h3>
        {isAuthenticated && !showForm && (
          <button
            onClick={() => setShowForm(true)}
            className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center space-x-2"
          >
            <span>🙋‍♂️</span>
            <span>I Can Help</span>
          </button>
        )}
      </div>

      {/* Response Form */}
      {showForm && (
        <div className="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
          <h4 className="font-semibold text-green-800 mb-3">Offer Your Help</h4>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Type of Help
              </label>
              <select
                value={formData.response_type}
                onChange={(e) => setFormData({ ...formData, response_type: e.target.value })}
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              >
                <option value="help">General Help</option>
                <option value="resources">Provide Resources</option>
                <option value="information">Share Information</option>
                <option value="support">Emotional Support</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Message
              </label>
              <textarea
                value={formData.message}
                onChange={(e) => setFormData({ ...formData, message: e.target.value })}
                placeholder="Describe how you can help..."
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                rows={3}
                required
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Resources Available (comma-separated)
              </label>
              <input
                type="text"
                value={formData.resources}
                onChange={(e) => setFormData({ ...formData, resources: e.target.value })}
                placeholder="e.g., First Aid, Transportation, Food"
                className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Contact Method
                </label>
                <select
                  value={formData.contact_method}
                  onChange={(e) => setFormData({ ...formData, contact_method: e.target.value })}
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                  <option value="app">Through App</option>
                  <option value="phone">Phone</option>
                  <option value="email">Email</option>
                  <option value="social">Social Media</option>
                </select>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Contact Info (optional)
                </label>
                <input
                  type="text"
                  value={formData.contact_info}
                  onChange={(e) => setFormData({ ...formData, contact_info: e.target.value })}
                  placeholder="Phone, email, etc."
                  className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                />
              </div>
            </div>

            <div className="flex space-x-3">
              <button
                type="submit"
                disabled={loading}
                className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium disabled:opacity-50"
              >
                {loading ? 'Submitting...' : 'Submit Offer'}
              </button>
              <button
                type="button"
                onClick={() => setShowForm(false)}
                className="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-md font-medium"
              >
                Cancel
              </button>
            </div>
          </form>
        </div>
      )}

      {/* Existing Responses */}
      <div className="space-y-4">
        {responses.length === 0 ? (
          <p className="text-gray-500 text-center py-8">
            No community responses yet. Be the first to offer help! 🤝
          </p>
        ) : (
          responses.map((response) => (
            <div
              key={response.id}
              className="bg-white border border-gray-200 rounded-lg p-4 shadow-sm"
            >
              <div className="flex items-start justify-between">
                <div className="flex items-center space-x-3">
                  <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <span className="text-green-600 font-semibold">
                      {response.user.name.charAt(0).toUpperCase()}
                    </span>
                  </div>
                  <div>
                    <div className="flex items-center space-x-2">
                      <h4 className="font-semibold text-gray-900">{response.user.name}</h4>
                      <span className={`text-sm font-medium ${getReputationColor(response.user.reputation_level)}`}>
                        {response.user.reputation_level.charAt(0).toUpperCase() + response.user.reputation_level.slice(1)}
                      </span>
                      <span className="text-sm text-gray-500">
                        Trust: {response.user.trust_score}/100
                      </span>
                    </div>
                    <p className="text-sm text-gray-500">
                      {new Date(response.created_at).toLocaleDateString()}
                    </p>
                  </div>
                </div>
                <span className="text-2xl" title={response.response_type}>
                  {getResponseTypeIcon(response.response_type)}
                </span>
              </div>

              <div className="mt-3">
                <p className="text-gray-700">{response.message}</p>
                
                {response.resources && response.resources.length > 0 && (
                  <div className="mt-2">
                    <p className="text-sm font-medium text-gray-600">Available Resources:</p>
                    <div className="flex flex-wrap gap-2 mt-1">
                      {response.resources.map((resource, index) => (
                        <span
                          key={index}
                          className="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
                        >
                          {resource}
                        </span>
                      ))}
                    </div>
                  </div>
                )}

                <div className="mt-3 flex items-center justify-between">
                  <span className="text-sm text-gray-500">
                    Contact: {response.contact_method}
                  </span>
                  <span className={`text-xs px-2 py-1 rounded-full ${
                    response.status === 'active' 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-gray-100 text-gray-600'
                  }`}>
                    {response.status}
                  </span>
                </div>
              </div>
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default CommunityResponse;
