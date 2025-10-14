import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { Toaster } from 'react-hot-toast';
import { AuthProvider } from './context/AuthContext';
import Navbar from './components/Navbar';
import ProtectedRoute from './components/ProtectedRoute';
import AdminRoute from './components/AdminRoute';
import Home from './pages/Home';
import Login from './pages/Login';
import EmailLogin from './pages/EmailLogin';
import Register from './pages/Register';
import RegistrationVerification from './pages/RegistrationVerification';
import Posts from './pages/Posts';
import PostDetail from './pages/PostDetail';
import CreatePost from './pages/CreatePost';
import Profile from './pages/Profile';
import Map from './pages/Map';
import Dashboard from './pages/Dashboard';
import AdminDashboard from './pages/admin/Dashboard';
import Users from './pages/admin/Users';
import PostsManagement from './pages/admin/PostsManagement';
import ReputationManagement from './pages/admin/ReputationManagement';
import NotificationManagement from './pages/admin/NotificationManagement';

function App() {
  return (
    <AuthProvider>
      <BrowserRouter>
        <Navbar />
        <Routes>
          <Route path="/" element={<Home />} />
          <Route path="/login" element={<Login />} />
          <Route path="/email-login" element={<EmailLogin />} />
          <Route path="/register" element={<Register />} />
          <Route path="/verify-registration" element={<RegistrationVerification />} />
          <Route path="/posts" element={<Posts />} />
          <Route path="/posts/:id" element={<PostDetail />} />
          <Route path="/map" element={<Map />} />
          
          <Route path="/dashboard" element={<ProtectedRoute><Dashboard /></ProtectedRoute>} />
          <Route path="/create-post" element={<ProtectedRoute><CreatePost /></ProtectedRoute>} />
          <Route path="/edit-post/:id" element={<ProtectedRoute><CreatePost /></ProtectedRoute>} />
          <Route path="/profile" element={<ProtectedRoute><Profile /></ProtectedRoute>} />
          
          <Route path="/admin" element={<AdminRoute><AdminDashboard /></AdminRoute>} />
          <Route path="/admin/users" element={<AdminRoute><Users /></AdminRoute>} />
          <Route path="/admin/posts" element={<AdminRoute><PostsManagement /></AdminRoute>} />
          <Route path="/admin/reputation" element={<AdminRoute><ReputationManagement /></AdminRoute>} />
          <Route path="/admin/notifications" element={<AdminRoute><NotificationManagement /></AdminRoute>} />
        </Routes>
        <Toaster 
          position="top-right"
          toastOptions={{
            duration: 4000,
            style: {
              background: '#363636',
              color: '#fff',
            },
          }}
        />
      </BrowserRouter>
    </AuthProvider>
  );
}

export default App;
