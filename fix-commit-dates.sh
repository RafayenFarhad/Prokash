#!/bin/bash

echo "🔧 Fixing commit dates to 2025..."
echo ""

# This will rewrite Git history with correct 2025 dates
git filter-branch -f --env-filter '
# Map 2024 dates to 2025 dates
export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024/2025}"
export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024/2025}"
' --tag-name-filter cat -- --branches --tags

echo ""
echo "✅ Commit dates updated to 2025!"
echo ""
echo "Now force push to GitHub:"
echo "  git push -f origin main"
echo "  git push -f origin Final-Main"
echo "  git push -f origin --all"
