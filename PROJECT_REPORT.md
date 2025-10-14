# Prokash Alert System - Project Report

## Executive Summary

**Prokash Alert System** is a community-driven alert platform for sharing real-time local updates about traffic, emergencies, crime, market prices, and community events. Built with Laravel (backend) and React (frontend), it features AI-powered content moderation, reputation-based trust system, and location-based emergency notifications.

---

## Table of Contents
1. [Technology Stack](#technology-stack)
2. [System Architecture](#system-architecture)
3. [Database Schema](#database-schema)
4. [Core Features](#core-features)
5. [API Endpoints](#api-endpoints)
6. [Installation Guide](#installation-guide)
7. [Feature Documentation](#feature-documentation)

---

## Technology Stack

### Backend
- **Framework**: Laravel 12.0 (PHP 8.2+)
- **Authentication**: Laravel Sanctum (Token-based)
- **Database**: MySQL
- **API**: RESTful Architecture

### Frontend
- **Framework**: React 19.1.1 with TypeScript 5.9.3
- **Build Tool**: Vite (Rolldown)
- **Styling**: TailwindCSS 3.4.18
- **HTTP Client**: Axios 1.12.2
- **Routing**: React Router DOM 7.9.3
- **Maps**: React Leaflet 5.0.0
- **UI Icons**: Lucide React
- **Notifications**: React Hot Toast

### External APIs
- **Google Gemini AI**: Content categorization, fake news detection, sentiment analysis
- **OpenWeather API**: Automatic weather alerts
- **OpenStreetMap**: Location services and traffic data

---

## System Architecture

```
Frontend (React + TypeScript)
    ↓ HTTP/REST API
Backend (Laravel + PHP)
    ↓ Database Queries
MySQL Database
    ↓ External Calls
External APIs (Gemini AI, OpenWeather, OSM)
```

**Design Patterns**: MVC, Repository Pattern, Middleware Pattern, Observer Pattern

---

## Database Schema

### Core Tables

**users** - User accounts and reputation
- id, name, email, password, email_verification_code, email_verified_at
- role (user/admin), is_active, trust_score (0-100)
- reputation_level (bronze/silver/gold/platinum/diamond)
- badges (JSON array)

**posts** - Community alerts and posts
- id, user_id, category_id, title, content
- image, video, latitude, longitude, location_name
- priority (low/medium/high/emergency)
- status (active/resolved/closed)
- is_verified, verification_score
- upvotes, downvotes

**categories** - Post categories
- id, name, slug, description, icon, color
- Default: Traffic, Crime & Safety, Price Updates, Lost & Found, Local Services, Emergency Alerts

**comments** - Nested comment system
- id, post_id, user_id, parent_id, content
- Supports unlimited nesting depth

**post_votes** - Upvote/downvote system
- id, post_id, user_id, vote_type (upvote/downvote)
- Unique constraint: (post_id, user_id)

**likes** - Simple like system
- id, post_id, user_id
- Unique constraint: (post_id, user_id)

**post_verifications** - Community verification
- id, post_id, user_id
- Unique constraint: (post_id, user_id)

**post_reports** - Spam/abuse reporting
- id, post_id, user_id, reason, description
- status (pending/reviewed/resolved)
- reviewed_by, reviewed_at

**notifications** - User notifications
- id, user_id, type, title, message, data (JSON)
- read_at

**post_updates** - Status updates for posts
- id, post_id, user_id, content

**community_responses** - Help offers
- id, post_id, user_id, response_type, content
- contact_info, status (pending/accepted/completed/declined)

**emergency_broadcasts** - Admin emergency alerts
- id, admin_id, title, message, priority
- target_area, latitude, longitude, radius_km
- expires_at

**tags** - Flexible tagging
- id, name, slug

**post_tag** - Post-tag relationship
- post_id, tag_id

---

## Core Features

### 1. Authentication System

**Registration Flow:**
1. User provides name, email, password
2. System generates 6-digit OTP (valid 10 minutes)
3. OTP sent to email
4. User verifies OTP to activate account
5. Can resend OTP if expired

**Login:**
- Email/password authentication
- Laravel Sanctum token generation
- Token-based API access

**Roles:**
- **User**: Create posts, comment, vote, report
- **Admin**: Full access + moderation tools

### 2. Post Management

**Create Post:**
- Title, content, category (required)
- Priority: low/medium/high/emergency
- Location: lat/long, location name
- Media: image or video upload
- Tags: multiple tags

**Post Lifecycle:**
- Active → Resolved → Closed

**Post Features:**
- Upvote/downvote voting
- Community verification
- Nested comments
- Like/unlike
- Spam reporting
- Status updates

### 3. Voting System

- One vote per user per post
- Vote types: upvote/downvote
- Can change or remove vote
- Affects author reputation
- Influences post ranking

### 4. Comment System

- Unlimited nesting depth
- Parent-child relationships
- Edit capability
- Threaded discussions
- Real-time updates

### 5. Verification System

- Users verify post authenticity
- One verification per user per post
- Verification count displayed
- Boosts author reputation
- Increases post credibility

### 6. Reporting & Moderation

**Report Types:**
- Spam, Fake News, Inappropriate, Duplicate, Other

**Workflow:**
1. User submits report
2. Admins notified
3. Post creator notified
4. Admin reviews
5. Action taken (remove/warn/dismiss)
6. Reporter notified

**Admin Tools:**
- View all reports
- Delete posts/users
- User activity history
- Analytics dashboard

### 7. Reputation & Badge System

**Trust Score (0-100):**

*Positive Factors:*
- Email verification: +10
- Creating posts: +2 each
- Receiving upvotes: +1 each
- Receiving verifications: +3 each
- Helpful comments: +1 each
- Accepted responses: +5 each

*Negative Factors:*
- Receiving downvotes: -2 each
- Spam reports: -10 each
- Deleted posts: -5 each

**Reputation Levels:**
- 🥉 Bronze (0-39)
- 🥈 Silver (40-59)
- 🥇 Gold (60-74)
- 💎 Platinum (75-89)
- 💠 Diamond (90-100)

**Badge Categories:**

*Basic:* Verified User, Member, Veteran, Legend

*Contribution:* Contributor (10+ posts), Active Contributor (50+), Super Contributor (100+)

*Trust:* Trusted Reporter (5+ verified), Expert Reporter (20+), Trusted Member (80+ score), Exemplary Citizen (95+)

*Emergency:* First Responder (3+ alerts), Emergency Expert (10+)

*Community:* Community Favorite (25+ upvotes), Highly Valued (100+), Community Helper (5+ accepted), Guardian Angel (20+)

*Engagement:* Conversationalist (50+ comments), Discussion Leader (200+)

*Quality:* Clean Record (10+ posts, no spam)

### 8. Notification System

**Types:**
- Post interactions (comments, likes, votes, verifications)
- Reputation changes (badges, level ups)
- Moderation (reports, removals)
- Admin alerts

**Features:**
- Real-time delivery
- Unread count
- Mark as read/unread
- Mark all as read
- Delete notifications
- Rich data (links, context)

### 9. AI Integration

**Google Gemini AI:**

1. **Auto-Categorization**
   - Analyzes title and content
   - Suggests category
   - Fallback to manual

2. **Fake News Detection**
   - Suspicion score (0-100)
   - Reasoning provided
   - Flags misinformation

3. **Sentiment Analysis**
   - Sentiment: positive/negative/neutral/urgent
   - Emotion: calm/worried/angry/helpful/panicked
   - Confidence score

4. **Post Suggestions**
   - 3 improvement suggestions
   - Enhances content quality

**Weather Alerts (OpenWeather):**
- Heavy rain (>5mm/hour)
- Extreme heat (>38°C)
- Cold waves (<10°C)
- High humidity (>85%)

**Traffic Alerts (OpenStreetMap):**
- Monitors major roads
- Auto-generates alerts
- Location-based

### 10. Location Features

- Interactive maps (Leaflet)
- Geocoding support
- Location-based filtering
- Visual post markers
- Emergency broadcast radius

### 11. Analytics

**User Stats:**
- Posts, comments, upvotes
- Verifications received
- Reputation history
- Badge collection

**Admin Dashboard:**
- Total users/posts
- Active emergencies
- Pending reports
- Top contributors
- Category distribution
- Reputation distribution

---

## API Endpoints

### Base URL: `http://localhost:8000/api`

### Public Endpoints

**Authentication:**
- `POST /register` - Register new user
- `POST /verify-registration` - Verify email OTP
- `POST /resend-registration-otp` - Resend OTP
- `POST /login` - Login

**Posts:**
- `GET /posts` - List posts (pagination, filters)
- `GET /posts/{id}` - Get post details
- `GET /categories` - List categories
- `GET /tags` - List tags
- `GET /reputation/leaderboard` - Top users

**AI:**
- `POST /ai/categorize` - Auto-categorize post
- `POST /ai/detect-fake-news` - Detect suspicious content
- `POST /ai/analyze-sentiment` - Analyze sentiment
- `POST /ai/suggestions` - Get improvement suggestions

### Protected Endpoints (Require Auth Token)

**User:**
- `POST /logout` - Logout
- `GET /me` - Get current user
- `PUT /profile` - Update profile

**Posts:**
- `POST /posts` - Create post
- `PUT /posts/{id}` - Update post
- `DELETE /posts/{id}` - Delete post
- `POST /posts/{id}/updates` - Add status update

**Voting:**
- `POST /posts/{id}/vote` - Vote (upvote/downvote)
- `GET /posts/{id}/user-vote` - Get user's vote

**Comments:**
- `GET /posts/{postId}/comments` - List comments
- `POST /posts/{postId}/comments` - Create comment
- `PUT /posts/{postId}/comments/{id}` - Update comment

**Interactions:**
- `POST /posts/{postId}/like` - Toggle like
- `GET /posts/{postId}/like/check` - Check if liked
- `POST /posts/{id}/verify` - Toggle verification
- `GET /posts/{id}/verify/check` - Check if verified

**Reporting:**
- `POST /posts/{id}/report` - Report post

**Notifications:**
- `GET /notifications` - List notifications
- `POST /notifications/{id}/read` - Mark as read
- `DELETE /notifications/{id}` - Delete notification
- `GET /notifications/unread-count` - Unread count
- `POST /notifications/mark-all-read` - Mark all read

**Reputation:**
- `GET /reputation` - Get user reputation
- `GET /reputation/{userId}` - Get user reputation by ID

**Community:**
- `POST /posts/{id}/responses` - Create help offer
- `GET /posts/{id}/responses` - List responses
- `PUT /posts/{postId}/responses/{responseId}` - Update response
- `GET /community/my-offers` - My help offers

### Admin Endpoints (Require Admin Role)

**User Management:**
- `GET /admin/users` - List users
- `PUT /admin/users/{id}` - Update user
- `DELETE /admin/users/{id}` - Delete user

**Post Management:**
- `GET /admin/posts` - List all posts
- `DELETE /admin/posts/{postId}` - Delete post

**Reports:**
- `GET /admin/reports` - List reports
- `PUT /admin/reports/{reportId}/status` - Update report status

**Emergency:**
- `POST /admin/emergency-broadcast` - Create broadcast
- `GET /admin/emergency-broadcasts` - List broadcasts
- `GET /admin/emergency-broadcasts/{id}` - Get broadcast
- `GET /admin/emergency-broadcast-stats` - Broadcast stats

**AI:**
- `POST /admin/ai/generate-weather-alerts` - Generate weather alerts
- `POST /admin/ai/generate-traffic-alerts` - Generate traffic alerts
- `GET /admin/ai/batch-analyze` - Batch analyze posts

**Analytics:**
- `GET /admin/stats` - System statistics
- `GET /admin/analytics/dashboard` - Analytics dashboard
- `GET /admin/analytics/community-stats` - Community stats
- `GET /admin/reputation-stats` - Reputation statistics
- `GET /admin/notification-stats` - Notification statistics

**Reputation:**
- `PUT /admin/users/{id}/reputation` - Update user reputation

**Notifications:**
- `GET /admin/notifications` - Admin notifications
- `GET /admin/notification-stats` - Notification stats

---

## Installation Guide

### Prerequisites
- PHP 8.2+
- Composer
- MySQL
- Node.js 18+
- npm/yarn
- XAMPP (or similar)

### Backend Setup

1. **Clone Repository**
```bash
cd /Applications/XAMPP/xamppfiles/htdocs
git clone <repository-url> Prokash_SWE_project
cd Prokash_SWE_project/backend
```

2. **Install Dependencies**
```bash
composer install
```

3. **Environment Configuration**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure .env**
```env
APP_NAME=Prokash
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=prokash
DB_USERNAME=root
DB_PASSWORD=

FRONTEND_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost:5173

# Optional: AI Features
GEMINI_API_KEY=your_gemini_api_key
OPENWEATHER_API_KEY=your_openweather_key
```

5. **Database Setup**
```bash
# Create database
mysql -u root -p
CREATE DATABASE prokash;
EXIT;

# Run migrations
php artisan migrate

# Seed categories (if seeder exists)
php artisan db:seed
```

6. **Start Backend Server**
```bash
php artisan serve
# Runs on http://localhost:8000
```

### Frontend Setup

1. **Navigate to Frontend**
```bash
cd ../frontend
```

2. **Install Dependencies**
```bash
npm install
```

3. **Configure API URL**
Edit `src/services/api.ts`:
```typescript
const API_URL = 'http://localhost:8000/api';
```

4. **Start Development Server**
```bash
npm run dev
# Runs on http://localhost:5173
```

### Default Admin Account

Create admin manually:
```bash
php artisan tinker
```
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@prokash.com';
$user->password = Hash::make('admin123');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
```

### Testing the System

1. **Register New User**
   - Go to http://localhost:5173/register
   - Fill registration form
   - Check console for OTP (if using log mailer)
   - Verify email with OTP

2. **Create Post**
   - Login
   - Click "Create Alert"
   - Fill post details
   - Upload image (optional)
   - Submit

3. **Test Features**
   - Upvote/downvote posts
   - Add comments
   - Verify posts
   - Report spam
   - Check notifications
   - View reputation

---

## Feature Documentation

### How Reputation System Works

1. **Initial State**
   - New user starts with trust_score = 0
   - Reputation level = "bronze"
   - Badges = []

2. **Score Calculation Triggers**
   - Creating a post
   - Receiving votes
   - Getting verifications
   - Receiving reports
   - Post deletion

3. **Automatic Updates**
   - ReputationService calculates new score
   - Determines new level
   - Calculates earned badges
   - Compares with previous state
   - Sends notifications if changed

4. **Badge Assignment**
   - Checks all badge criteria
   - Awards applicable badges
   - Stores in user.badges JSON
   - Notifies user of new badges

### How Voting Works

1. **User Clicks Vote Button**
   - Frontend sends POST /posts/{id}/vote
   - vote_type: "upvote" or "downvote"

2. **Backend Processing**
   - Checks if user already voted
   - If same vote: removes vote
   - If different vote: updates vote
   - If no vote: creates new vote

3. **Post Update**
   - Recalculates upvotes count
   - Recalculates downvotes count
   - Updates post record

4. **Reputation Update**
   - Triggers ReputationService
   - Updates post author's score
   - Checks for badge eligibility

### How Notifications Work

1. **Event Triggers**
   - User action (comment, vote, verify)
   - System event (badge earned, report reviewed)
   - Admin action (post deleted, user warned)

2. **Notification Creation**
   - NotificationController creates record
   - Stores: user_id, type, title, message, data
   - read_at = null (unread)

3. **User Retrieval**
   - Frontend polls GET /notifications
   - Shows unread count
   - Displays in notification panel

4. **Mark as Read**
   - User clicks notification
   - Frontend sends POST /notifications/{id}/read
   - Updates read_at timestamp

### How AI Categorization Works

1. **User Creates Post**
   - Enters title and content
   - Optionally requests AI suggestion

2. **Frontend Request**
   - Sends POST /ai/categorize
   - Includes title and content

3. **Backend Processing**
   - AIService calls Gemini API
   - Sends prompt with categories
   - Receives category suggestion

4. **Response**
   - Returns category_id
   - Frontend auto-selects category
   - User can override

### How Reporting Works

1. **User Reports Post**
   - Clicks "Report" button
   - Selects reason
   - Adds description

2. **Report Creation**
   - Creates post_reports record
   - Status = "pending"

3. **Notifications Sent**
   - All admins notified
   - Post creator notified
   - Includes report details

4. **Admin Review**
   - Admin views report
   - Updates status to "reviewed"
   - Takes action (delete/warn/dismiss)

5. **Resolution**
   - Status = "resolved"
   - Reporter notified of outcome
   - Post creator notified if actioned

### How Emergency Broadcasts Work

1. **Admin Creates Broadcast**
   - Fills: title, message, priority
   - Sets: target area, radius, expiration

2. **Broadcast Storage**
   - Stored in emergency_broadcasts table
   - Includes lat/long and radius_km

3. **Display**
   - Frontend queries active broadcasts
   - Shows in prominent location
   - Filters by user location (optional)

4. **Expiration**
   - Broadcasts expire automatically
   - Removed from active display
   - Archived for records

---

## Security Features

1. **Authentication**
   - Password hashing (bcrypt)
   - Token-based API auth (Sanctum)
   - Email verification required
   - OTP expiration (10 minutes)

2. **Authorization**
   - Role-based access control
   - Middleware protection
   - Owner-only edit/delete
   - Admin-only endpoints

3. **Input Validation**
   - Laravel validation rules
   - SQL injection prevention
   - XSS protection
   - CSRF protection

4. **Rate Limiting**
   - API rate limiting
   - Login attempt limiting
   - OTP resend cooldown

5. **Data Protection**
   - Password hashing
   - Token encryption
   - Sensitive data hiding
   - CORS configuration

---

## Project Structure

### Backend Structure
```
backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── PostController.php
│   │   │   ├── CommentController.php
│   │   │   ├── VoteController.php
│   │   │   ├── ReportController.php
│   │   │   ├── NotificationController.php
│   │   │   ├── ReputationController.php
│   │   │   ├── AIController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/
│   │       └── AdminMiddleware.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Post.php
│   │   ├── Comment.php
│   │   ├── PostVote.php
│   │   ├── Like.php
│   │   ├── PostVerification.php
│   │   ├── PostReport.php
│   │   ├── Notification.php
│   │   └── ...
│   └── Services/
│       ├── ReputationService.php
│       └── AIService.php
├── database/
│   └── migrations/
├── routes/
│   └── api.php
└── .env
```

### Frontend Structure
```
frontend/
├── src/
│   ├── components/
│   │   ├── Navbar.tsx
│   │   ├── PostCard.tsx
│   │   ├── CommentSection.tsx
│   │   └── ...
│   ├── pages/
│   │   ├── Home.tsx
│   │   ├── Register.tsx
│   │   ├── Login.tsx
│   │   ├── CreatePost.tsx
│   │   ├── PostDetail.tsx
│   │   └── ...
│   ├── context/
│   │   └── AuthContext.tsx
│   ├── services/
│   │   └── api.ts
│   ├── types/
│   │   └── index.ts
│   └── App.tsx
└── package.json
```

---

## Testing Results

All core features have been tested and verified:

✅ **Authentication System**
- Registration with OTP
- Email verification
- Login/logout
- Token management

✅ **Post CRUD Operations**
- Create posts with media
- Update posts
- Delete posts
- Emergency alerts

✅ **Voting System**
- Upvote/downvote
- Vote changes
- Vote removal
- Count updates

✅ **Comment System**
- Create comments
- Nested replies
- Edit comments
- Threaded display

✅ **Spam Reporting**
- Submit reports
- Admin notifications
- Creator notifications
- Report review

✅ **Reputation System**
- Score calculation
- Level assignment
- Badge awards
- Notifications

✅ **Notification System**
- Real-time delivery
- Unread count
- Mark as read
- Delete notifications

✅ **API Endpoints**
- All endpoints functional
- Proper authentication
- Correct responses
- Error handling

---

## Future Enhancements

1. **Real-time Features**
   - WebSocket integration
   - Live notifications
   - Real-time post updates

2. **Mobile Application**
   - React Native app
   - Push notifications
   - Offline support

3. **Advanced AI**
   - Image recognition
   - Video analysis
   - Automatic tagging

4. **Social Features**
   - User following
   - Private messaging
   - Groups/communities

5. **Enhanced Analytics**
   - Detailed insights
   - Trend prediction
   - Heat maps

6. **Gamification**
   - Achievements
   - Challenges
   - Rewards system

---

## Conclusion

Prokash Alert System successfully delivers a comprehensive community alert platform with robust features including AI-powered moderation, reputation-based trust system, and real-time notifications. The system is production-ready with all core features tested and functional.

**Key Achievements:**
- Complete authentication system with OTP verification
- Full CRUD operations for posts with media support
- Advanced voting and verification systems
- Nested comment system with unlimited depth
- Comprehensive reputation and badge system
- AI integration for content moderation
- Real-time notification system
- Admin moderation tools
- Location-based features
- Emergency broadcast system

**Technical Excellence:**
- Clean, maintainable code
- RESTful API design
- Secure authentication
- Scalable architecture
- Responsive UI
- Comprehensive error handling

The project demonstrates modern web development practices and provides a solid foundation for future enhancements.

---

**Project Status**: ✅ Complete and Tested
**Version**: 1.0.0
**Last Updated**: October 10, 2025
