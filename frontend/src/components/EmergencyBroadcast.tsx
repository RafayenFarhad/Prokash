import React, { useState } from 'react';
import { toast } from 'react-hot-toast';
import { AlertTriangle, Send, Users, Mail, MessageSquare, Bell } from 'lucide-react';
import api from '../services/api';

interface EmergencyBroadcastProps {
  onClose: () => void;
}

const EmergencyBroadcast: React.FC<EmergencyBroadcastProps> = ({ onClose }) => {
  const [loading, setLoading] = useState(false);
  const [formData, setFormData] = useState({
    title: '',
    message: '',
    severity: 'high',
    target_area: '',
    target_categories: [] as string[],
    send_email: true,
    send_sms: false,
    send_push: true
  });

  const severityOptions = [
    { value: 'low', label: 'Low Priority', color: 'bg-blue-100 text-blue-800' },
    { value: 'medium', label: 'Medium Priority', color: 'bg-yellow-100 text-yellow-800' },
    { value: 'high', label: 'High Priority', color: 'bg-orange-100 text-orange-800' },
    { value: 'critical', label: 'Critical Emergency', color: 'bg-red-100 text-red-800' }
  ];

  const categoryOptions = [
    'Natural Disaster',
    'Medical Emergency',
    'Security Alert',
    'Traffic Emergency',
    'Weather Alert',
    'Public Safety',
    'Infrastructure',
    'Community Notice'
  ];

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!formData.title.trim() || !formData.message.trim()) {
      toast.error('Please fill in all required fields');
      return;
    }

    if (!formData.send_email && !formData.send_sms && !formData.send_push) {
      toast.error('Please select at least one delivery method');
      return;
    }

    setLoading(true);
    try {
      const response = await api.post('/admin/emergency-broadcast', formData);
      
      toast.success(`Emergency broadcast sent to ${response.data.recipients_count} users!`);
      onClose();
    } catch (error: any) {
      toast.error(error.response?.data?.message || 'Failed to send emergency broadcast');
    } finally {
      setLoading(false);
    }
  };

  const handleCategoryChange = (category: string, checked: boolean) => {
    setFormData(prev => ({
      ...prev,
      target_categories: checked
        ? [...prev.target_categories, category]
        : prev.target_categories.filter(c => c !== category)
    }));
  };

  const getEstimatedRecipients = () => {
    // This would typically come from an API call
    // For now, showing a mock calculation
    let baseUsers = 1000;
    if (formData.target_area) baseUsers = Math.floor(baseUsers * 0.3);
    if (formData.target_categories.length > 0) baseUsers = Math.floor(baseUsers * 0.5);
    return baseUsers;
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
      <div className="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {/* Header */}
        <div className="bg-red-600 text-white p-6 rounded-t-lg">
          <div className="flex items-center justify-between">
            <div className="flex items-center space-x-3">
              <AlertTriangle size={24} />
              <h2 className="text-xl font-bold">Emergency Broadcast System</h2>
            </div>
            <button
              onClick={onClose}
              className="text-white hover:text-red-200 text-2xl"
            >
              ×
            </button>
          </div>
          <p className="text-red-100 mt-2">
            Send critical alerts to all users in the system
          </p>
        </div>

        <form onSubmit={handleSubmit} className="p-6 space-y-6">
          {/* Title */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Alert Title *
            </label>
            <input
              type="text"
              value={formData.title}
              onChange={(e) => setFormData({ ...formData, title: e.target.value })}
              placeholder="e.g., Severe Weather Alert, Security Notice"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
              required
            />
          </div>

          {/* Message */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Alert Message *
            </label>
            <textarea
              value={formData.message}
              onChange={(e) => setFormData({ ...formData, message: e.target.value })}
              placeholder="Detailed message about the emergency or alert..."
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
              rows={4}
              required
            />
            <p className="text-sm text-gray-500 mt-1">
              {formData.message.length}/500 characters
            </p>
          </div>

          {/* Severity */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Severity Level
            </label>
            <div className="grid grid-cols-2 gap-2">
              {severityOptions.map((option) => (
                <label
                  key={option.value}
                  className={`
                    flex items-center p-3 border rounded-md cursor-pointer transition-colors
                    ${formData.severity === option.value
                      ? `${option.color} border-current`
                      : 'border-gray-300 hover:bg-gray-50'
                    }
                  `}
                >
                  <input
                    type="radio"
                    name="severity"
                    value={option.value}
                    checked={formData.severity === option.value}
                    onChange={(e) => setFormData({ ...formData, severity: e.target.value })}
                    className="sr-only"
                  />
                  <span className="font-medium">{option.label}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Target Area */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Target Area (Optional)
            </label>
            <input
              type="text"
              value={formData.target_area}
              onChange={(e) => setFormData({ ...formData, target_area: e.target.value })}
              placeholder="e.g., Downtown, University District, City-wide"
              className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
            />
            <p className="text-sm text-gray-500 mt-1">
              Leave blank to send to all users
            </p>
          </div>

          {/* Target Categories */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Target Categories (Optional)
            </label>
            <div className="grid grid-cols-2 gap-2">
              {categoryOptions.map((category) => (
                <label
                  key={category}
                  className="flex items-center space-x-2 p-2 border border-gray-200 rounded-md hover:bg-gray-50"
                >
                  <input
                    type="checkbox"
                    checked={formData.target_categories.includes(category)}
                    onChange={(e) => handleCategoryChange(category, e.target.checked)}
                    className="text-red-600 focus:ring-red-500"
                  />
                  <span className="text-sm">{category}</span>
                </label>
              ))}
            </div>
          </div>

          {/* Delivery Methods */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Delivery Methods
            </label>
            <div className="space-y-2">
              <label className="flex items-center space-x-3 p-3 border border-gray-200 rounded-md">
                <input
                  type="checkbox"
                  checked={formData.send_push}
                  onChange={(e) => setFormData({ ...formData, send_push: e.target.checked })}
                  className="text-red-600 focus:ring-red-500"
                />
                <Bell size={20} className="text-gray-600" />
                <div>
                  <span className="font-medium">Push Notification</span>
                  <p className="text-sm text-gray-500">Instant in-app notification</p>
                </div>
              </label>

              <label className="flex items-center space-x-3 p-3 border border-gray-200 rounded-md">
                <input
                  type="checkbox"
                  checked={formData.send_email}
                  onChange={(e) => setFormData({ ...formData, send_email: e.target.checked })}
                  className="text-red-600 focus:ring-red-500"
                />
                <Mail size={20} className="text-gray-600" />
                <div>
                  <span className="font-medium">Email Alert</span>
                  <p className="text-sm text-gray-500">Send to user email addresses</p>
                </div>
              </label>

              <label className="flex items-center space-x-3 p-3 border border-gray-200 rounded-md">
                <input
                  type="checkbox"
                  checked={formData.send_sms}
                  onChange={(e) => setFormData({ ...formData, send_sms: e.target.checked })}
                  className="text-red-600 focus:ring-red-500"
                />
                <MessageSquare size={20} className="text-gray-600" />
                <div>
                  <span className="font-medium">SMS Alert</span>
                  <p className="text-sm text-gray-500">Send to verified phone numbers</p>
                </div>
              </label>
            </div>
          </div>

          {/* Estimated Recipients */}
          <div className="bg-gray-50 p-4 rounded-md">
            <div className="flex items-center space-x-2 text-gray-700">
              <Users size={20} />
              <span className="font-medium">
                Estimated Recipients: {getEstimatedRecipients().toLocaleString()} users
              </span>
            </div>
          </div>

          {/* Warning */}
          <div className="bg-yellow-50 border border-yellow-200 p-4 rounded-md">
            <div className="flex items-start space-x-2">
              <AlertTriangle size={20} className="text-yellow-600 mt-0.5" />
              <div className="text-yellow-800">
                <p className="font-medium">Important Notice</p>
                <p className="text-sm mt-1">
                  This will send an emergency alert to all selected users. 
                  Please ensure the information is accurate and necessary before sending.
                </p>
              </div>
            </div>
          </div>

          {/* Actions */}
          <div className="flex space-x-3 pt-4 border-t">
            <button
              type="button"
              onClick={onClose}
              className="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-3 rounded-md font-medium transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              disabled={loading}
              className="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-md font-medium disabled:opacity-50 transition-colors flex items-center justify-center space-x-2"
            >
              <Send size={20} />
              <span>{loading ? 'Sending...' : 'Send Emergency Alert'}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  );
};

export default EmergencyBroadcast;
