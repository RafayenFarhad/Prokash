import { useState, useEffect } from 'react';
import type { Post, Category, Tag } from '../types';
import api from '../services/api';
import PostCard from '../components/PostCard';
import { Search, Filter, SlidersHorizontal, TrendingUp, Clock, AlertTriangle } from 'lucide-react';

export default function Posts() {
  const [posts, setPosts] = useState<Post[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const [tags, setTags] = useState<Tag[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [selectedCategory, setSelectedCategory] = useState('');
  const [selectedTag, setSelectedTag] = useState('');
  const [sortBy, setSortBy] = useState('latest');
  const [filterPriority, setFilterPriority] = useState('');
  const [showFilters, setShowFilters] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);
  const [totalPosts, setTotalPosts] = useState(0);
  const [loadingMore, setLoadingMore] = useState(false);

  useEffect(() => {
    fetchCategories();
    fetchTags();
  }, []);

  useEffect(() => {
    fetchPosts();
  }, [search, selectedCategory, selectedTag]);

  const fetchPosts = async (page = 1, append = false) => {
    try {
      if (!append) {
        setLoading(true);
        setCurrentPage(1);
      } else {
        setLoadingMore(true);
      }
      
      const params: any = { page, per_page: 50 };
      if (search) params.search = search;
      if (selectedCategory) params.category_id = selectedCategory;
      if (selectedTag) params.tag_id = selectedTag;

      const response = await api.get('/posts', { params });
      const data = response.data;
      
      if (append) {
        setPosts(prev => [...prev, ...(data.data || [])]);
      } else {
        setPosts(data.data || data);
      }
      
      setCurrentPage(data.current_page || 1);
      setTotalPages(data.last_page || 1);
      setTotalPosts(data.total || (data.data?.length || 0));
    } catch (error) {
      console.error('Error fetching posts:', error);
    } finally {
      setLoading(false);
      setLoadingMore(false);
    }
  };

  const loadMore = () => {
    if (currentPage < totalPages) {
      fetchPosts(currentPage + 1, true);
    }
  };

  const fetchCategories = async () => {
    try {
      const response = await api.get('/categories');
      setCategories(response.data);
    } catch (error) {
      console.error('Error fetching categories:', error);
    }
  };

  const fetchTags = async () => {
    try {
      const response = await api.get('/tags');
      setTags(response.data);
    } catch (error) {
      console.error('Error fetching tags:', error);
    }
  };

  // Sort and filter posts
  const getFilteredAndSortedPosts = () => {
    let filtered = [...posts];

    // Filter by priority
    if (filterPriority) {
      filtered = filtered.filter(post => post.priority === filterPriority);
    }

    // Sort posts
    switch (sortBy) {
      case 'latest':
        filtered.sort((a, b) => new Date(b.created_at).getTime() - new Date(a.created_at).getTime());
        break;
      case 'popular':
        filtered.sort((a, b) => (b.likes_count || 0) - (a.likes_count || 0));
        break;
      case 'verified':
        filtered.sort((a, b) => (b.is_verified ? 1 : 0) - (a.is_verified ? 1 : 0));
        break;
      case 'priority':
        const priorityOrder = { emergency: 0, high: 1, medium: 2, low: 3 };
        filtered.sort((a, b) => priorityOrder[a.priority] - priorityOrder[b.priority]);
        break;
    }

    return filtered;
  };

  const displayPosts = getFilteredAndSortedPosts();
  const emergencyPosts = displayPosts.filter(p => p.priority === 'emergency');
  const regularPosts = displayPosts.filter(p => p.priority !== 'emergency');

  return (
    <div className="min-h-screen bg-gray-50 py-8">
      <div className="container mx-auto px-4 max-w-7xl">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-2">Explore Alerts</h1>
          <p className="text-gray-600">Real-time community alerts and updates from Bangladesh</p>
        </div>

        {/* Search and Filters Bar */}
        <div className="bg-white rounded-lg shadow-md p-4 mb-6">
          <div className="grid md:grid-cols-4 gap-4 mb-4">
            <div className="relative md:col-span-2">
              <Search className="absolute left-3 top-3 text-gray-400" size={20} />
              <input
                type="text"
                placeholder="Search alerts..."
                className="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>

            <select
              className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              value={selectedCategory}
              onChange={(e) => setSelectedCategory(e.target.value)}
            >
              <option value="">All Categories</option>
              {categories.map((category) => (
                <option key={category.id} value={category.id}>
                  {category.name}
                </option>
              ))}
            </select>

            <button
              onClick={() => setShowFilters(!showFilters)}
              className="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition"
            >
              <SlidersHorizontal size={20} className="mr-2" />
              Filters
            </button>
          </div>

          {/* Advanced Filters */}
          {showFilters && (
            <div className="grid md:grid-cols-3 gap-4 pt-4 border-t">
              <select
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={sortBy}
                onChange={(e) => setSortBy(e.target.value)}
              >
                <option value="latest">Latest First</option>
                <option value="popular">Most Popular</option>
                <option value="verified">Verified First</option>
                <option value="priority">By Priority</option>
              </select>

              <select
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={filterPriority}
                onChange={(e) => setFilterPriority(e.target.value)}
              >
                <option value="">All Priorities</option>
                <option value="emergency">🔴 Emergency</option>
                <option value="high">🟠 High</option>
                <option value="medium">🟡 Medium</option>
                <option value="low">🟢 Low</option>
              </select>

              <select
                className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                value={selectedTag}
                onChange={(e) => setSelectedTag(e.target.value)}
              >
                <option value="">All Tags</option>
                {tags.map((tag) => (
                  <option key={tag.id} value={tag.id}>
                    #{tag.name}
                  </option>
                ))}
              </select>
            </div>
          )}
        </div>

        {/* Sort Tabs */}
        <div className="flex items-center space-x-2 mb-6 overflow-x-auto pb-2">
          <button
            onClick={() => setSortBy('latest')}
            className={`flex items-center px-4 py-2 rounded-lg font-medium transition ${
              sortBy === 'latest'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            <Clock size={18} className="mr-2" />
            Latest
          </button>
          <button
            onClick={() => setSortBy('popular')}
            className={`flex items-center px-4 py-2 rounded-lg font-medium transition ${
              sortBy === 'popular'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            <TrendingUp size={18} className="mr-2" />
            Popular
          </button>
          <button
            onClick={() => setSortBy('priority')}
            className={`flex items-center px-4 py-2 rounded-lg font-medium transition ${
              sortBy === 'priority'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            <AlertTriangle size={18} className="mr-2" />
            By Priority
          </button>
          <button
            onClick={() => setSortBy('verified')}
            className={`flex items-center px-4 py-2 rounded-lg font-medium transition ${
              sortBy === 'verified'
                ? 'bg-blue-600 text-white'
                : 'bg-white text-gray-700 hover:bg-gray-100'
            }`}
          >
            <Filter size={18} className="mr-2" />
            Verified
          </button>
        </div>

        {/* Loading State */}
        {loading ? (
          <div className="text-center py-20">
            <div className="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            <p className="mt-4 text-gray-600">Loading alerts...</p>
          </div>
        ) : displayPosts.length === 0 ? (
          <div className="text-center py-20 bg-white rounded-lg shadow-md">
            <div className="text-6xl mb-4">📭</div>
            <h3 className="text-2xl font-bold text-gray-900 mb-2">No alerts found</h3>
            <p className="text-gray-600">Try adjusting your filters or search terms</p>
          </div>
        ) : (
          <>
            {/* Emergency Alerts Section */}
            {emergencyPosts.length > 0 && (
              <div className="mb-8">
                <div className="flex items-center mb-4">
                  <AlertTriangle className="text-red-600 mr-2" size={24} />
                  <h2 className="text-2xl font-bold text-gray-900">Emergency Alerts</h2>
                  <span className="ml-3 bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-semibold">
                    {emergencyPosts.length}
                  </span>
                </div>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {emergencyPosts.map((post) => (
                    <PostCard key={post.id} post={post} />
                  ))}
                </div>
              </div>
            )}

            {/* Regular Posts Section */}
            {regularPosts.length > 0 && (
              <div>
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-2xl font-bold text-gray-900">
                    {emergencyPosts.length > 0 ? 'Other Alerts' : 'All Alerts'}
                  </h2>
                  <span className="text-gray-600">
                    Showing {displayPosts.length} of {totalPosts} {totalPosts === 1 ? 'alert' : 'alerts'}
                  </span>
                </div>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                  {regularPosts.map((post) => (
                    <PostCard key={post.id} post={post} />
                  ))}
                </div>
              </div>
            )}

            {/* Load More Button */}
            {currentPage < totalPages && !loading && (
              <div className="mt-12 text-center">
                <button
                  onClick={loadMore}
                  disabled={loadingMore}
                  className="bg-blue-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed inline-flex items-center"
                >
                  {loadingMore ? (
                    <>
                      <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                      Loading...
                    </>
                  ) : (
                    <>
                      Load More Alerts
                      <span className="ml-2 bg-blue-700 px-2 py-1 rounded text-sm">
                        {totalPosts - displayPosts.length} remaining
                      </span>
                    </>
                  )}
                </button>
                <p className="mt-3 text-gray-600 text-sm">
                  Page {currentPage} of {totalPages}
                </p>
              </div>
            )}
          </>
        )}
      </div>
    </div>
  );
}
