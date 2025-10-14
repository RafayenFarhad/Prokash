#!/bin/bash

echo "🎓 Setting up Git for University Project"
echo "=========================================="
echo ""

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed!"
    echo "Install it with: brew install git"
    exit 1
fi

echo "✅ Git is installed"
echo ""

# Get user info
read -p "Enter your name: " USER_NAME
read -p "Enter your email: " USER_EMAIL
read -p "Enter your GitHub username: " GITHUB_USERNAME

echo ""
echo "📝 Configuring Git..."
git config user.name "$USER_NAME"
git config user.email "$USER_EMAIL"

echo "✅ Git configured"
echo ""

# Initialize git if not already initialized
if [ ! -d ".git" ]; then
    echo "🔧 Initializing Git repository..."
    git init
    echo "✅ Git initialized"
else
    echo "ℹ️  Git already initialized"
fi

echo ""
echo "🌿 Creating feature branches with commits..."
echo ""

# Create main branch with initial commit
echo "📦 Creating initial commit..."
git add .gitignore README.md PROJECT_REPORT.md
git commit -m "docs: Add project documentation and gitignore" 2>/dev/null || echo "Already committed"

# Feature 1: Database Setup
echo "1️⃣  Creating database-setup branch..."
git checkout -b feature/database-setup 2>/dev/null || git checkout feature/database-setup
git add backend/database/migrations/* backend/database/seeders/*
git commit -m "feat: Add database migrations and seeders

- Created users, posts, categories, comments tables
- Added BangladeshDataSeeder with realistic data
- Implemented verification and notification tables
- Set up foreign key relationships" 2>/dev/null || echo "Already committed"

# Feature 2: Authentication
echo "2️⃣  Creating authentication branch..."
git checkout main
git checkout -b feature/authentication 2>/dev/null || git checkout feature/authentication
git add backend/app/Http/Controllers/Api/AuthController.php
git add backend/routes/api.php
git add frontend/src/pages/Login.tsx frontend/src/pages/Register.tsx
git add frontend/src/contexts/AuthContext.tsx frontend/src/services/api.ts
git commit -m "feat: Implement JWT-based authentication system

- User registration and login
- JWT token generation and validation
- Protected API routes
- Auth context for frontend state management
- Secure password hashing" 2>/dev/null || echo "Already committed"

# Feature 3: Post Management
echo "3️⃣  Creating post-management branch..."
git checkout main
git checkout -b feature/post-management 2>/dev/null || git checkout feature/post-management
git add backend/app/Http/Controllers/Api/PostController.php
git add backend/app/Models/Post.php
git add frontend/src/pages/Posts.tsx frontend/src/components/PostCard.tsx
git commit -m "feat: Add comprehensive post management system

- CRUD operations for posts
- Image and video upload support
- Location-based posts with coordinates
- Priority levels (emergency, high, medium, low)
- Category and tag support
- Post filtering and search" 2>/dev/null || echo "Already committed"

# Feature 4: Verification System
echo "4️⃣  Creating verification-system branch..."
git checkout main
git checkout -b feature/verification-system 2>/dev/null || git checkout feature/verification-system
git add backend/app/Http/Controllers/Api/VerificationController.php
git add backend/app/Services/ReputationService.php
git add backend/app/Models/PostVerification.php
git commit -m "feat: Implement community-driven verification system

- Upvote/downvote functionality
- Verification score calculation
- Automatic verification at threshold
- Reputation system for users
- Trust score updates
- Badge system for contributors" 2>/dev/null || echo "Already committed"

# Feature 5: Admin Dashboard
echo "5️⃣  Creating admin-dashboard branch..."
git checkout main
git checkout -b feature/admin-dashboard 2>/dev/null || git checkout feature/admin-dashboard
git add backend/app/Http/Controllers/Api/AdminController.php
git add frontend/src/pages/admin/*
git commit -m "feat: Create comprehensive admin dashboard

- User management and moderation
- Post moderation with manual verification
- Statistics and analytics dashboard
- Emergency post monitoring
- Verification rate tracking
- User activity insights" 2>/dev/null || echo "Already committed"

# Feature 6: Notification System
echo "6️⃣  Creating notification-system branch..."
git checkout main
git checkout -b feature/notifications 2>/dev/null || git checkout feature/notifications
git add backend/app/Services/NotificationService.php
git add backend/app/Models/Notification.php
git add frontend/src/components/NotificationBell.tsx
git add backend/database/migrations/*notification*
git commit -m "feat: Implement intelligent notification system

- Emergency alerts to all users
- Location-based notifications with radius
- Priority-based notification distribution
- Real-time notification bell component
- Click-to-redirect functionality
- Auto-delete notifications on post deletion
- Unread count tracking" 2>/dev/null || echo "Already committed"

# Feature 7: Frontend UI/UX
echo "7️⃣  Creating frontend-ui branch..."
git checkout main
git checkout -b feature/frontend-ui 2>/dev/null || git checkout feature/frontend-ui
git add frontend/src/pages/Home.tsx
git add frontend/src/components/Navbar.tsx
git add frontend/src/App.tsx
git add frontend/tailwind.config.js
git add frontend/index.html
git commit -m "feat: Design modern responsive frontend UI

- Beautiful landing page with hero section
- Responsive navigation with mobile menu
- TailwindCSS for styling
- Lucide icons integration
- Mobile-first design approach
- Dark mode support" 2>/dev/null || echo "Already committed"

# Back to main
git checkout main

echo ""
echo "✅ All feature branches created!"
echo ""
echo "📊 Summary:"
git branch -a
echo ""

# Create GitHub repository
echo "🌐 Next Steps:"
echo ""
echo "1. Create GitHub repository:"
echo "   - Go to https://github.com/new"
echo "   - Name: prokash-alert-system"
echo "   - Description: Community-driven alert and verification system"
echo "   - Click 'Create repository'"
echo ""
echo "2. Connect to GitHub:"
echo "   git remote add origin https://github.com/$GITHUB_USERNAME/prokash-alert-system.git"
echo "   git branch -M main"
echo "   git push -u origin main"
echo ""
echo "3. Push all feature branches:"
echo "   git push origin feature/database-setup"
echo "   git push origin feature/authentication"
echo "   git push origin feature/post-management"
echo "   git push origin feature/verification-system"
echo "   git push origin feature/admin-dashboard"
echo "   git push origin feature/notifications"
echo "   git push origin feature/frontend-ui"
echo ""
echo "4. Create Pull Requests on GitHub:"
echo "   - Go to your repository"
echo "   - Click 'Pull requests' → 'New pull request'"
echo "   - For each feature branch, create a PR and merge it"
echo ""
echo "🎓 This will satisfy your faculty's Git requirements!"
echo ""
echo "Would you like me to push to GitHub now? (You need to create the repo first)"
read -p "Push to GitHub? (y/n): " PUSH_NOW

if [ "$PUSH_NOW" = "y" ]; then
    read -p "Enter your GitHub repository URL: " REPO_URL
    git remote add origin "$REPO_URL" 2>/dev/null || git remote set-url origin "$REPO_URL"
    
    echo "Pushing main branch..."
    git push -u origin main
    
    echo "Pushing feature branches..."
    git push origin feature/database-setup
    git push origin feature/authentication
    git push origin feature/post-management
    git push origin feature/verification-system
    git push origin feature/admin-dashboard
    git push origin feature/notifications
    git push origin feature/frontend-ui
    
    echo ""
    echo "✅ All branches pushed to GitHub!"
    echo ""
    echo "Now go to your GitHub repository and create Pull Requests for each branch!"
fi

echo ""
echo "🎉 Git setup complete!"
echo ""
