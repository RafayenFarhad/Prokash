# 🔔 Professional Notification System - Complete Guide

## ✅ Notification System Implemented!

Your Prokash Alert System now has a **comprehensive, professional notification system** that sends relevant notifications based on alert priority and user location.

---

## 🎯 How It Works

### 📱 Notification Types

| Type | When Triggered | Who Gets Notified |
|------|----------------|-------------------|
| 🚨 **Emergency Alert** | Emergency post created | **ALL active users** |
| ⚠️ **High Priority Alert** | High priority post created | Users within **20km** (or all if no nearby users) |
| 🟡 **Medium Priority Alert** | Medium priority post created | Users within **10km** |
| 🟢 **Low Priority Alert** | Low priority post created | Users within **5km** |
| ✅ **Post Verified (Admin)** | Admin verifies your post | **Post author** |
| ✅ **Post Verified (Community)** | Community verifies your post | **Post author** |
| 💬 **New Comment** | Someone comments on your post | **Post author** |
| ❤️ **Post Liked** | Someone likes your post | **Post author** |
| 📝 **Post Updated** | Post you're following is updated | **Users who liked/commented** |

---

## 🚨 Emergency Alert System

### How It Works

When someone creates an **EMERGENCY** alert:

```
1. Post is created with priority = "emergency"
2. System identifies ALL active users
3. Notification sent to EVERY user (except post creator)
4. Users receive: "🚨 EMERGENCY: [Post Title]"
```

**Example:**
```
Post: "Fire breaks out in Chawkbazar"
Priority: Emergency
Result: All 30+ users get notified immediately
```

---

## 📍 Location-Based Notifications

### Radius by Priority

| Priority | Notification Radius | Logic |
|----------|---------------------|-------|
| Emergency | **Unlimited** | Everyone gets notified |
| High | **20km** | Nearby users + all if none nearby |
| Medium | **10km** | Only nearby users |
| Low | **5km** | Very local notifications |

### How Location Works

```php
// System calculates distance using Haversine formula
Distance = 6371 * acos(
    cos(radians(user_lat)) * 
    cos(radians(post_lat)) * 
    cos(radians(post_lng) - radians(user_lng)) + 
    sin(radians(user_lat)) * 
    sin(radians(post_lat))
)

// If distance <= radius → User gets notified
```

---

## 🔔 Notification Flow

### When You Create a Post

```
1. You create post: "Heavy traffic at Shahbag"
2. Priority: Medium
3. Location: Shahbag (23.7389, 90.3950)

System Actions:
├─ Finds users within 10km of Shahbag
├─ Excludes you (post creator)
├─ Sends notification to 15 nearby users
└─ Returns: "15 notifications sent"
```

### When Your Post Gets Verified

```
Admin verifies your post:
├─ You receive notification
├─ Title: "✅ Post Verified"
├─ Message: "Your post 'Heavy traffic...' has been verified by admin"
└─ Reputation points added
```

### When Someone Comments

```
User comments on your post:
├─ You receive notification
├─ Title: "💬 New Comment"
├─ Message: "John Doe commented on your post"
└─ Click to view comment
```

---

## 📊 Notification Data Structure

Each notification contains:

```json
{
  "id": 1,
  "user_id": 5,
  "type": "emergency_alert",
  "title": "🚨 Emergency Alert",
  "message": "🚨 EMERGENCY: Fire breaks out in Chawkbazar",
  "data": {
    "post_id": 12,
    "post_title": "Fire breaks out in Chawkbazar",
    "post_priority": "emergency",
    "post_category": "Emergency",
    "location": "Chawkbazar, Dhaka"
  },
  "read": false,
  "read_at": null,
  "created_at": "2025-10-13 23:45:00"
}
```

---

## 🎨 Notification Icons & Titles

| Type | Icon | Title | Color |
|------|------|-------|-------|
| Emergency Alert | 🚨 | Emergency Alert | Red |
| High Priority | ⚠️ | High Priority Alert | Orange |
| Nearby Alert | 📍 | Nearby Alert | Blue |
| Post Verified (Admin) | ✅ | Post Verified | Green |
| Post Verified (Community) | ✅ | Community Verified | Green |
| New Comment | 💬 | New Comment | Blue |
| Post Liked | ❤️ | Post Liked | Pink |
| Post Updated | 📝 | Post Updated | Gray |

---

## 🔧 API Endpoints

### Get Your Notifications
```
GET /api/notifications
Authorization: Bearer {token}

Response:
{
  "data": [
    {
      "id": 1,
      "type": "emergency_alert",
      "title": "🚨 Emergency Alert",
      "message": "...",
      "read": false,
      "created_at": "..."
    }
  ],
  "current_page": 1,
  "total": 25
}
```

### Get Unread Count
```
GET /api/notifications/unread-count
Authorization: Bearer {token}

Response:
{
  "count": 5
}
```

### Mark as Read
```
POST /api/notifications/{id}/read
Authorization: Bearer {token}

Response:
{
  "message": "Notification marked as read"
}
```

### Mark All as Read
```
POST /api/notifications/mark-all-read
Authorization: Bearer {token}

Response:
{
  "message": "All notifications marked as read"
}
```

### Delete Notification
```
DELETE /api/notifications/{id}
Authorization: Bearer {token}

Response:
{
  "message": "Notification deleted"
}
```

---

## 📈 Notification Statistics

### For Users
- Total notifications received
- Unread count
- Notifications by type
- Recent notifications

### For Admins
```
GET /api/admin/notification-stats

Response:
{
  "total_notifications": 250,
  "unread_count": 45,
  "notifications_today": 30,
  "by_type": {
    "emergency_alert": 15,
    "nearby_alert": 80,
    "post_verified_admin": 20,
    "new_comment": 100,
    "post_liked": 35
  }
}
```

---

## 🎯 Smart Notification Logic

### Prevents Spam
- ✅ Don't notify post creator about their own post
- ✅ Don't notify if you comment on your own post
- ✅ Don't notify if you like your own post
- ✅ Only notify once per action

### Location Intelligence
- ✅ Uses Haversine formula for accurate distance
- ✅ Considers Earth's curvature
- ✅ Works with any lat/lng coordinates
- ✅ Efficient database queries

### Priority-Based
- ✅ Emergency = Maximum reach
- ✅ High = Wide reach with fallback
- ✅ Medium = Local reach
- ✅ Low = Very local

---

## 🚀 Usage Examples

### Example 1: Emergency Fire Alert

```
User creates:
- Title: "Fire breaks out in Chawkbazar"
- Priority: Emergency
- Location: Chawkbazar

System sends:
- Notification to: ALL 30 users
- Type: emergency_alert
- Message: "🚨 EMERGENCY: Fire breaks out in Chawkbazar"
- Result: Everyone is warned immediately
```

### Example 2: Traffic Update

```
User creates:
- Title: "Heavy traffic at Shahbag"
- Priority: Medium
- Location: Shahbag (23.7389, 90.3950)

System sends:
- Finds users within 10km
- Notification to: 12 nearby users
- Type: nearby_alert
- Message: "📍 New alert near you: Heavy traffic at Shahbag"
- Result: Local users are informed
```

### Example 3: Admin Verification

```
Admin verifies post:
- Post: "Gas leak in Mohammadpur"
- Author: Karim Rahman

System sends:
- Notification to: Karim Rahman only
- Type: post_verified_admin
- Message: "✅ Your post 'Gas leak...' has been verified by admin"
- Result: Author knows post is trusted
```

---

## 📱 Frontend Integration (Coming Soon)

### Notification Bell
```jsx
<NotificationBell>
  <Badge count={5} />
  <Dropdown>
    <NotificationList />
  </Dropdown>
</NotificationBell>
```

### Real-time Updates
- WebSocket integration
- Auto-refresh every 30 seconds
- Sound/vibration alerts
- Desktop notifications

---

## 🎨 Notification UI Components

### Notification Item
```
┌─────────────────────────────────────┐
│ 🚨 Emergency Alert          [New]   │
│ Fire breaks out in Chawkbazar       │
│ 2 minutes ago                       │
│ [View Post] [Mark as Read]          │
└─────────────────────────────────────┘
```

### Notification List
```
┌─────────────────────────────────────┐
│ Notifications (5 unread)            │
├─────────────────────────────────────┤
│ 🚨 Emergency Alert         [New]    │
│ Fire breaks out...                  │
│ 2 min ago                           │
├─────────────────────────────────────┤
│ 💬 New Comment                      │
│ John Doe commented...               │
│ 15 min ago                          │
├─────────────────────────────────────┤
│ ✅ Post Verified                    │
│ Your post has been verified         │
│ 1 hour ago                          │
└─────────────────────────────────────┘
```

---

## 🔐 Security & Privacy

### User Privacy
- ✅ Users only see their own notifications
- ✅ Location data used only for proximity
- ✅ No personal data in notifications
- ✅ Can delete notifications anytime

### Admin Access
- ✅ Admins can view all notifications
- ✅ Admins can see notification stats
- ✅ Audit trail for admin actions

---

## 📊 Performance

### Optimizations
- ✅ Batch notification creation
- ✅ Efficient database queries
- ✅ Indexed notification table
- ✅ Pagination for large lists
- ✅ Lazy loading

### Scalability
- Can handle thousands of users
- Efficient distance calculations
- Optimized for high traffic
- Background job processing ready

---

## ✅ Testing the System

### Test Emergency Alerts
1. Create a post with priority = "emergency"
2. Check: All users should get notified
3. Verify: Notification count in response

### Test Location-Based
1. Create a post with location
2. Set priority = "medium"
3. Check: Only nearby users notified

### Test Interactions
1. Comment on someone's post
2. Check: Post author gets notified
3. Like a post
4. Check: Author gets notification

---

## 🎯 Success Metrics

Your notification system is working if:
- ✅ Emergency alerts reach all users
- ✅ Location-based alerts reach nearby users
- ✅ Post authors get interaction notifications
- ✅ Verified posts notify authors
- ✅ Unread count updates correctly
- ✅ Notifications can be marked as read
- ✅ Notifications can be deleted

---

## 🚀 Next Steps

### Immediate
1. Test creating emergency post
2. Check notification count
3. View notifications in API

### Future Enhancements
- [ ] Real-time WebSocket notifications
- [ ] Push notifications (mobile)
- [ ] Email notifications
- [ ] SMS for emergencies
- [ ] Notification preferences
- [ ] Mute/unmute categories
- [ ] Notification scheduling

---

**Your professional notification system is now live!** 🎉

Create a post and watch the notifications flow! 🔔
