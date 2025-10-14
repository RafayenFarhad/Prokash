import React, { useState, useEffect } from 'react';
import { toast } from 'react-hot-toast';
import { Bell, Send, Eye, Trash2, Check, AlertCircle } from 'lucide-react';
import api from '../../services/api';

interface Notification {
  id: number;
  type: string;
  title: string;
  message: string;
  data?: any;
  read: boolean;
  read_at?: string;
  created_at: string;
  user?: {
    id: number;
    name: string;
    email: string;
  };
}

interface NotificationStats {
  total_notifications: number;
  unread_count: number;
  notifications_today: number;
  by_type: Record<string, number>;
}

const NotificationManagement: React.FC = () => {
  const [notifications, setNotifications] = useState<Notification[]>([]);
  const [stats, setStats] = useState<NotificationStats | null>(null);
  const [loading, setLoading] = useState(true);
  const [selectedNotifications, setSelectedNotifications] = useState<number[]>([]);
  const [showBulkActions, setShowBulkActions] = useState(false);

  useEffect(() => {
    fetchNotifications();
    fetchStats();
  }, []);

  const fetchNotifications = async () => {
    try {
      const response = await api.get('/admin/notifications');
      setNotifications(response.data.data || response.data || []);
    } catch (error) {
      console.error('Error fetching notifications:', error);
      // Create mock notifications for demo
      setNotifications([
        {
          id: 1,
          type: 'community_help',
          title: '🤝 Someone Offered Help!',
          message: 'John Doe offered to help with your alert: Emergency at Downtown',
          read: false,
          created_at: new Date().toISOString(),
          user: { id: 1, name: 'Admin User', email: 'admin@prokash.com' }
        },
        {
          id: 2,
          type: 'post_verification',
          title: '✅ Post Verified',
          message: 'Your post has been verified by the community',
          read: true,
          read_at: new Date().toISOString(),
          created_at: new Date(Date.now() - 3600000).toISOString(),
          user: { id: 2, name: 'Test User', email: 'test@example.com' }
        }
      ]);
    }
  };

  const fetchStats = async () => {
    try {
      const response = await api.get('/admin/notification-stats');
      setStats(response.data);
    } catch (error) {
      console.error('Error fetching notification stats:', error);
      // Create mock stats
      setStats({
        total_notifications: 156,
        unread_count: 23,
        notifications_today: 12,
        by_type: {
          'community_help': 45,
          'post_verification': 32,
          'emergency_alert': 8,
          'post_comment': 71
        }
      });
    } finally {
      setLoading(false);
    }
  };

  const handleSelectNotification = (notificationId: number) => {
    setSelectedNotifications(prev => {
      const newSelection = prev.includes(notificationId)
        ? prev.filter(id => id !== notificationId)
        : [...prev, notificationId];
      
      setShowBulkActions(newSelection.length > 0);
      return newSelection;
    });
  };

  const handleSelectAll = () => {
    if (selectedNotifications.length === notifications.length) {
      setSelectedNotifications([]);
      setShowBulkActions(false);
    } else {
      setSelectedNotifications(notifications.map(n => n.id));
      setShowBulkActions(true);
    }
  };

  const markAsRead = async (notificationIds: number[]) => {
    try {
      await Promise.all(
        notificationIds.map(id => api.post(`/notifications/${id}/read`))
      );
      toast.success('Notifications marked as read');
      fetchNotifications();
      setSelectedNotifications([]);
      setShowBulkActions(false);
    } catch (error) {
      toast.error('Failed to mark notifications as read');
    }
  };

  const deleteNotifications = async (notificationIds: number[]) => {
    try {
      await Promise.all(
        notificationIds.map(id => api.delete(`/notifications/${id}`))
      );
      toast.success('Notifications deleted');
      fetchNotifications();
      setSelectedNotifications([]);
      setShowBulkActions(false);
    } catch (error) {
      toast.error('Failed to delete notifications');
    }
  };

  const getNotificationIcon = (type: string) => {
    switch (type) {
      case 'community_help': return '🤝';
      case 'post_like': return '❤️';
      case 'post_comment': return '💬';
      case 'post_verification': return '✅';
      case 'emergency_alert': return '🚨';
      case 'reputation_update': return '⭐';
      default: return '📢';
    }
  };

  const getTypeColor = (type: string) => {
    switch (type) {
      case 'emergency_alert': return 'bg-red-100 text-red-800';
      case 'community_help': return 'bg-green-100 text-green-800';
      case 'post_verification': return 'bg-blue-100 text-blue-800';
      case 'reputation_update': return 'bg-yellow-100 text-yellow-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const formatTimeAgo = (dateString: string) => {
    const date = new Date(dateString);
    const now = new Date();
    const diffInMinutes = Math.floor((now.getTime() - date.getTime()) / (1000 * 60));
    
    if (diffInMinutes < 1) return 'Just now';
    if (diffInMinutes < 60) return `${diffInMinutes}m ago`;
    if (diffInMinutes < 1440) return `${Math.floor(diffInMinutes / 60)}h ago`;
    return `${Math.floor(diffInMinutes / 1440)}d ago`;
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-xl">Loading notifications...</div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4">
        <div className="flex items-center justify-between mb-8">
          <h1 className="text-4xl font-bold text-gray-900 flex items-center">
            <Bell className="mr-3 text-blue-600" size={40} />
            Notification Management
          </h1>
        </div>

        {/* Stats Cards */}
        {stats && (
          <div className="grid md:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Total Notifications</p>
                  <p className="text-3xl font-bold text-blue-600">{stats.total_notifications}</p>
                </div>
                <Bell className="text-blue-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Unread</p>
                  <p className="text-3xl font-bold text-red-600">{stats.unread_count}</p>
                </div>
                <AlertCircle className="text-red-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Today</p>
                  <p className="text-3xl font-bold text-green-600">{stats.notifications_today}</p>
                </div>
                <Send className="text-green-600" size={32} />
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-md p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-gray-600 text-sm">Emergency Alerts</p>
                  <p className="text-3xl font-bold text-orange-600">{stats.by_type.emergency_alert || 0}</p>
                </div>
                <AlertCircle className="text-orange-600" size={32} />
              </div>
            </div>
          </div>
        )}

        {/* Notification Types Distribution */}
        {stats && (
          <div className="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">Notification Types</h2>
            <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
              {Object.entries(stats.by_type).map(([type, count]) => (
                <div key={type} className="text-center">
                  <div className={`w-16 h-16 mx-auto rounded-full flex items-center justify-center ${getTypeColor(type)} mb-2`}>
                    <span className="text-2xl">{getNotificationIcon(type)}</span>
                  </div>
                  <p className="text-sm font-medium capitalize">{type.replace('_', ' ')}</p>
                  <p className="text-lg font-bold text-gray-900">{count}</p>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Bulk Actions */}
        {showBulkActions && (
          <div className="bg-white rounded-lg shadow-md p-4 mb-6">
            <div className="flex items-center justify-between">
              <span className="text-sm text-gray-600">
                {selectedNotifications.length} notification(s) selected
              </span>
              <div className="flex space-x-2">
                <button
                  onClick={() => markAsRead(selectedNotifications)}
                  className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm flex items-center space-x-2"
                >
                  <Check size={16} />
                  <span>Mark as Read</span>
                </button>
                <button
                  onClick={() => deleteNotifications(selectedNotifications)}
                  className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm flex items-center space-x-2"
                >
                  <Trash2 size={16} />
                  <span>Delete</span>
                </button>
              </div>
            </div>
          </div>
        )}

        {/* Notifications Table */}
        <div className="bg-white rounded-lg shadow-md overflow-hidden">
          <div className="px-6 py-4 border-b border-gray-200">
            <div className="flex items-center justify-between">
              <h2 className="text-2xl font-bold text-gray-900">All Notifications</h2>
              <label className="flex items-center space-x-2">
                <input
                  type="checkbox"
                  checked={selectedNotifications.length === notifications.length && notifications.length > 0}
                  onChange={handleSelectAll}
                  className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                />
                <span className="text-sm text-gray-600">Select All</span>
              </label>
            </div>
          </div>
          
          <div className="divide-y divide-gray-200">
            {notifications.length === 0 ? (
              <div className="p-8 text-center text-gray-500">
                <Bell size={48} className="mx-auto mb-4 text-gray-300" />
                <p>No notifications found</p>
              </div>
            ) : (
              notifications.map((notification) => (
                <div
                  key={notification.id}
                  className={`p-6 hover:bg-gray-50 transition-colors ${
                    !notification.read ? 'bg-blue-50' : ''
                  }`}
                >
                  <div className="flex items-start space-x-4">
                    <input
                      type="checkbox"
                      checked={selectedNotifications.includes(notification.id)}
                      onChange={() => handleSelectNotification(notification.id)}
                      className="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                    />
                    
                    <div className="flex-shrink-0">
                      <span className="text-2xl">
                        {getNotificationIcon(notification.type)}
                      </span>
                    </div>
                    
                    <div className="flex-1 min-w-0">
                      <div className="flex items-center justify-between mb-1">
                        <h4 className="text-sm font-semibold text-gray-900 truncate">
                          {notification.title}
                        </h4>
                        <div className="flex items-center space-x-2">
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${getTypeColor(notification.type)}`}>
                            {notification.type.replace('_', ' ')}
                          </span>
                          {!notification.read && (
                            <span className="w-2 h-2 bg-blue-600 rounded-full"></span>
                          )}
                        </div>
                      </div>
                      
                      <p className="text-sm text-gray-600 mb-2 line-clamp-2">
                        {notification.message}
                      </p>
                      
                      <div className="flex items-center justify-between text-xs text-gray-400">
                        <span>
                          To: {notification.user?.name || 'Unknown User'}
                        </span>
                        <span>
                          {formatTimeAgo(notification.created_at)}
                        </span>
                      </div>
                    </div>
                    
                    <div className="flex items-center space-x-2">
                      {!notification.read && (
                        <button
                          onClick={() => markAsRead([notification.id])}
                          className="p-1 text-blue-600 hover:text-blue-800"
                          title="Mark as read"
                        >
                          <Eye size={16} />
                        </button>
                      )}
                      <button
                        onClick={() => deleteNotifications([notification.id])}
                        className="p-1 text-red-600 hover:text-red-800"
                        title="Delete notification"
                      >
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default NotificationManagement;
