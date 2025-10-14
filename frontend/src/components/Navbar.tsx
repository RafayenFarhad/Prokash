import { Link } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import { LogOut, User, LayoutDashboard, Map, FileText } from 'lucide-react';
import NotificationCenter from './NotificationCenter';

export default function Navbar() {
  const { user, logout, isAuthenticated, isAdmin } = useAuth();

  return (
    <nav className="bg-white shadow-lg">
      <div className="container mx-auto px-4">
        <div className="flex justify-between items-center h-16">
          <Link to="/" className="text-2xl font-bold text-blue-600">
            Prokash
          </Link>

          <div className="flex items-center space-x-6">
            {isAuthenticated ? (
              <>
                <Link to="/posts" className="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                  <FileText size={18} />
                  <span>Posts</span>
                </Link>
                <Link to="/map" className="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                  <Map size={18} />
                  <span>Map</span>
                </Link>
                <Link to="/create-post" className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                  Create Alert
                </Link>
                <NotificationCenter />
                {isAdmin && (
                  <Link to="/admin" className="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                    <LayoutDashboard size={18} />
                    <span>Admin</span>
                  </Link>
                )}
                <Link to="/profile" className="flex items-center space-x-1 text-gray-700 hover:text-blue-600">
                  <User size={18} />
                  <span>{user?.name}</span>
                </Link>
                <button
                  onClick={logout}
                  className="flex items-center space-x-1 text-red-600 hover:text-red-700"
                >
                  <LogOut size={18} />
                  <span>Logout</span>
                </button>
              </>
            ) : (
              <>
                <Link to="/posts" className="text-gray-700 hover:text-blue-600">
                  Posts
                </Link>
                <Link to="/login" className="text-gray-700 hover:text-blue-600">
                  Login
                </Link>
                <Link to="/register" className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                  Register
                </Link>
              </>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
}
