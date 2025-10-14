# 🚨 Prokash - Community Alert & Verification System

> A comprehensive community-driven platform for real-time alerts, emergency broadcasts, and information verification in Bangladesh.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![React](https://img.shields.io/badge/React-18.x-blue.svg)](https://reactjs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-blue.svg)](https://www.typescriptlang.org)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## 📋 Table of Contents

- [About](#about)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [System Architecture](#system-architecture)
- [Installation](#installation)
- [Usage](#usage)
- [API Documentation](#api-documentation)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)

---

## 🎯 About

**Prokash** is a community-powered alert and verification system designed specifically for Bangladesh. It enables citizens to report, verify, and respond to local incidents, emergencies, and important information in real-time. The platform combines social media features with a robust verification system to ensure information accuracy and community trust.

### Problem Statement

In Bangladesh, misinformation and delayed emergency responses are critical issues. Citizens need a reliable platform to:
- Report emergencies and local incidents quickly
- Verify information through community consensus
- Receive location-based alerts
- Access trusted, real-time information

### Solution

Prokash provides a comprehensive solution with:
- **Community Verification**: Upvote/downvote system for information accuracy
- **Location-Based Alerts**: Proximity-based notification system
- **Priority Levels**: Emergency, high, medium, and low priority posts
- **Admin Moderation**: Manual verification and content moderation
- **Reputation System**: Trust scores and badges for reliable contributors

---

## ✨ Key Features

### 🔐 Authentication & User Management
- **JWT-based Authentication**: Secure token-based authentication
- **Role-Based Access Control**: User and Admin roles
- **User Profiles**: Customizable profiles with reputation tracking
- **Trust Score System**: Reputation-based user credibility

### 📝 Post Management
- **Create Posts**: Text, images, and video support
- **Location Tagging**: GPS coordinates and location names
- **Priority Levels**: Emergency, high, medium, low
- **Categories**: Traffic, Crime, Emergency, Weather, etc.
- **Media Upload**: Image and video attachments
- **Post Editing**: Update and delete own posts

### ✅ Community Verification System
- **Upvote/Downvote**: Community-driven verification
- **Verification Score**: Automatic calculation based on votes
- **Auto-Verification**: Posts verified at threshold (3+ net upvotes)
- **Verification Badge**: Visual indicator for verified posts
- **Reputation Updates**: Contributors earn reputation points

### 👨‍💼 Admin Dashboard
- **User Management**: View, activate/deactivate, delete users
- **Post Moderation**: Manual verification and deletion
- **Statistics Dashboard**: Real-time analytics and insights
- **Emergency Monitoring**: Track emergency posts
- **Verification Rate**: Monitor community verification metrics

### 🔔 Intelligent Notification System
- **Emergency Alerts**: Broadcast to all users
- **Location-Based**: Notify users within radius
- **Priority-Based Distribution**:
  - Emergency: All users
  - High: 20km radius
  - Medium: 10km radius
  - Low: 5km radius
- **Click-to-Redirect**: Navigate to post from notification
- **Auto-Cleanup**: Notifications deleted when post is removed
- **Unread Count**: Real-time notification badge

### 📊 Analytics & Insights
- **User Statistics**: Total users, growth rate
- **Post Analytics**: Total posts, verified count, emergency count
- **Engagement Metrics**: Comments, likes, verification rate
- **Community Stats**: Active users, top contributors

### 🎨 User Interface
- **Responsive Design**: Mobile-first approach
- **Modern UI**: Clean, intuitive interface with Tailwind CSS
- **Dark Mode Ready**: Prepared for dark theme
- **Professional Cards**: Card-based layout for posts
- **Interactive Elements**: Smooth animations and transitions

---

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 10.x (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Authentication**: JWT (tymon/jwt-auth)
- **API**: RESTful API architecture
- **ORM**: Eloquent ORM
- **Validation**: Laravel Form Requests
- **Middleware**: Custom admin and auth middleware

### Frontend
- **Framework**: React 18.x with TypeScript
- **Build Tool**: Vite 5.x
- **Styling**: Tailwind CSS 3.x
- **Icons**: Lucide React
- **Routing**: React Router DOM v6
- **HTTP Client**: Axios
- **State Management**: React Context API

### Development Tools
- **Version Control**: Git & GitHub
- **Package Manager**: npm/Composer
- **Code Quality**: ESLint, TypeScript strict mode
- **API Testing**: Postman/Thunder Client

---

## 🏗️ System Architecture

### Database Schema

```
users
├─ id, name, email, password
├─ role (user/admin)
├─ trust_score, reputation_level
├─ latitude, longitude, location_name
└─ is_active, email_verified_at

posts
├─ id, user_id, category_id
├─ title, content
├─ image, video
├─ latitude, longitude, location_name
├─ priority (emergency/high/medium/low)
├─ is_verified, verification_score
├─ admin_verified, admin_verified_by
└─ upvotes, downvotes

post_verifications
├─ id, user_id, post_id
├─ type (upvote/downvote)
└─ timestamps

notifications
├─ id, user_id, post_id
├─ type, title, message
├─ action_url, redirect_url
├─ read, read_at
└─ CASCADE DELETE on post deletion

categories
├─ id, name, slug
└─ description

comments
├─ id, user_id, post_id
├─ content
└─ timestamps

likes
├─ id, user_id, post_id
└─ timestamps
```

### API Architecture

```
/api
├─ /auth
│  ├─ POST /register
│  ├─ POST /login
│  ├─ POST /logout
│  └─ GET /me
├─ /posts
│  ├─ GET /posts (with filters)
│  ├─ POST /posts
│  ├─ GET /posts/{id}
│  ├─ PUT /posts/{id}
│  └─ DELETE /posts/{id}
├─ /verification
│  ├─ POST /posts/{id}/verify
│  └─ GET /posts/{id}/verification-stats
├─ /notifications
│  ├─ GET /notifications
│  ├─ POST /notifications/{id}/read
│  ├─ GET /notifications/unread-count
│  └─ DELETE /notifications/{id}
└─ /admin (admin only)
   ├─ GET /admin/stats
   ├─ GET /admin/users
   ├─ PUT /admin/users/{id}/role
   ├─ PUT /admin/users/{id}/toggle-status
   ├─ DELETE /admin/users/{id}
   ├─ GET /admin/posts
   ├─ POST /admin/posts/{id}/verify
   └─ DELETE /admin/posts/{id}
```

---

## 📦 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and npm
- MySQL 8.0+
- Git

### Backend Setup

1. **Clone the repository**
```bash
git clone https://github.com/RafayenFarhad/Prokash.git
cd Prokash/backend
```

2. **Install dependencies**
```bash
composer install
```

3. **Environment configuration**
```bash
cp .env.example .env
```

4. **Configure database** (edit `.env`)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prokash
DB_USERNAME=root
DB_PASSWORD=
```

5. **Generate application key**
```bash
php artisan key:generate
```

6. **Generate JWT secret**
```bash
php artisan jwt:secret
```

7. **Run migrations**
```bash
php artisan migrate
```

8. **Seed database** (optional - adds sample data)
```bash
php artisan db:seed
```

9. **Start development server**
```bash
php artisan serve
```

Backend will run at: `http://localhost:8000`

### Frontend Setup

1. **Navigate to frontend directory**
```bash
cd ../frontend
```

2. **Install dependencies**
```bash
npm install
```

3. **Environment configuration**
```bash
cp .env.example .env
```

4. **Configure API URL** (edit `.env`)
```env
VITE_API_URL=http://localhost:8000/api
```

5. **Start development server**
```bash
npm run dev
```

Frontend will run at: `http://localhost:5173`

---

## 🚀 Usage

### For Users

1. **Register/Login**
   - Create an account or login
   - Verify email (if enabled)

2. **Create Posts**
   - Click "Create Post" button
   - Add title, content, category
   - Optionally add image/video
   - Set priority level
   - Add location (manual or GPS)
   - Submit

3. **Verify Posts**
   - View posts in feed
   - Click upvote (✓) to verify
   - Click downvote (✗) if inaccurate
   - Posts auto-verify at 3+ net upvotes

4. **Receive Notifications**
   - Click bell icon to view notifications
   - Click notification to view related post
   - Notifications based on location and priority

### For Admins

1. **Access Admin Dashboard**
   - Login with admin account
   - Click "Admin" in navigation

2. **Manage Users**
   - View all users
   - Toggle user/admin roles
   - Activate/deactivate accounts
   - Delete users

3. **Moderate Posts**
   - View all posts
   - Manually verify posts
   - Delete inappropriate content
   - Monitor emergency posts

4. **View Analytics**
   - Dashboard statistics
   - User growth metrics
   - Post verification rates
   - Emergency alert tracking

---

## 📚 API Documentation

### Authentication

#### Register
```http
POST /api/register
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password123"
}

Response:
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer",
  "expires_in": 3600,
  "user": { ... }
}
```

### Posts

#### Create Post
```http
POST /api/posts
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "title": "Traffic jam at Shahbag",
  "content": "Heavy traffic reported...",
  "category_id": 1,
  "priority": "high",
  "latitude": 23.7389,
  "longitude": 90.3950,
  "location_name": "Shahbag, Dhaka",
  "image": (file),
  "video": (file)
}
```

#### Get Posts
```http
GET /api/posts?category=1&priority=emergency&search=traffic
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "title": "Traffic jam at Shahbag",
      "content": "Heavy traffic...",
      "priority": "high",
      "is_verified": true,
      "verification_score": 5,
      "user": { ... },
      "category": { ... }
    }
  ],
  "meta": { ... }
}
```

### Verification

#### Verify Post
```http
POST /api/posts/{id}/verify
Authorization: Bearer {token}
Content-Type: application/json

{
  "type": "upvote"  // or "downvote"
}

Response:
{
  "message": "Verification recorded",
  "post": {
    "verification_score": 4,
    "is_verified": true
  }
}
```

### Notifications

#### Get Notifications
```http
GET /api/notifications
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "title": "🚨 Emergency Alert",
      "message": "Fire breaks out in Chawkbazar",
      "redirect_url": "/posts/123",
      "read": false,
      "created_at": "2024-10-14T10:00:00Z"
    }
  ]
}
```

---

## 📸 Screenshots

### Landing Page
![Landing Page](screenshots/landing.png)

### Post Feed
![Post Feed](screenshots/feed.png)

### Admin Dashboard
![Admin Dashboard](screenshots/admin.png)

### Post Moderation
![Post Moderation](screenshots/moderation.png)

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines

- Follow PSR-12 coding standards for PHP
- Use TypeScript for all React components
- Write meaningful commit messages
- Add tests for new features
- Update documentation

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👥 Team

- **Rafayen Farhad** - Lead Developer
- **Project Supervisor** - [Supervisor Name]
- **Institution** - [University Name]

---

## 🙏 Acknowledgments

- Laravel community for excellent documentation
- React team for the powerful framework
- Tailwind CSS for the utility-first CSS framework
- All contributors and testers

---

## 📞 Contact

- **Email**: rafayenfarhad@example.com
- **GitHub**: [@RafayenFarhad](https://github.com/RafayenFarhad)
- **Project Link**: [https://github.com/RafayenFarhad/Prokash](https://github.com/RafayenFarhad/Prokash)

---

## 🔮 Future Enhancements

- [ ] Mobile app (React Native)
- [ ] Real-time chat for emergency coordination
- [ ] AI-powered fake news detection
- [ ] Multi-language support (Bengali, English)
- [ ] SMS alerts for emergencies
- [ ] Integration with government emergency services
- [ ] Advanced analytics dashboard
- [ ] WebSocket for real-time updates

---

**Made with ❤️ in Bangladesh**
