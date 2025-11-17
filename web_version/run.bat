@echo off
cd /d "%~dp0"
echo Starting Paper Piano Web Version...
echo.
echo Opening in browser...
start http://localhost:8000/app.html
echo.
echo Server running at: http://localhost:8000
echo Press Ctrl+C to stop the server
echo.

REM Use Python's built-in HTTP server (simpler)
python -m http.server 8000

pause
