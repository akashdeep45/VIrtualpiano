const DEFAULT_INTERVAL_MS = 50;

export function initMobileCameraManager({
  videoElement,
  layoutCanvas,
  canvasCtx,
  videoWidth,
  videoHeight
}) {
  const state = {
    currentStream: null,
    useMobileCamera: false,
    mobileCameraUrl: '',
    mobileCameraImageInterval: null,
    mobileCameraImg: null,
    streamCanvas: null,
    streamCtx: null,
    video: videoElement,
    layoutCanvas,
    ctxLC: canvasCtx,
    VIDEO_WIDTH: videoWidth,
    VIDEO_HEIGHT: videoHeight
  };

  async function setupWebcam() {
    if (!state.video) {
      throw new Error('Video element not initialized');
    }

    if (state.currentStream) {
      state.currentStream.getTracks().forEach(track => track.stop());
      state.currentStream = null;
    }

    if (state.video.srcObject) {
      state.video.srcObject = null;
    }

    if (state.mobileCameraImageInterval) {
      clearInterval(state.mobileCameraImageInterval);
      state.mobileCameraImageInterval = null;
    }

    try {
      if (state.useMobileCamera && state.mobileCameraUrl) {
        await connectMobileCamera();
      } else {
        await connectLaptopCamera();
      }
    } catch (error) {
      console.error('Camera error:', error);
      throw error;
    }
  }

  async function connectLaptopCamera() {
    if (state.mobileCameraImageInterval) {
      clearInterval(state.mobileCameraImageInterval);
      state.mobileCameraImageInterval = null;
    }
    if (state.mobileCameraImg) {
      state.mobileCameraImg.src = '';
    }
    state.video.src = '';
    state.video.srcObject = null;

    const stream = await navigator.mediaDevices.getUserMedia({
      video: {
        facingMode: 'user',
        width: state.VIDEO_WIDTH,
        height: state.VIDEO_HEIGHT
      }
    });
    state.currentStream = stream;
    state.video.srcObject = stream;
    await new Promise(resolve => (state.video.onloadedmetadata = resolve));
    await state.video.play();
  }

  async function connectMobileCamera() {
    let inputUrl = state.mobileCameraUrl.trim();
    if (inputUrl.endsWith('/')) {
      inputUrl = inputUrl.slice(0, -1);
    }

    const urlObj = new URL(inputUrl);
    const hasEndpoint =
      /\/\w+/.test(urlObj.pathname) && urlObj.pathname !== '/';
    let baseUrl = inputUrl;
    let videoUrl = inputUrl;
    const isIPWebcam = baseUrl.includes(':8080');

    if (!hasEndpoint) {
      baseUrl = inputUrl;
      videoUrl = inputUrl + '/video';
    } else {
      baseUrl = inputUrl.substring(0, inputUrl.lastIndexOf('/'));
      videoUrl = inputUrl;
    }

    if (isIPWebcam && !hasEndpoint) {
      await setupMobileCameraFallback(baseUrl, null);
      return;
    }

    state.video.onloadedmetadata = null;
    state.video.onerror = null;
    state.video.oncanplay = null;

    try {
      state.video.crossOrigin = null;
      state.video.src = videoUrl;

      await new Promise((resolve, reject) => {
        let resolved = false;
        const timeout = setTimeout(() => {
          if (!resolved) {
            resolved = true;
            reject(new Error('TIMEOUT_FALLBACK'));
          }
        }, 5000);

        const cleanup = () => {
          clearTimeout(timeout);
          state.video.onloadedmetadata = null;
          state.video.onerror = null;
          state.video.oncanplay = null;
        };

        state.video.onloadedmetadata = () => {
          if (!resolved) {
            resolved = true;
            cleanup();
            resolve();
          }
        };

        state.video.oncanplay = () => {
          if (!resolved) {
            resolved = true;
            cleanup();
            resolve();
          }
        };

        state.video.onerror = () => {
          if (!resolved) {
            resolved = true;
            cleanup();
            reject(new Error('VIDEO_ERROR'));
          }
        };

        state.video.load();
      });

      await state.video.play();
    } catch (err) {
      if (
        err.message.includes('FALLBACK') ||
        err.message === 'VIDEO_ERROR' ||
        err.message === 'PLAY_FALLBACK'
      ) {
        await setupMobileCameraFallback(baseUrl, videoUrl);
        return;
      }
      throw err;
    }
  }

  async function setupMobileCameraFallback(baseUrl, originalUrl = null) {
    const endpoints = [
      '/shot.jpg',
      '/shot.jpg?',
      '/cam_1.jpg',
      '/cam.jpg',
      '/video',
      '/mjpegfeed',
      '/mjpegfeed?1',
      '/videofeed'
    ];

    if (originalUrl && originalUrl !== baseUrl) {
      const originalEndpoint = originalUrl.replace(baseUrl, '');
      if (originalEndpoint && originalEndpoint !== '/') {
        endpoints.unshift(originalEndpoint);
      }
    }

    let workingUrl = null;
    const quickTestUrl = baseUrl + '/shot.jpg';

    try {
      const img = new Image();
      const canLoad = await new Promise(resolve => {
        const timeout = setTimeout(() => resolve(false), 3000);
        img.onload = () => {
          clearTimeout(timeout);
          resolve(true);
        };
        img.onerror = () => {
          clearTimeout(timeout);
          resolve(false);
        };
        img.crossOrigin = null;
        img.src = quickTestUrl + '?t=' + Date.now();
      });

      if (canLoad) {
        workingUrl = quickTestUrl;
      }
    } catch {
      // ignore and continue
    }

    if (!workingUrl) {
      for (const endpoint of endpoints) {
        if (endpoint === '/shot.jpg') continue;
        const testUrl = baseUrl + endpoint;
        if (
          endpoint === '/video' ||
          endpoint.includes('video') ||
          endpoint.includes('mjpeg')
        ) {
          continue;
        }
        try {
          const img = new Image();
          const canLoad = await new Promise(resolve => {
            const timeout = setTimeout(() => resolve(false), 3000);
            img.onload = () => {
              clearTimeout(timeout);
              resolve(true);
            };
            img.onerror = () => {
              clearTimeout(timeout);
              resolve(false);
            };
            img.crossOrigin = null;
            img.src =
              testUrl + (endpoint.includes('?') ? '' : '?t=' + Date.now());
          });
          if (canLoad) {
            workingUrl = testUrl;
            break;
          }
        } catch {
          continue;
        }
      }
    }

    if (!workingUrl) {
      throw new Error(
        'Unable to connect to IP Webcam. Please verify the URL and try again.'
      );
    }

    if (!state.mobileCameraImg) {
      state.mobileCameraImg = document.createElement('img');
      state.mobileCameraImg.crossOrigin = null;
      state.mobileCameraImg.style.display = 'none';
      document.body.appendChild(state.mobileCameraImg);
    } else {
      state.mobileCameraImg.crossOrigin = null;
    }

    if (!state.streamCanvas) {
      state.streamCanvas = document.createElement('canvas');
      state.streamCanvas.width = state.VIDEO_WIDTH;
      state.streamCanvas.height = state.VIDEO_HEIGHT;
      state.streamCtx = state.streamCanvas.getContext('2d');
    } else {
      state.streamCanvas.width = state.VIDEO_WIDTH;
      state.streamCanvas.height = state.VIDEO_HEIGHT;
    }

    if (!state.layoutCanvas.width) {
      state.layoutCanvas.width = state.VIDEO_WIDTH;
      state.layoutCanvas.height = state.VIDEO_HEIGHT;
    }

    await new Promise((resolve, reject) => {
      let initialLoad = true;
      let streamCreated = false;
      const timeout = setTimeout(() => {
        if (initialLoad) {
          initialLoad = false;
          reject(new Error('Connection timeout.'));
        }
      }, 10000);

      const drawCurrentFrame = () => {
        if (!state.mobileCameraImg || !state.streamCtx) return;
        try {
          state.streamCtx.clearRect(0, 0, state.VIDEO_WIDTH, state.VIDEO_HEIGHT);
          state.streamCtx.drawImage(
            state.mobileCameraImg,
            0,
            0,
            state.VIDEO_WIDTH,
            state.VIDEO_HEIGHT
          );
          state.ctxLC.drawImage(
            state.mobileCameraImg,
            0,
            0,
            state.VIDEO_WIDTH,
            state.VIDEO_HEIGHT
          );
        } catch (e) {
          console.warn('Error drawing mobile frame:', e);
        }
      };

      const startVideoStream = () => {
        if (streamCreated) return;
        streamCreated = true;

        if (state.video.srcObject) {
          state.video.srcObject.getTracks().forEach(track => track.stop());
          state.video.srcObject = null;
        }

        const stream = state.streamCanvas.captureStream(30);
        state.video.srcObject = stream;
        state.video
          .play()
          .then(resolve)
          .catch(e => {
            console.warn('Video play warning:', e);
            resolve();
          });
      };

      state.mobileCameraImg.onload = () => {
        drawCurrentFrame();
        if (initialLoad) {
          initialLoad = false;
          clearTimeout(timeout);
          startVideoStream();
        }
      };

      state.mobileCameraImg.onerror = err => {
        if (initialLoad) {
          clearTimeout(timeout);
          reject(
            new Error(
              `Failed to load mobile camera image: ${err?.message || 'unknown'}`
            )
          );
        } else {
          console.warn('Mobile camera frame error:', err);
        }
      };

      const cacheBuster = workingUrl.includes('?') ? '&' : '?';
      const requestNextFrame = () => {
        state.mobileCameraImg.src =
          workingUrl + cacheBuster + 't=' + Date.now();
      };

      requestNextFrame();
      state.mobileCameraImageInterval = setInterval(
        requestNextFrame,
        DEFAULT_INTERVAL_MS
      );
    });
  }

  function setupMobileCameraControls() {
    const cameraLocal = document.getElementById('cameraLocal');
    const cameraMobile = document.getElementById('cameraMobile');
    const mobileCameraInput = document.getElementById('mobileCameraInput');
    const ipCameraUrl = document.getElementById('ipCameraUrl');
    const connectMobileBtn = document.getElementById('connectMobileBtn');

    if (!cameraLocal || !cameraMobile || !mobileCameraInput) {
      console.error('Mobile camera control elements not found');
      return;
    }

    cameraLocal.addEventListener('change', () => {
      mobileCameraInput.style.display = 'none';
      state.useMobileCamera = false;
      setupWebcam();
    });

    cameraMobile.addEventListener('change', () => {
      mobileCameraInput.style.display = 'block';
    });

    const testUrlBtn = document.getElementById('testUrlBtn');
    if (testUrlBtn) {
      testUrlBtn.addEventListener('click', () => {
        const url = ipCameraUrl.value.trim();
        if (!url) {
          alert('Please enter a URL first');
          return;
        }
        const testUrl = url.endsWith('/') ? url.slice(0, -1) : url;
        window.open(testUrl, '_blank');
        setTimeout(() => {
          if (confirm('Did the IP Webcam page load? Try /shot.jpg endpoint?')) {
            window.open(testUrl + '/shot.jpg', '_blank');
          }
        }, 1000);
      });
    }

    connectMobileBtn.addEventListener('click', async () => {
      const url = ipCameraUrl.value.trim();
      if (!url) {
        alert(
          'Please enter a valid IP camera URL\n\nExample: http://192.168.1.100:8080'
        );
        return;
      }

      try {
        const urlObj = new URL(url);
        if (!['http:', 'https:'].includes(urlObj.protocol)) {
          throw new Error('URL must start with http:// or https://');
        }
      } catch (e) {
        alert(
          'Invalid URL format. Example: http://192.168.1.100:8080'
        );
        return;
      }

      const originalText = connectMobileBtn.textContent;
      const originalBg = connectMobileBtn.style.backgroundColor;
      const originalColor = connectMobileBtn.style.color;

      state.mobileCameraUrl = url;
      state.useMobileCamera = true;
      connectMobileBtn.textContent = 'Connecting...';
      connectMobileBtn.disabled = true;
      connectMobileBtn.style.backgroundColor = '#ff9800';
      connectMobileBtn.style.color = 'white';

      const overallTimeout = setTimeout(() => {
        if (connectMobileBtn.textContent === 'Connecting...') {
          connectMobileBtn.textContent = 'Failed! Try Again';
          connectMobileBtn.style.backgroundColor = '#f44336';
          connectMobileBtn.style.color = 'white';
          connectMobileBtn.disabled = false;
          state.useMobileCamera = false;
          state.mobileCameraUrl = '';
          alert(
            'Connection timeout after 20 seconds. Make sure both devices are on same WiFi and the IP Webcam server is running.'
          );
        }
      }, 20000);

      try {
        await setupWebcam();
        clearTimeout(overallTimeout);
        connectMobileBtn.textContent = 'Connected ✓';
        connectMobileBtn.style.backgroundColor = '#4CAF50';
        connectMobileBtn.style.color = 'white';
        connectMobileBtn.disabled = false;
      } catch (error) {
        clearTimeout(overallTimeout);
        console.error('Mobile camera connection failed:', error);
        connectMobileBtn.textContent = 'Failed! Try Again';
        connectMobileBtn.style.backgroundColor = '#f44336';
        connectMobileBtn.style.color = 'white';
        connectMobileBtn.disabled = false;
        state.useMobileCamera = false;
        state.mobileCameraUrl = '';
        alert(
          `Failed to connect to mobile camera:\n\n${error.message}\n\nTips:\n• Same WiFi network\n• Correct IP/port\n• Try Test URL first\n• Ensure IP Webcam server is started`
        );
      } finally {
        setTimeout(() => {
          connectMobileBtn.textContent = originalText;
          connectMobileBtn.style.backgroundColor = originalBg;
          connectMobileBtn.style.color = originalColor;
        }, 4000);
      }
    });
  }

  return {
    setupWebcam,
    setupMobileCameraControls,
    isMobileCameraActive: () => state.useMobileCamera,
    getMobileCameraImage: () => state.mobileCameraImg,
    getStreamCanvas: () => state.streamCanvas,
    getStreamContext: () => state.streamCtx
  };
}

