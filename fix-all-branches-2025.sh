#!/bin/bash

echo "🔧 Fixing ALL branches to 2025..."
echo ""

# Remove backup refs
rm -rf .git/refs/original/

# Set environment variable to suppress warnings
export FILTER_BRANCH_SQUELCH_WARNING=1

# Get all branch names
BRANCHES=$(git branch -a | grep -v HEAD | sed 's/remotes\/origin\///' | sed 's/\*//' | tr -d ' ' | sort -u)

echo "Branches to fix:"
echo "$BRANCHES"
echo ""

# Fix all branches one by one
for branch in $BRANCHES; do
    echo "Fixing branch: $branch"
    
    # Checkout the branch (ignore errors for remote branches)
    git checkout "$branch" 2>/dev/null || git checkout -b "$branch" "origin/$branch" 2>/dev/null || continue
    
    # Apply filter to this branch
    git filter-branch -f --env-filter '
        # Replace 2024 with 2025 in all date formats
        export GIT_AUTHOR_DATE=$(echo "$GIT_AUTHOR_DATE" | sed "s/2024/2025/g")
        export GIT_COMMITTER_DATE=$(echo "$GIT_COMMITTER_DATE" | sed "s/2024/2025/g")
    ' "$branch" 2>&1 | grep -v "WARNING" | tail -1
    
    echo "✓ $branch fixed"
    echo ""
done

# Go back to main
git checkout main

echo ""
echo "✅ All branches fixed!"
echo ""

# Verify
echo "Checking for remaining 2024 dates..."
DATES_2024=$(git log --all --format="%ai" | grep "2024" | wc -l | tr -d ' ')
DATES_2025=$(git log --all --format="%ai" | grep "2025" | wc -l | tr -d ' ')

echo "Commits with 2024: $DATES_2024"
echo "Commits with 2025: $DATES_2025"
echo ""

if [ "$DATES_2024" -eq 0 ]; then
    echo "🎉 SUCCESS! All commits now have 2025 dates!"
    echo ""
    echo "Now force push all branches:"
    echo "  git push -f origin --all"
else
    echo "⚠️  Still $DATES_2024 commits with 2024 dates"
    echo ""
    echo "Running one more comprehensive fix..."
    
    # Nuclear option - rewrite everything
    git filter-branch -f --env-filter '
        export GIT_AUTHOR_DATE=$(echo "$GIT_AUTHOR_DATE" | sed "s/2024/2025/g")
        export GIT_COMMITTER_DATE=$(echo "$GIT_COMMITTER_DATE" | sed "s/2024/2025/g")
    ' --tag-name-filter cat -- --all 2>&1 | tail -5
    
    echo ""
    DATES_2024_FINAL=$(git log --all --format="%ai" | grep "2024" | wc -l | tr -d ' ')
    echo "Final check - Commits with 2024: $DATES_2024_FINAL"
    
    if [ "$DATES_2024_FINAL" -eq 0 ]; then
        echo "✅ All fixed now!"
    fi
fi

echo ""
echo "Ready to push!"
