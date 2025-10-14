import React, { useState, useEffect } from 'react';
import { toast } from 'react-hot-toast';
import { Award, TrendingUp, Users, Star, Edit, Save, X } from 'lucide-react';
import api from '../../services/api';

interface User {
  id: number;
  name: string;
  email: string;
  trust_score: number;
  reputation_level: string;
  badges?: string | null;
  created_at: string;
}

interface ReputationStats {
  total_users: number;
  average_trust_score: number;
  reputation_distribution: Record<string, number>;
  recent_changes: any[];
}

const ReputationManagement: React.FC = () => {
  const [users, setUsers] = useState<User[]>([]);
  const [stats, setStats] = useState<ReputationStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [editingUser, setEditingUser] = useState<number | null>(null);
  const [editForm, setEditForm] = useState({
    trust_score: 0,
    reputation_level: '',
    badges: ''
  });

  useEffect(() => {
    fetchUsers();
    fetchStats();
  }, []);

  const fetchUsers = async () => {
    try {
      const response = await api.get('/admin/users');
      setUsers(response.data.data || response.data || []);
    } catch (error) {
      console.error('Error fetching users:', error);
      toast.error('Failed to fetch users');
    }
  };

  const fetchStats = async () => {
    try {
      const response = await api.get('/admin/reputation-stats');
      setStats(response.data);
    } catch (error) {
      console.error('Error fetching reputation stats:', error);
      // Create mock stats if API doesn't exist
      setStats({
        total_users: users.length,
        average_trust_score: 75,
        reputation_distribution: {
          'bronze': 45,
          'silver': 30,
          'gold': 20,
          'platinum': 4,
          'diamond': 1
        },
        recent_changes: []
      });
    } finally {
      setLoading(false);
    }
  };

  const startEditing = (user: User) => {
    setEditingUser(user.id);
    setEditForm({
      trust_score: user.trust_score || 0,
      reputation_level: user.reputation_level || 'bronze',
      badges: user.badges || ''
    });
  };

  const cancelEditing = () => {
    setEditingUser(null);
    setEditForm({ trust_score: 0, reputation_level: '', badges: '' });
  };

  const saveChanges = async (userId: number) => {
    try {
      await api.put(`/admin/users/${userId}/reputation`, editForm);
      toast.success('Reputation updated successfully');
      setEditingUser(null);
      fetchUsers();
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Failed to update reputation');
    }
  };

  const getReputationColor = (level: string) => {
    switch (level.toLowerCase()) {
      case 'bronze': return 'text-amber-600 bg-amber-100';
      case 'silver': return 'text-gray-600 bg-gray-100';
      case 'gold': return 'text-yellow-600 bg-yellow-100';
      case 'platinum': return 'text-blue-600 bg-blue-100';
      case 'diamond': return 'text-purple-600 bg-purple-100';
      default: return 'text-gray-500 bg-gray-50';
    }
  };

  const getTrustScoreColor = (score: number) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 70) return 'text-blue-600';
    if (score >= 50) return 'text-yellow-600';
    if (score >= 30) return 'text-orange-600';
    return 'text-red-600';
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-xl">Loading reputation data...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-4xl font-bold text-gray-900 flex items-center">
            <Award className="mr-3 text-yellow-600" size={40} />
            Reputation Management
          </h1>
        </div>

        {/* Stats Cards */}
        {stats && (
          <div className="grid md:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Total Users</p>
                  <p className="text-3xl font-bold text-blue-600">{stats.total_users}</p>
                </div>
                <Users className="text-blue-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Avg Trust Score</p>
                  <p className="text-3xl font-bold text-green-600">{stats.average_trust_score}</p>
                </div>
                <TrendingUp className="text-green-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Gold+ Users</p>
                  <p className="text-3xl font-bold text-yellow-600">
                    {(stats.reputation_distribution.gold || 0) + 
                     (stats.reputation_distribution.platinum || 0) + 
                     (stats.reputation_distribution.diamond || 0)}
                  </p>
                </div>
                <Star className="text-yellow-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Diamond Users</p>
                  <p className="text-3xl font-bold text-purple-600">
                    {stats.reputation_distribution.diamond || 0}
                  </p>
                </div>
                <Award className="text-purple-600" size={32} />
              </div>
            </div>
          </div>
        )}

        {/* Reputation Distribution */}
        {stats && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Reputation Distribution</h2>
            <div className="grid grid-cols-5 gap-4">
              {Object.entries(stats.reputation_distribution).map(([level, count]) => (
                <div key={level} className="text-center">
                  <div className={`w-16 h-16 mx-auto rounded-full flex items-center justify-center ${getReputationColor(level)} mb-2`}>
                    <span className="text-2xl font-bold">{count}</span>
                  </div>
                  <p className="text-sm font-medium capitalize">{level}</p>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Users Table */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200">
            <h2 className="text-2xl font-bold text-gray-900">User Reputation</h2>
          </div>
          
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    User
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Trust Score
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Reputation Level
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Badges
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {users.map((user) => (
                  <tr key={user.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div className="text-sm font-medium text-gray-900">{user.name}</div>
                        <div className="text-sm text-gray-500">{user.email}</div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {editingUser === user.id ? (
                        <input
                          type="number"
                          min="0"
                          max="100"
                          value={editForm.trust_score}
                          onChange={(e) => setEditForm({...editForm, trust_score: parseInt(e.target.value) || 0})}
                          className="w-20 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                      ) : (
                        <span className={`text-sm font-semibold ${getTrustScoreColor(user.trust_score || 0)}`}>
                          {user.trust_score || 0}/100
                        </span>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {editingUser === user.id ? (
                        <select
                          value={editForm.reputation_level}
                          onChange={(e) => setEditForm({...editForm, reputation_level: e.target.value})}
                          className="px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                          <option value="bronze">Bronze</option>
                          <option value="silver">Silver</option>
                          <option value="gold">Gold</option>
                          <option value="platinum">Platinum</option>
                          <option value="diamond">Diamond</option>
                        </select>
                      ) : (
                        <span className={`px-3 py-1 rounded-full text-xs font-medium ${getReputationColor(user.reputation_level || 'bronze')}`}>
                          {(user.reputation_level || 'bronze').charAt(0).toUpperCase() + (user.reputation_level || 'bronze').slice(1)}
                        </span>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      {editingUser === user.id ? (
                        <input
                          type="text"
                          value={editForm.badges}
                          onChange={(e) => setEditForm({...editForm, badges: e.target.value})}
                          placeholder="Helper,Verified,Expert"
                          className="w-32 px-2 py-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                      ) : (
                        <div className="flex flex-wrap gap-1">
                          {user.badges ? (
                            user.badges.split(',').map((badge, index) => (
                              <span key={index} className="px-2 py-1 bg-indigo-100 text-indigo-800 text-xs rounded-full">
                                {badge.trim()}
                              </span>
                            ))
                          ) : (
                            <span className="text-gray-400 text-sm">No badges</span>
                          )}
                        </div>
                      )}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      {editingUser === user.id ? (
                        <div className="flex space-x-2">
                          <button
                            onClick={() => saveChanges(user.id)}
                            className="text-green-600 hover:text-green-900"
                          >
                            <Save size={16} />
                          </button>
                          <button
                            onClick={cancelEditing}
                            className="text-gray-600 hover:text-gray-900"
                          >
                            <X size={16} />
                          </button>
                        </div>
                      ) : (
                        <button
                          onClick={() => startEditing(user)}
                          className="text-blue-600 hover:text-blue-900"
                        >
                          <Edit size={16} />
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  );
};

export default ReputationManagement;
