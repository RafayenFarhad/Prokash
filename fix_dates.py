#!/usr/bin/env python3
import subprocess
import sys

def run_cmd(cmd):
    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)
    return result.stdout.strip()

def fix_all_dates():
    print("🔧 Fixing ALL commit dates to 2025...")
    print("")
    
    # Get all commits with 2024 dates
    commits_2024 = run_cmd("git log --all --format='%H|%ai|%s' | grep '2024'")
    
    if not commits_2024:
        print("✅ No 2024 dates found!")
        return
    
    lines = commits_2024.split('\n')
    print(f"Found {len(lines)} commits with 2024 dates")
    print("")
    
    # Use filter-branch with explicit date replacement
    cmd = """
export FILTER_BRANCH_SQUELCH_WARNING=1
git filter-branch -f --env-filter '
    # Replace all 2024 dates with 2025
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-09-24/2025-09-24}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-09-25/2025-09-25}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-09-26/2025-09-26}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-09-30/2025-09-30}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-01/2025-10-01}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-03/2025-10-03}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-04/2025-10-04}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-07/2025-10-07}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-08/2025-10-08}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-10/2025-10-10}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-11/2025-10-11}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-12/2025-10-12}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-13/2025-10-13}"
    export GIT_AUTHOR_DATE="${GIT_AUTHOR_DATE//2024-10-14/2025-10-14}"
    
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-09-24/2025-09-24}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-09-25/2025-09-25}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-09-26/2025-09-26}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-09-30/2025-09-30}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-01/2025-10-01}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-03/2025-10-03}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-04/2025-10-04}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-07/2025-10-07}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-08/2025-10-08}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-10/2025-10-10}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-11/2025-10-11}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-12/2025-10-12}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-13/2025-10-13}"
    export GIT_COMMITTER_DATE="${GIT_COMMITTER_DATE//2024-10-14/2025-10-14}"
' --tag-name-filter cat -- --all
"""
    
    print("Running filter-branch...")
    subprocess.run(cmd, shell=True)
    
    # Check result
    commits_2024_after = run_cmd("git log --all --format='%ai' | grep '2024' | wc -l")
    commits_2025_after = run_cmd("git log --all --format='%ai' | grep '2025' | wc -l")
    
    print("")
    print(f"Commits with 2024: {commits_2024_after.strip()}")
    print(f"Commits with 2025: {commits_2025_after.strip()}")
    print("")
    
    if int(commits_2024_after.strip()) == 0:
        print("🎉 SUCCESS! All commits now have 2025 dates!")
        print("")
        print("Now push to GitHub:")
        print("  git push -f origin --all")
    else:
        print("⚠️  Some 2024 dates remain. Trying one more time...")
        # Try again
        subprocess.run(cmd, shell=True)
        
        commits_2024_final = run_cmd("git log --all --format='%ai' | grep '2024' | wc -l")
        print(f"Final: {commits_2024_final.strip()} commits with 2024")

if __name__ == "__main__":
    fix_all_dates()
