#!/bin/bash

echo "🔧 Fixing ALL commit dates to 2025..."
echo ""

# Remove the filter-branch backup
rm -rf .git/refs/original/

# Use git filter-repo if available, otherwise filter-branch
if command -v git-filter-repo &> /dev/null; then
    echo "Using git-filter-repo..."
    git filter-repo --force --commit-callback '
commit.author_date = commit.author_date.replace(b"2024", b"2025")
commit.committer_date = commit.committer_date.replace(b"2024", b"2025")
'
else
    echo "Using git filter-branch..."
    export FILTER_BRANCH_SQUELCH_WARNING=1
    
    git filter-branch -f --env-filter '
    # Replace all 2024 with 2025 in dates
    export GIT_AUTHOR_DATE=$(echo "$GIT_AUTHOR_DATE" | sed "s/2024/2025/g")
    export GIT_COMMITTER_DATE=$(echo "$GIT_COMMITTER_DATE" | sed "s/2024/2025/g")
    ' -- --all
fi

echo ""
echo "✅ All dates updated to 2025!"
echo ""
echo "Verifying dates..."
DATES_2024=$(git log --all --format="%ai" | grep "2024" | wc -l)
DATES_2025=$(git log --all --format="%ai" | grep "2025" | wc -l)

echo "Commits with 2024: $DATES_2024"
echo "Commits with 2025: $DATES_2025"
echo ""

if [ "$DATES_2024" -eq 0 ]; then
    echo "✅ SUCCESS! All commits now have 2025 dates!"
else
    echo "⚠️  Still some 2024 dates remaining. Running one more time..."
    git filter-branch -f --env-filter '
    export GIT_AUTHOR_DATE=$(echo "$GIT_AUTHOR_DATE" | sed "s/2024/2025/g")
    export GIT_COMMITTER_DATE=$(echo "$GIT_COMMITTER_DATE" | sed "s/2024/2025/g")
    ' -- --all
fi

echo ""
echo "Now force push:"
echo "  git push -f origin --all"
echo "  git push -f origin --tags"
