#!/bin/bash
cd "$(dirname "$0")"
echo "Starting Paper Piano Web Version..."
echo ""
echo "Opening in browser..."
echo ""
echo "You can also access it at: http://localhost:8000"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Try using Python's built-in HTTP server
python3 -m http.server 8000

