import React from 'react';

interface User {
  trust_score: number;
  reputation_level: string;
  badges?: string | null;
}

interface ReputationBadgeProps {
  user: User;
  size?: 'small' | 'medium' | 'large';
  showDetails?: boolean;
}

const ReputationBadge: React.FC<ReputationBadgeProps> = ({ 
  user, 
  size = 'medium', 
  showDetails = false 
}) => {
  const getReputationColor = (level: string) => {
    switch (level.toLowerCase()) {
      case 'bronze': return 'bg-amber-100 text-amber-800 border-amber-200';
      case 'silver': return 'bg-gray-100 text-gray-800 border-gray-200';
      case 'gold': return 'bg-yellow-100 text-yellow-800 border-yellow-200';
      case 'platinum': return 'bg-blue-100 text-blue-800 border-blue-200';
      case 'diamond': return 'bg-purple-100 text-purple-800 border-purple-200';
      default: return 'bg-gray-100 text-gray-600 border-gray-200';
    }
  };

  const getReputationIcon = (level: string) => {
    switch (level.toLowerCase()) {
      case 'bronze': return '🥉';
      case 'silver': return '🥈';
      case 'gold': return '🥇';
      case 'platinum': return '💎';
      case 'diamond': return '💠';
      default: return '⭐';
    }
  };

  const getSizeClasses = () => {
    switch (size) {
      case 'small': return 'text-xs px-2 py-1';
      case 'large': return 'text-base px-4 py-2';
      default: return 'text-sm px-3 py-1';
    }
  };

  const getTrustScoreColor = (score: number) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 70) return 'text-blue-600';
    if (score >= 50) return 'text-yellow-600';
    if (score >= 30) return 'text-orange-600';
    return 'text-red-600';
  };

  const parseBadges = (badges: string | null | undefined): string[] => {
    if (!badges) return [];
    try {
      return JSON.parse(badges);
    } catch {
      return badges.split(',').map(b => b.trim()).filter(Boolean);
    }
  };

  const badgeList = parseBadges(user.badges);

  return (
    <div className="flex items-center space-x-2">
      {/* Reputation Level Badge */}
      <span className={`
        inline-flex items-center rounded-full border font-medium
        ${getReputationColor(user.reputation_level)}
        ${getSizeClasses()}
      `}>
        <span className="mr-1">{getReputationIcon(user.reputation_level)}</span>
        {user.reputation_level.charAt(0).toUpperCase() + user.reputation_level.slice(1)}
      </span>

      {/* Trust Score */}
      {showDetails && (
        <span className={`font-semibold ${getTrustScoreColor(user.trust_score)}`}>
          {user.trust_score}/100
        </span>
      )}

      {/* Additional Badges */}
      {showDetails && badgeList.length > 0 && (
        <div className="flex space-x-1">
          {badgeList.slice(0, 3).map((badge: string, index: number) => (
            <span
              key={index}
              className="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
              title={badge}
            >
              {badge}
            </span>
          ))}
          {badgeList.length > 3 && (
            <span className="text-xs text-gray-500">
              +{badgeList.length - 3} more
            </span>
          )}
        </div>
      )}
    </div>
  );
};

export default ReputationBadge;
