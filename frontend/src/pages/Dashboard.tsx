import React from 'react';
import { useAuth } from '../context/AuthContext';
import { Link } from 'react-router-dom';

const Dashboard: React.FC = () => {
  const { user } = useAuth();

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {/* Welcome Header */}
        <div className="bg-white rounded-lg shadow-sm p-6 mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Welcome to Prokash, {user?.name}! 🎉
          </h1>
          <p className="text-gray-600">
            Your account has been successfully verified. You can now access all features of the Prokash Alert System.
          </p>
        </div>

        {/* Quick Actions */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          <Link
            to="/create-post"
            className="bg-blue-600 hover:bg-blue-700 text-white p-6 rounded-lg shadow-sm transition-colors"
          >
            <div className="flex items-center">
              <svg className="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4v16m8-8H4" />
              </svg>
              <div>
                <h3 className="text-lg font-semibold">Create Alert</h3>
                <p className="text-blue-100">Report a new incident</p>
              </div>
            </div>
          </Link>

          <Link
            to="/posts"
            className="bg-green-600 hover:bg-green-700 text-white p-6 rounded-lg shadow-sm transition-colors"
          >
            <div className="flex items-center">
              <svg className="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
              </svg>
              <div>
                <h3 className="text-lg font-semibold">View Alerts</h3>
                <p className="text-green-100">Browse community alerts</p>
              </div>
            </div>
          </Link>

          <Link
            to="/map"
            className="bg-purple-600 hover:bg-purple-700 text-white p-6 rounded-lg shadow-sm transition-colors"
          >
            <div className="flex items-center">
              <svg className="w-8 h-8 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
              </svg>
              <div>
                <h3 className="text-lg font-semibold">Map View</h3>
                <p className="text-purple-100">See alerts on map</p>
              </div>
            </div>
          </Link>
        </div>

        {/* Features Overview */}
        <div className="bg-white rounded-lg shadow-sm p-6">
          <h2 className="text-xl font-bold text-gray-900 mb-4">
            🚀 Available Features
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div className="flex items-start space-x-3">
              <span className="text-2xl">📧</span>
              <div>
                <h3 className="font-semibold text-gray-900">Email OTP Authentication</h3>
                <p className="text-gray-600 text-sm">Secure login with email verification</p>
              </div>
            </div>
            <div className="flex items-start space-x-3">
              <span className="text-2xl">📸</span>
              <div>
                <h3 className="font-semibold text-gray-900">Multiple Images Support</h3>
                <p className="text-gray-600 text-sm">Upload up to 10 images per alert</p>
              </div>
            </div>
            <div className="flex items-start space-x-3">
              <span className="text-2xl">🗺️</span>
              <div>
                <h3 className="font-semibold text-gray-900">Interactive Map</h3>
                <p className="text-gray-600 text-sm">View alerts geographically</p>
              </div>
            </div>
            <div className="flex items-start space-x-3">
              <span className="text-2xl">🤝</span>
              <div>
                <h3 className="font-semibold text-gray-900">Community Response</h3>
                <p className="text-gray-600 text-sm">"I Can Help" system for assistance</p>
              </div>
            </div>
            <div className="flex items-start space-x-3">
              <span className="text-2xl">🏆</span>
              <div>
                <h3 className="font-semibold text-gray-900">Reputation System</h3>
                <p className="text-gray-600 text-sm">Earn trust scores and badges</p>
              </div>
            </div>
            <div className="flex items-start space-x-3">
              <span className="text-2xl">🚨</span>
              <div>
                <h3 className="font-semibold text-gray-900">Emergency Alerts</h3>
                <p className="text-gray-600 text-sm">Real-time emergency notifications</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;
