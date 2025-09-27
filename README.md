# Prokash - Community Alerts Platform

A comprehensive community-powered social media platform for real-time local alerts and community support.

## Features

### Core Functionality
- **User Authentication**: Email, phone OTP, and social login options
- **Real-time Post Creation**: Text, images, videos, and GPS location support
- **Community Verification**: Voting system to ensure information accuracy
- **Location-based Feed**: Posts sorted by relevance and proximity
- **Interactive Maps**: Real-time alert visualization with Google Maps integration
- **Advanced Search**: Filter by keywords, category, location radius, and date
- **Admin Dashboard**: Content moderation and user management

### Categories
- **Traffic & Transportation**: Jams, accidents, transport issues
- **Crime & Safety**: Fraud alerts, missing persons, harassment reports
- **Price & Market Updates**: Daily goods, fuel prices, market rates
- **Lost & Found**: Items, pets, owner contact system
- **Local Services & Help**: Blood donation, outage alerts, emergency requests

### Technical Features
- **Responsive Design**: Mobile-first approach with desktop optimization
- **Real-time Updates**: Live feed with automatic refresh
- **Push Notifications**: Firebase Cloud Messaging ready
- **Geolocation**: GPS integration for location-based features
- **Community Voting**: Upvote/downvote system for content verification
- **Media Upload**: Support for images and videos
- **Profile Management**: User profiles with statistics and achievements

## Technology Stack

- **Frontend**: React 18 with TypeScript
- **Styling**: Tailwind CSS with responsive design
- **Icons**: Lucide React
- **Routing**: React Router DOM
- **Build Tool**: Vite
- **Development**: ESLint, TypeScript strict mode

## Getting Started

### Prerequisites
- Node.js 18+ 
- npm or yarn

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd prokash-community-platform
```

2. Install dependencies:
```bash
npm install
```

3. Start the development server:
```bash
npm run dev
```

4. Open your browser and navigate to `http://localhost:5173`

### Demo Accounts
- **Regular User**: demo@prokash.com
- **Admin User**: admin@prokash.com

## Project Structure

```
src/
├── components/          # Reusable UI components
│   ├── Header.tsx      # Navigation header
│   ├── BottomNav.tsx   # Mobile bottom navigation
│   ├── Sidebar.tsx     # Desktop sidebar
│   ├── PostCard.tsx    # Individual post display
│   └── CategoryFilter.tsx # Category filtering
├── pages/              # Main application pages
│   ├── Auth.tsx        # Authentication page
│   ├── Feed.tsx        # Main feed page
│   ├── CreatePost.tsx  # Post creation
│   ├── Maps.tsx        # Interactive map view
│   ├── Search.tsx      # Search and filtering
│   ├── Profile.tsx     # User profile management
│   └── AdminDashboard.tsx # Admin panel
├── types/              # TypeScript type definitions
│   └── index.ts        # Core interfaces
├── App.tsx             # Main application component
├── main.tsx            # Application entry point
└── index.css           # Global styles

```

## Key Features Implementation

### Authentication System
- Multiple login methods (email, phone, social)
- Role-based access control (user/admin)
- Profile management with avatar upload

### Post Management
- Rich text content with media support
- GPS location tagging
- Category-based organization
- Community verification through voting

### Real-time Features
- Live feed updates
- Location-based post sorting
- Interactive map with real-time markers
- Push notification system (Firebase ready)

### Admin Dashboard
- Content moderation tools
- User management system
- Analytics and reporting
- Post verification workflow

### Search & Discovery
- Advanced filtering options
- Location radius search
- Category-based filtering
- Popular search suggestions

## Future Enhancements

### Backend Integration
- RESTful API development
- Database integration (MySQL/PostgreSQL)
- Real-time WebSocket connections
- File upload and storage

### Mobile App
- React Native implementation
- Push notifications
- Offline support
- Camera integration

### Advanced Features
- AI-powered content moderation
- Machine learning for spam detection
- Advanced analytics dashboard
- Multi-language support

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions, please open an issue in the GitHub repository or contact the development team.

---

**Prokash** - Empowering communities through real-time local alerts and verified information sharing.