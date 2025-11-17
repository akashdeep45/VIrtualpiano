# Recommended Camera Apps for Paper Piano

## ✅ **Best Option: IP Webcam (Recommended)**

**Why it works best:**
- Uses JPEG snapshots (`/shot.jpg`) that work perfectly in browsers
- No MJPEG stream issues
- Free and reliable
- Easy to set up

**Download:**
- Android: [IP Webcam on Google Play](https://play.google.com/store/apps/details?id=com.pas.webcam)
- Free and open source

**Setup:**
1. Install IP Webcam
2. Tap "Start server"
3. Note the IP address shown (e.g., `http://192.168.1.100:8080`)
4. Enter just the base URL in Paper Piano (e.g., `http://192.168.1.100:8080`)
5. The app will automatically use `/shot.jpg` endpoint

---

## ⚠️ DroidCam (Has Issues)

**Problems:**
- Uses MJPEG streams that browsers don't support well
- Often shows "Error creating image encoder"
- Requires special handling

**If you must use DroidCam:**
1. Try restarting the app
2. Check DroidCam settings
3. Try USB connection mode instead
4. Or use DroidCamX (paid version, might work better)

**Download:**
- Android: [DroidCam on Google Play](https://play.google.com/store/apps/details?id=com.dev47apps.droidcam)

---

## 📱 Other Alternatives

### 1. **EpocCam** (iOS only)
- Works well but iOS only
- Paid app

### 2. **iVCam** (iOS/Android)
- Paid app
- Better browser support than DroidCam

### 3. **USB Connection**
- Use DroidCam USB mode
- Requires ADB setup
- More complex but reliable

---

## 🎯 Quick Recommendation

**For best results, use IP Webcam:**
1. It's free
2. Works perfectly with web browsers
3. No encoding errors
4. Simple setup
5. Reliable connection

Just install IP Webcam, start the server, and enter the base URL shown in the app!



