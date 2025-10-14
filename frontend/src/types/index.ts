export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string;
  phone_verified?: boolean;
  avatar?: string;
  bio?: string;
  latitude?: number;
  longitude?: number;
  location_name?: string;
  role: 'user' | 'admin';
  is_active: boolean;
  trust_score: number;
  reputation_level: string;
  badges?: string | null;
  created_at: string;
  updated_at: string;
}

export interface Category {
  id: number;
  name: string;
  slug: string;
  description?: string;
  posts_count?: number;
  created_at: string;
  updated_at: string;
}

export interface Tag {
  id: number;
  name: string;
  slug: string;
  posts_count?: number;
  created_at: string;
  updated_at: string;
}

export interface Post {
  id: number;
  user_id: number;
  category_id?: number;
  title: string;
  content: string;
  image?: string;
  video?: string;
  latitude?: number;
  longitude?: number;
  location_name?: string;
  priority: 'low' | 'medium' | 'high' | 'emergency';
  is_verified: boolean;
  verification_score: number;
  upvotes: number;
  downvotes: number;
  likes_count: number;
  comments_count: number;
  distance?: number;
  created_at: string;
  updated_at: string;
  user?: User;
  category?: Category;
  tags?: Tag[];
  comments?: Comment[];
  likes?: Like[];
}

export interface Comment {
  id: number;
  user_id: number;
  post_id: number;
  parent_id?: number;
  content: string;
  created_at: string;
  updated_at: string;
  user?: User;
  parent?: Comment;
  replies?: Comment[];
}

export interface Like {
  id: number;
  user_id: number;
  post_id: number;
  created_at: string;
  updated_at: string;
  user?: User;
}

export interface AuthResponse {
  user: User;
  token: string;
  message: string;
}

export interface PaginatedResponse<T> {
  data: T[];
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
}

export interface AdminStats {
  total_users: number;
  total_posts: number;
  total_comments: number;
  posts_this_month: number;
  users_this_month: number;
  recent_users: User[];
  recent_posts: Post[];
}

export interface Notification {
  id: number;
  user_id: number;
  post_id?: number;
  type: string;
  title: string;
  message: string;
  data?: any;
  action_url?: string;
  redirect_url?: string;
  read: boolean;
  read_at?: string;
  created_at: string;
  updated_at: string;
}

export interface PostReport {
  id: number;
  user_id: number;
  post_id: number;
  reason: 'spam' | 'false_info' | 'inappropriate' | 'harassment' | 'other';
  description?: string;
  status: 'pending' | 'reviewed' | 'resolved';
  created_at: string;
  updated_at: string;
  user?: User;
  post?: Post;
}

export interface PostVerification {
  id: number;
  user_id: number;
  post_id: number;
  type: 'upvote' | 'downvote';
  created_at: string;
  updated_at: string;
}
