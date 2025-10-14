#!/bin/bash

echo "🌐 Prokash Alert System - ngrok Sharing Setup"
echo "=============================================="
echo ""

# Check if ngrok is installed
if ! command -v ngrok &> /dev/null; then
    echo "❌ ngrok is not installed!"
    echo ""
    echo "Install ngrok:"
    echo "  Mac: brew install ngrok"
    echo "  Or download from: https://ngrok.com/download"
    echo ""
    echo "After installation, sign up at https://ngrok.com and run:"
    echo "  ngrok config add-authtoken YOUR_AUTH_TOKEN"
    exit 1
fi

echo "✅ ngrok is installed"
echo ""

# Check if servers are running
BACKEND_RUNNING=$(lsof -i :8000 | grep LISTEN | wc -l)
FRONTEND_RUNNING=$(lsof -i :5173 | grep LISTEN | wc -l)

if [ "$BACKEND_RUNNING" -eq 0 ]; then
    echo "⚠️  Backend server not running on port 8000"
    echo "Starting backend server..."
    cd backend
    php artisan serve > /dev/null 2>&1 &
    echo "✅ Backend started"
    cd ..
    sleep 3
else
    echo "✅ Backend server already running on port 8000"
fi

if [ "$FRONTEND_RUNNING" -eq 0 ]; then
    echo "⚠️  Frontend server not running on port 5173"
    echo "Starting frontend server..."
    cd frontend
    npm run dev > /dev/null 2>&1 &
    echo "✅ Frontend started"
    cd ..
    sleep 5
else
    echo "✅ Frontend server already running on port 5173"
fi

echo ""
echo "📡 Starting ngrok tunnels..."
echo ""
echo "Opening ngrok for backend (port 8000)..."
echo "Opening ngrok for frontend (port 5173)..."
echo ""
echo "⚠️  IMPORTANT STEPS:"
echo "1. Two ngrok windows will open"
echo "2. Copy the HTTPS URLs from each window"
echo "3. Backend URL will look like: https://abc123.ngrok.io"
echo "4. Frontend URL will look like: https://xyz789.ngrok.io"
echo ""
echo "5. Create frontend/.env file with:"
echo "   VITE_API_URL=https://YOUR_BACKEND_URL.ngrok.io/api"
echo ""
echo "6. Update backend/.env file with:"
echo "   FRONTEND_URL=https://YOUR_FRONTEND_URL.ngrok.io"
echo ""
echo "7. Restart frontend server after updating .env"
echo ""
echo "8. Share the FRONTEND URL with your teammates!"
echo ""
echo "📊 View all tunnels at: http://localhost:4040"
echo ""

# Start ngrok tunnels in separate terminal windows
osascript -e 'tell app "Terminal" to do script "cd \"'$(pwd)'\" && ngrok http 8000"'
sleep 2
osascript -e 'tell app "Terminal" to do script "cd \"'$(pwd)'\" && ngrok http 5173"'

echo "✅ ngrok tunnels started!"
echo ""
echo "Press Ctrl+C to stop this script (tunnels will continue running)"
echo ""

# Keep script running
tail -f /dev/null
