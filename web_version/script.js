// Suppress MediaPipe verbose WebGL console logging
(function() {
  const originalLog = console.log;
  const originalWarn = console.warn;
  
  console.log = function(...args) {
    const message = args.join(' ');
    // Filter out MediaPipe WebGL verbose messages
    if (message.includes('gl_context') || 
        message.includes('GL version') || 
        message.includes('OpenGL error checking') ||
        message.includes('Successfully created a WebGL context') ||
        message.includes('I0000') ||
        message.includes('W0000')) {
      return; // Suppress these messages
    }
    originalLog.apply(console, args);
  };
  
  console.warn = function(...args) {
    const message = args.join(' ');
    // Filter out MediaPipe WebGL verbose warnings
    if (message.includes('gl_context') || 
        message.includes('OpenGL error checking') ||
        message.includes('W0000')) {
      return; // Suppress these warnings
    }
    originalWarn.apply(console, args);
  };
})();

import { initMobileCameraManager } from './mobileCamera.js';

// variables
const VIDEO_WIDTH = 1280;
const VIDEO_HEIGHT = 720;
const IMAGE_SIZE = 512;
const CLASS_COLORS = {
  0: [0, 0, 0],
  1: [0, 0, 255],
  2: [255, 0, 0],
};

let frameCount = 0;
let currentlyHoveredNotes = new Set();
let hoveredKeyIndices = new Set();

// Smoothing/debouncing for key detection (fast + stable)
let keyDetectionHistory = {}; // key index -> array of recent detections
let smoothedActiveKeys = new Set(); // Track smoothed active keys separately
const SMOOTHING_FRAMES = 3; // Short history keeps response snappy yet stable
const ACTIVATION_THRESHOLD = 0.5; // Balanced activation for quick taps
const DEACTIVATION_THRESHOLD = 0.3; // Release quickly when finger lifts
let canvasInitialized = false; // Track if canvas has been initialized

// Landmark stabilization (reduces MediaPipe dot shaking)
const LANDMARK_SMOOTHING_ALPHA = 0.45; // Balance between smoothness and responsiveness
let stabilizedLandmarksCache = [];

// Camera transform variables
let flipHorizontal = true;
let flipVertical = false;
let rotate180 = false;

// getting elements
const video = document.getElementById('webcam');
const layoutCanvas = document.getElementById('layout-canvas');
const ctxLC = layoutCanvas.getContext('2d');

const mobileCameraManager = initMobileCameraManager({
  videoElement: video,
  layoutCanvas,
  canvasCtx: ctxLC,
  videoWidth: VIDEO_WIDTH,
  videoHeight: VIDEO_HEIGHT
});
const {
  setupWebcam,
  setupMobileCameraControls,
  isMobileCameraActive,
  getMobileCameraImage,
  getStreamCanvas,
  getStreamContext
} = mobileCameraManager;

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupMobileCameraControls);
} else {
  setupMobileCameraControls();
}

const noteDisplay = document.getElementById('note-display');
const dummyButton = document.getElementById('dummy-layout');

let exportKeys = [];
let virtualPianoSVG = null; // Store the virtual piano SVG element

// Piano transformation state
let pianoTransform = {
  octaves: 2.5,
  startNote: 'C3',
  scale: 1.0,
  translateX: 0,
  translateY: 0,
  rotation: 0,
  flipH: true,
  flipV: false
};

// Red separator line position (as percentage of key height, 0.6 = 60% from top)
// Above line = black keys only, Below line = white keys only
let redLinePosition = 0.68; // Slightly lower for better separation
const RED_LINE_BUFFER = 0.025; // 2.5% deadband to prevent jitter near the line

// Load and extract piano keys from virtual piano library
async function loadVirtualPianoKeys() {
  return new Promise((resolve) => {
    try {
      // Create a container to load the virtual piano
      const tempContainer = document.createElement('div');
      tempContainer.style.position = 'absolute';
      tempContainer.style.left = '-9999px';
      tempContainer.style.visibility = 'hidden';
      tempContainer.style.width = '1000px';
      tempContainer.style.height = '200px';
      document.body.appendChild(tempContainer);
      
      // Load the virtual piano script
      const script = document.createElement('script');
      script.src = './saveweb2zip-com-virtualpiano-online/svgpiano.php';
      script.onload = () => {
        // Wait a bit for the script to fully initialize
        setTimeout(() => {
          // Initialize piano panel with default parameters
          if (typeof init_pianopanels === 'function') {
            const pianoContainer = document.createElement('figure');
            pianoContainer.id = 'virtual-piano-widget';
            tempContainer.appendChild(pianoContainer);
            
            try {
              init_pianopanels(pianoContainer);
              
              // Wait for SVG to be rendered
              setTimeout(() => {
                // Find the SVG piano element
                const svgPiano = pianoContainer.querySelector('svg');
                if (svgPiano && svgPiano.querySelector('.whitekeybutton')) {
                  virtualPianoSVG = svgPiano;
                  extractKeysFromVirtualPiano(svgPiano);
                  resolve();
                } else {
                  console.warn('Virtual piano SVG not found or not rendered, using generated keys');
                  exportKeys = generatePianoKeys('C3', 2.5);
                  drawAllKeys();
                  resolve();
                }
                
                // Clean up after extraction
                setTimeout(() => {
                  try {
                    if (tempContainer.parentNode) {
                      document.body.removeChild(tempContainer);
                    }
                  } catch (e) {
                    // Ignore cleanup errors
                  }
                }, 500);
              }, 500);
            } catch (e) {
              console.warn('Could not initialize virtual piano, using generated keys:', e);
              exportKeys = generatePianoKeys('C3', 2.5);
              drawAllKeys();
              resolve();
            }
          } else {
            console.warn('Virtual piano library not loaded, using generated keys');
            exportKeys = generatePianoKeys('C3', 2.5);
            drawAllKeys();
            resolve();
          }
        }, 100);
      };
      script.onerror = () => {
        console.warn('Failed to load virtual piano, using generated keys');
        exportKeys = generatePianoKeys('C3', 2.5);
        drawAllKeys();
        try {
          if (tempContainer.parentNode) {
            document.body.removeChild(tempContainer);
          }
        } catch (e) {
          // Ignore cleanup errors
        }
        resolve();
      };
      document.head.appendChild(script);
    } catch (error) {
      console.warn('Error loading virtual piano:', error);
      exportKeys = generatePianoKeys('C3', 2.5);
      drawAllKeys();
      resolve();
    }
  });
}

// Extract key coordinates from virtual piano SVG
function extractKeysFromVirtualPiano(svgElement) {
  exportKeys = [];
  
  // Get SVG viewBox and actual rendered dimensions
  const viewBox = svgElement.getAttribute('viewBox')?.split(' ').map(parseFloat) || [0, 0, 1000, 150];
  const svgViewBoxWidth = viewBox[2];
  const svgViewBoxHeight = viewBox[3];
  
  // Get actual rendered size of SVG
  const svgRect = svgElement.getBoundingClientRect();
  const svgActualWidth = svgRect.width;
  const svgActualHeight = svgRect.height;
  
  // Calculate scale factors
  const scaleX = svgActualWidth / svgViewBoxWidth;
  const scaleY = svgActualHeight / svgViewBoxHeight;
  
  // Target dimensions for canvas (centered, 35% of canvas height)
  const targetHeight = VIDEO_HEIGHT * 0.35;
  const targetWidth = (svgViewBoxWidth / svgViewBoxHeight) * targetHeight;
  const targetScale = targetHeight / svgViewBoxHeight;
  const offsetX = (VIDEO_WIDTH - targetWidth) / 2;
  const offsetY = (VIDEO_HEIGHT - targetHeight) / 2;
  
  // Extract all keys (white and black)
  const whiteKeys = svgElement.querySelectorAll('.whitekeybutton');
  const blackKeys = svgElement.querySelectorAll('.blackkeybutton');
  
  // Helper function to convert SVG percentage to canvas coordinates
  const svgToCanvas = (svgX, svgY, svgW, svgH) => {
    // SVG uses percentage-based positioning
    const xPercent = parseFloat(svgX) || 0;
    const yPercent = parseFloat(svgY) || 0;
    const wPercent = parseFloat(svgW) || 0;
    const hPercent = parseFloat(svgH) || 0;
    
    // Convert percentage to viewBox coordinates
    const x = (xPercent / 100) * svgViewBoxWidth;
    const y = (yPercent / 100) * svgViewBoxHeight;
    const w = (wPercent / 100) * svgViewBoxWidth;
    const h = (hPercent / 100) * svgViewBoxHeight;
    
    // Scale to canvas coordinates
    return {
      x: x * targetScale + offsetX,
      y: y * targetScale + offsetY,
      w: w * targetScale,
      h: h * targetScale
    };
  };
  
  // Process white keys
  whiteKeys.forEach((key) => {
    const keyId = key.getAttribute('id');
    const midiNote = keyId ? parseInt(keyId.replace('klawisz', '')) : null;
    
    if (midiNote !== null && midiNote >= 24 && midiNote <= 108) {
      // Convert MIDI note to note name
      const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
      const octave = Math.floor(midiNote / 12) - 1;
      const noteIndex = midiNote % 12;
      const noteName = noteNames[noteIndex];
      const fullNote = noteName + octave;
      
      // Get key position and dimensions from SVG (percentage-based)
      const x = key.getAttribute('x') || '0%';
      const y = key.getAttribute('y') || '0%';
      const width = key.getAttribute('width') || '0%';
      const height = key.getAttribute('height') || '100%';
      
      const coords = svgToCanvas(x, y, width, height);
      
      // Create polygon for this key
      const polygon = [
        [coords.x, coords.y],
        [coords.x + coords.w, coords.y],
        [coords.x + coords.w, coords.y + coords.h],
        [coords.x, coords.y + coords.h]
      ];
      
      exportKeys.push({
        note: fullNote,
        polygon: polygon,
        type: 'white',
        index: exportKeys.length,
        midiNote: midiNote
      });
    }
  });
  
  // Process black keys
  blackKeys.forEach((key) => {
    const keyId = key.getAttribute('id');
    const midiNote = keyId ? parseInt(keyId.replace('klawisz', '')) : null;
    
    if (midiNote !== null && midiNote >= 24 && midiNote <= 108) {
      // Convert MIDI note to note name
      const noteNames = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
      const octave = Math.floor(midiNote / 12) - 1;
      const noteIndex = midiNote % 12;
      const noteName = noteNames[noteIndex];
      const fullNote = noteName + octave;
      
      // Get key position and dimensions from SVG (percentage-based)
      const x = key.getAttribute('x') || '0%';
      const y = key.getAttribute('y') || '0%';
      const width = key.getAttribute('width') || '0%';
      const height = key.getAttribute('height') || '66.67%';
      
      const coords = svgToCanvas(x, y, width, height);
      
      // Create polygon for this key
      const polygon = [
        [coords.x, coords.y],
        [coords.x + coords.w, coords.y],
        [coords.x + coords.w, coords.y + coords.h],
        [coords.x, coords.y + coords.h]
      ];
      
      exportKeys.push({
        note: fullNote,
        polygon: polygon,
        type: 'black',
        index: exportKeys.length,
        midiNote: midiNote
      });
    }
  });
  
  // Sort keys by X position
  exportKeys.sort((a, b) => {
    const aCenterX = a.polygon.reduce((sum, p) => sum + p[0], 0) / a.polygon.length;
    const bCenterX = b.polygon.reduce((sum, p) => sum + p[0], 0) / b.polygon.length;
    return aCenterX - bCenterX;
  });
  
  console.log('Extracted', exportKeys.length, 'keys from virtual piano');
  
  // Draw keys on canvas
  if (exportKeys.length > 0) {
    if (!canvasInitialized) {
      layoutCanvas.width = VIDEO_WIDTH;
      layoutCanvas.height = VIDEO_HEIGHT;
      canvasInitialized = true;
    }
    drawAllKeys();
  }
}

// Generate piano keys in proper piano layout (like real piano)
// Note: Uses note names matching audio files (C3, C#3, Db3, etc.)
function generatePianoKeys(startNote = null, numOctaves = null) {
  // Use transform state if not provided
  startNote = startNote || pianoTransform.startNote;
  numOctaves = numOctaves !== null ? numOctaves : pianoTransform.octaves;
  // Note mapping: [note name, audio file name, is white key]
  const noteMap = [
    ['C', 'C', true], ['C#', 'C#', false], ['Db', 'Db', true], // C# and Db are same note
    ['D', 'D', true], ['D#', 'D#', false], ['Eb', 'Eb', true],
    ['E', 'E', true],
    ['F', 'F', true], ['F#', 'F#', false], ['Gb', 'Gb', true],
    ['G', 'G', true], ['G#', 'G#', false], ['Ab', 'Ab', true],
    ['A', 'A', true], ['A#', 'A#', false], ['Bb', 'Bb', true],
    ['B', 'B', true]
  ];
  
  // Standard 12-note chromatic scale
  const chromaticNotes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
  const whiteNotes = ['C', 'D', 'E', 'F', 'G', 'A', 'B'];
  
  // Parse start note (e.g., 'C3')
  const startOctave = parseInt(startNote.slice(-1)) || 3;
  const startNoteName = startNote.replace(/\d+$/, ''); // Remove octave number
  const startIndex = chromaticNotes.indexOf(startNoteName);
  
  if (startIndex === -1) {
    console.warn('Invalid start note, using C3');
    return generatePianoKeys('C3', numOctaves);
  }
  
  const keys = [];
  
  // Calculate total keys first
  const totalKeys = Math.floor(numOctaves * 12) + (numOctaves % 1 > 0 ? 5 : 0); // 29 keys for 2.5 octaves
  
  // Calculate key dimensions to fit canvas nicely
  // For 29 keys (2.5 octaves), we need about 17 white keys
  const numWhiteKeys = Math.ceil(totalKeys * 7 / 12); // Approximate white keys (7 per octave)
  const availableWidth = VIDEO_WIDTH - 200; // Leave margins
  const keyWidth = Math.max(35, Math.floor(availableWidth / numWhiteKeys)); // Dynamic width, min 35px
  const keyHeight = Math.min(250, VIDEO_HEIGHT * 0.35); // 35% of canvas height, max 250px
  const blackKeyWidth = Math.floor(keyWidth * 0.6); // 60% of white key width
  const blackKeyHeight = Math.floor(keyHeight * 0.6); // 60% of white key height
  const startX = (VIDEO_WIDTH - (numWhiteKeys * keyWidth)) / 2; // Center horizontally
  const startY = VIDEO_HEIGHT / 2 - keyHeight / 2; // Center vertically
  
  let currentX = startX;
  let keyIndex = 0;
  
  // Map chromatic notes to audio file names (some use sharps, some use flats)
  const noteToAudioMap = {
    'C': 'C', 'C#': 'C#', 'D': 'D', 'D#': 'D#', 'E': 'E',
    'F': 'F', 'F#': 'F#', 'G': 'G', 'G#': 'G#',
    'A': 'A', 'A#': 'A#', 'B': 'B'
  };
  
  for (let i = 0; i < totalKeys; i++) {
    const noteIndex = (startIndex + i) % 12;
    const octave = startOctave + Math.floor((startIndex + i) / 12);
    const noteName = chromaticNotes[noteIndex];
    const isWhite = whiteNotes.includes(noteName);
    
    // Map to audio file naming (use sharps for black keys: C#, D#, F#, G#, A#)
    const audioNoteName = noteToAudioMap[noteName] || noteName;
    let fullNote = audioNoteName + octave;
    
    // Map notes to available audio files (some notes use flats in audio files)
    // Check if note exists in ALL_NOTES, if not, try flat equivalent
    const noteMapping = {
      'C#3': 'Db3', 'C#4': 'Db4', 'C#5': 'Db5',
      'D#3': 'Eb3', 'D#4': 'Eb4', 'D#5': 'Eb5',
      'F#3': 'Gb3', 'F#4': 'Gb4',
      'G#3': 'Ab3', 'G#4': 'Ab4',
      'A#3': 'Bb3', 'A#4': 'Bb4'
    };
    
    // Use flat equivalent if available in audio files
    if (noteMapping[fullNote]) {
      fullNote = noteMapping[fullNote];
    }
    
    // For notes not in ALL_NOTES, try to find closest available note
    // This ensures all keys have sound even if exact note isn't available
    const availableNotes = ['A3', 'A4', 'Ab3', 'Ab4', 'B3', 'B4', 'Bb3', 'Bb4',
      'C3', 'C4', 'C5', 'D3', 'D4', 'D5', 'Db3', 'Db4', 'Db5',
      'E3', 'E4', 'E5', 'Eb3', 'Eb4', 'Eb5', 'F3', 'F4',
      'G3', 'G4', 'Gb3', 'Gb4'];
    
    // Store original note for fallback
    const originalNote = fullNote;
    
    // If note doesn't exist, try to find closest match
    if (!availableNotes.includes(fullNote)) {
      // Try flat equivalent first
      if (noteMapping[fullNote]) {
        fullNote = noteMapping[fullNote];
      }
      
      // If still not available, try octave transposition
      if (!availableNotes.includes(fullNote)) {
        const noteNameOnly = fullNote.replace(/\d+$/, '');
        
        // Try same note in different octaves (prioritize closer octaves)
        for (let octOffset = 1; octOffset <= 2; octOffset++) {
          // Try lower octave first
          const lowerOctave = Math.max(3, octave - octOffset);
          const lowerNote = noteNameOnly + lowerOctave;
          if (availableNotes.includes(lowerNote)) {
            fullNote = lowerNote;
            break;
          }
          
          // Try higher octave
          const higherOctave = Math.min(5, octave + octOffset);
          const higherNote = noteNameOnly + higherOctave;
          if (availableNotes.includes(higherNote)) {
            fullNote = higherNote;
            break;
          }
        }
        
        // Last resort: use C4 as fallback (most common note)
        if (!availableNotes.includes(fullNote)) {
          fullNote = 'C4';
        }
      }
    }
    
    if (isWhite) {
      // White key with rounded top corners
      const polygon = [
        [currentX + 2, startY], // Top left (rounded)
        [currentX + keyWidth - 2, startY], // Top right (rounded)
        [currentX + keyWidth, startY + 2], // Top right curve
        [currentX + keyWidth, startY + keyHeight - 12], // Right side
        [currentX + keyWidth - 2, startY + keyHeight - 10], // Bottom right curve
        [currentX + 2, startY + keyHeight - 10], // Bottom left curve
        [currentX, startY + keyHeight - 12], // Left side
        [currentX, startY + 2] // Top left curve
      ];
      
      keys.push({
        note: fullNote,
        polygon: polygon,
        type: 'white',
        index: keyIndex++
      });
      
      currentX += keyWidth;
    } else {
      // Black key - positioned between white keys (centered on gap)
      const blackX = currentX - blackKeyWidth / 2;
      const polygon = [
        [blackX, startY],
        [blackX + blackKeyWidth, startY],
        [blackX + blackKeyWidth, startY + blackKeyHeight],
        [blackX, startY + blackKeyHeight]
      ];
      
      keys.push({
        note: fullNote,
        polygon: polygon,
        type: 'black',
        index: keyIndex++
      });
      // Don't advance X for black keys - they're positioned relative to white keys
    }
  }
  
  return keys;
}

async function loadExportKeys() {
  // First try to load virtual piano keys
  await loadVirtualPianoKeys();
  
  // If virtual piano didn't load keys, try JSON or generate
  if (!exportKeys || exportKeys.length === 0) {
  try {
    const response = await fetch('./dummylayout.json');
      const loadedKeys = await response.json();
      
      // If loaded keys exist, use them; otherwise generate piano keys
      if (loadedKeys && loadedKeys.length > 0) {
        exportKeys = loadedKeys;
        console.log('Keys loaded from JSON:', exportKeys.length);
      } else {
        // Generate default piano keys (C3 to E5 - 29 keys like the original)
        exportKeys = generatePianoKeys('C3', 2.5);
        console.log('Generated piano keys:', exportKeys.length);
      }
      
      // Draw keys immediately after loading
      if (exportKeys && exportKeys.length > 0) {
        // Initialize canvas if needed
        if (!canvasInitialized) {
          layoutCanvas.width = VIDEO_WIDTH;
          layoutCanvas.height = VIDEO_HEIGHT;
          canvasInitialized = true;
        }
        // Draw keys on the canvas
        drawAllKeys();
      }
  } catch (error) {
      console.warn("Failed to load dummylayout.json, generating default keys:", error);
      // Generate default piano keys if JSON fails
      exportKeys = generatePianoKeys('C3', 2.5);
      if (exportKeys && exportKeys.length > 0) {
        if (!canvasInitialized) {
          layoutCanvas.width = VIDEO_WIDTH;
          layoutCanvas.height = VIDEO_HEIGHT;
          canvasInitialized = true;
        }
        drawAllKeys();
      }
    }
  }
}
loadExportKeys();

dummyButton.addEventListener('click', async () => {
  // Generate fresh piano keys
  exportKeys = generatePianoKeys();
  if (exportKeys && exportKeys.length > 0) {
    if (!canvasInitialized) {
      layoutCanvas.width = VIDEO_WIDTH;
      layoutCanvas.height = VIDEO_HEIGHT;
      canvasInitialized = true;
    }
    drawAllKeys();
  }
});

// Piano control event listeners
function setupPianoControls() {
  // Octave increase/decrease buttons
  const pianoIncrease = document.getElementById('pianoIncrease');
  const pianoDecrease = document.getElementById('pianoDecrease');
  const pianoOctaves = document.getElementById('pianoOctaves');
  
  if (pianoIncrease) {
    pianoIncrease.addEventListener('click', () => {
      pianoTransform.octaves = Math.min(7, pianoTransform.octaves + 0.5);
      pianoOctaves.textContent = pianoTransform.octaves + ' Octaves';
      exportKeys = generatePianoKeys();
      drawAllKeys();
    });
  }
  
  if (pianoDecrease) {
    pianoDecrease.addEventListener('click', () => {
      pianoTransform.octaves = Math.max(1, pianoTransform.octaves - 0.5);
      pianoOctaves.textContent = pianoTransform.octaves + ' Octaves';
      exportKeys = generatePianoKeys();
      drawAllKeys();
    });
  }
  
  // Flip and rotate buttons
  const pianoFlipH = document.getElementById('pianoFlipH');
  const pianoFlipV = document.getElementById('pianoFlipV');
  const pianoRotate = document.getElementById('pianoRotate');
  const pianoReset = document.getElementById('pianoReset');
  
  if (pianoFlipH) {
    pianoFlipH.addEventListener('click', () => {
      pianoTransform.flipH = !pianoTransform.flipH;
      drawAllKeys();
    });
  }
  
  if (pianoFlipV) {
    pianoFlipV.addEventListener('click', () => {
      pianoTransform.flipV = !pianoTransform.flipV;
      drawAllKeys();
    });
  }
  
  if (pianoRotate) {
    pianoRotate.addEventListener('click', () => {
      pianoTransform.rotation = (pianoTransform.rotation + 90) % 360;
      drawAllKeys();
    });
  }
  
  if (pianoReset) {
    pianoReset.addEventListener('click', () => {
      pianoTransform = {
        octaves: 2.5,
        startNote: 'C3',
        scale: 1.0,
        translateX: 0,
        translateY: 0,
        rotation: 0,
        flipH: true,
        flipV: false
      };
      document.getElementById('pianoSizeSlider').value = 100;
      document.getElementById('pianoPosXSlider').value = 0;
      document.getElementById('pianoPosYSlider').value = 0;
      document.getElementById('pianoSize').textContent = '100%';
      document.getElementById('pianoPosX').textContent = '0';
      document.getElementById('pianoPosY').textContent = '0';
      pianoOctaves.textContent = '2.5 Octaves';
      exportKeys = generatePianoKeys();
      drawAllKeys();
    });
  }
  
  // Size slider
  const pianoSizeSlider = document.getElementById('pianoSizeSlider');
  const pianoSize = document.getElementById('pianoSize');
  if (pianoSizeSlider && pianoSize) {
    pianoSizeSlider.addEventListener('input', (e) => {
      pianoTransform.scale = e.target.value / 100;
      pianoSize.textContent = e.target.value + '%';
      drawAllKeys();
    });
  }
  
  // Position sliders
  const pianoPosXSlider = document.getElementById('pianoPosXSlider');
  const pianoPosX = document.getElementById('pianoPosX');
  if (pianoPosXSlider && pianoPosX) {
    pianoPosXSlider.addEventListener('input', (e) => {
      pianoTransform.translateX = parseInt(e.target.value);
      pianoPosX.textContent = e.target.value;
      drawAllKeys();
    });
  }
  
  const pianoPosYSlider = document.getElementById('pianoPosYSlider');
  const pianoPosY = document.getElementById('pianoPosY');
  if (pianoPosYSlider && pianoPosY) {
    pianoPosYSlider.addEventListener('input', (e) => {
      pianoTransform.translateY = parseInt(e.target.value);
      pianoPosY.textContent = e.target.value;
      drawAllKeys();
    });
  }
  
  // Initialize display
  if (pianoOctaves) {
    pianoOctaves.textContent = pianoTransform.octaves + ' Octaves';
  }
}

// Setup piano controls when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupPianoControls);
} else {
  setupPianoControls();
}

if ('serviceWorker' in navigator) {
window.addEventListener('load', () => {
  navigator.serviceWorker.register('/sw.js')
    .then(reg => {
      console.log('Service Worker registered:', reg);
    })
    .catch(err => {
      console.error('Service Worker registration failed:', err);
    });
});
}



// mediapipe
const maxHandsSlider = document.getElementById('maxHandsSlider');
const detectSlider = document.getElementById('detectSlider');
const trackSlider = document.getElementById('trackSlider');

maxHandsSlider.addEventListener('input', () => {
  document.getElementById('hands-count').textContent = maxHandsSlider.value;
  handLandmarker.setOptions({ numHands: parseInt(maxHandsSlider.value) });
});
detectSlider.addEventListener('input', () => {
  const val = parseFloat(detectSlider.value);
  document.getElementById('detect-conf').textContent = val.toFixed(2);
  if (handLandmarker) {
    // Apply 70% of slider value for more sensitivity
    handLandmarker.setOptions({ minHandDetectionConfidence: Math.max(0.1, val * 0.7) });
  }
});
trackSlider.addEventListener('input', () => {
  const val = parseFloat(trackSlider.value);
  document.getElementById('track-conf').textContent = val.toFixed(2);
  if (handLandmarker) {
    // Apply 70% of slider value for more sensitivity
    handLandmarker.setOptions({ minTrackingConfidence: Math.max(0.1, val * 0.7) });
  }
});

// Camera transform controls - setup after DOM is ready
function setupCameraControls() {
  const flipHorizontalCheckbox = document.getElementById('flipHorizontal');
  const flipVerticalCheckbox = document.getElementById('flipVertical');
  const rotate180Checkbox = document.getElementById('rotate180');
  
  if (!flipHorizontalCheckbox || !flipVerticalCheckbox || !rotate180Checkbox) {
    console.error('Camera control elements not found');
    return;
  }
  
  function updateCameraTransform() {
    let transform = '';
    
    if (rotate180) {
      transform += 'rotate(180deg) ';
    }
    if (flipVertical) {
      transform += 'scaleY(-1) ';
    }
    if (flipHorizontal) {
      transform += 'scaleX(-1) ';
    }
    
    const transformValue = transform.trim() || 'none';
    if (video) {
      video.style.transform = transformValue;
    }
    if (layoutCanvas) {
      layoutCanvas.style.transform = transformValue;
    }
  }
  
  flipHorizontalCheckbox.addEventListener('change', (e) => {
    flipHorizontal = e.target.checked;
    updateCameraTransform();
  });
  
  flipVerticalCheckbox.addEventListener('change', (e) => {
    flipVertical = e.target.checked;
    updateCameraTransform();
  });
  
  rotate180Checkbox.addEventListener('change', (e) => {
    rotate180 = e.target.checked;
    updateCameraTransform();
  });

  // Set initial checkbox states and apply transform
  flipHorizontalCheckbox.checked = flipHorizontal;
  flipVerticalCheckbox.checked = flipVertical;
  rotate180Checkbox.checked = rotate180;
  updateCameraTransform();
  
  console.log('Camera controls initialized');
}

// Initialize camera controls when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupCameraControls);
} else {
  // DOM is already loaded
  setupCameraControls();
}

import {
  HandLandmarker,
  FilesetResolver,
  DrawingUtils
} from "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.0";
let handLandmarker;
const drawingUtils = new DrawingUtils(ctxLC);

async function createLandmarker() {

  const vision = await FilesetResolver.forVisionTasks(
    "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@0.10.0/wasm"
  );

  handLandmarker = await HandLandmarker.createFromOptions(vision, {
    baseOptions: {
      modelAssetPath: 'https://storage.googleapis.com/mediapipe-models/hand_landmarker/hand_landmarker/float16/1/hand_landmarker.task',
      delegate: "GPU"
    },
    runningMode: "VIDEO",
    numHands: parseInt(maxHandsSlider.value),
    minHandDetectionConfidence: Math.max(0.1, parseFloat(detectSlider.value) * 0.7), // More sensitive (70% of slider value)
    minTrackingConfidence: Math.max(0.1, parseFloat(trackSlider.value) * 0.7) // More sensitive (70% of slider value)
  });

}

function processLandmarkerResults(results) {
  // Only set canvas dimensions once, not every frame (prevents clearing/flickering)
  if (!canvasInitialized || layoutCanvas.width !== VIDEO_WIDTH || layoutCanvas.height !== VIDEO_HEIGHT) {
  layoutCanvas.width = VIDEO_WIDTH;
  layoutCanvas.height = VIDEO_HEIGHT;
    canvasInitialized = true;
  }
  
  const mobileCameraActive = isMobileCameraActive();
  const mobileCameraImage = getMobileCameraImage();

  // IMPORTANT: Draw video feed FIRST (before keys and hand tracking)
  // This ensures mobile camera feed is visible
  try {
    if (mobileCameraActive && mobileCameraImage && mobileCameraImage.complete && mobileCameraImage.naturalWidth > 0) {
      // Draw mobile camera image
      ctxLC.drawImage(mobileCameraImage, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
    } else if (video.readyState >= 2 && video.videoWidth > 0) {
      // Draw video element (laptop camera or mobile camera stream)
      ctxLC.drawImage(video, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
    }
  } catch (e) {
    console.warn('Error drawing video feed:', e);
  }
  
  // Ensure exportKeys is loaded before processing
  if (!exportKeys || exportKeys.length === 0) {
    return; // Keys not loaded yet, skip processing
  }

  const stabilizedLandmarks = stabilizeLandmarks(results?.landmarks);

  // Raw detection (without smoothing)
  const rawDetectedKeys = new Set();

  if (stabilizedLandmarks && stabilizedLandmarks.length > 0) {
    stabilizedLandmarks.forEach(landmarks => {
      // Include thumb (4) and all finger tips (8=index, 12=middle, 16=ring, 20=pinky)
      // MediaPipe landmarks: 4=thumb tip, 8=index tip, 12=middle tip, 16=ring tip, 20=pinky tip
      const thumbTip = landmarks[4];
      const otherFingers = [8, 12, 16, 20].map(i => landmarks[i]);
      
      // Convert to canvas coordinates
      const thumbPoint = thumbTip ? [
        thumbTip.x * layoutCanvas.width,
        thumbTip.y * layoutCanvas.height
      ] : null;
      const otherFingerPoints = otherFingers.map(pt => [
        pt.x * layoutCanvas.width,
        pt.y * layoutCanvas.height
      ]);

      // Check if multiple fingers are present (thumb + at least 1 other finger)
      const hasMultipleFingers = thumbPoint && otherFingerPoints.length > 0;
      
      // Process thumb first (prioritize thumb when multiple fingers present)
      const allFingerPoints = thumbPoint ? [thumbPoint, ...otherFingerPoints] : otherFingerPoints;
      
      allFingerPoints.forEach(([x, y], fingerIndex) => {
        const isThumb = thumbPoint && fingerIndex === 0;
        // Transform finger point once for detection (to match key coordinate space)
        const transformedPoint = transformPointForDetection(x, y);
        const tx = transformedPoint.x;
        const ty = transformedPoint.y;
        
        // Get keys bounds to calculate line Y positions
        const keysBounds = getKeysBoundingBox(exportKeys);
        if (!keysBounds) return;
        
        // Calculate red line Y position (as percentage of key height from top)
        const redLineY = keysBounds.minY + (keysBounds.height * redLinePosition);
        const redBufferPx = keysBounds.height * RED_LINE_BUFFER;
        const upperRedBand = redLineY - redBufferPx;
        const lowerRedBand = redLineY + redBufferPx;
        
        // Determine finger position relative to red line with buffer
        const isAboveRedLine = ty < upperRedBand;
        const isBelowRedLine = ty > lowerRedBand;
        const isInsideRedBuffer = !isAboveRedLine && !isBelowRedLine;
        
        // Collect all keys this finger is in (not just first match)
        const fingerKeys = [];
        for (let i = 0; i < exportKeys.length; i++) {
          // Check if point is in polygon using transformed coordinates
          let inside = false;
          const poly = exportKeys[i].polygon;
          for (let j = 0, k = poly.length - 1; j < poly.length; k = j++) {
            const xi = poly[j][0], yi = poly[j][1];
            const xj = poly[k][0], yj = poly[k][1];
            const intersect = ((yi > ty) !== (yj > ty)) && (tx < (xj - xi) * (ty - yi) / (yj - yi) + xi);
            if (intersect) inside = !inside;
          }
          
          if (inside) {
            // Apply line filtering:
            // Above red line = black only
            // Below red line = white only
            // Inside buffer zone: allow both to avoid jitter
            if (!isInsideRedBuffer) {
              if (isAboveRedLine && exportKeys[i].type !== 'black') {
                continue; // Above red line, ignore white keys
              }
              if (isBelowRedLine && exportKeys[i].type !== 'white') {
                continue; // Below red line, ignore black keys
              }
            }
            
            // Calculate distance from finger to key center for prioritization
            const keyCenter = getPolygonCenter(exportKeys[i].polygon);
            const distance = Math.sqrt(
              Math.pow(tx - keyCenter[0], 2) + Math.pow(ty - keyCenter[1], 2)
            );
            fingerKeys.push({ 
              index: i, 
              type: exportKeys[i].type, 
              distance: distance,
              isThumb: isThumb,
              priority: isThumb ? 0 : 1 // Thumb has higher priority (0 < 1)
            });
          }
        }
        
        // If finger is in multiple keys, resolve conflicts
        if (fingerKeys.length > 1) {
          // Sort by priority (thumb first), then by distance
          fingerKeys.sort((a, b) => {
            if (a.priority !== b.priority) return a.priority - b.priority;
            return a.distance - b.distance;
          });
          // Add the best match (thumb preferred, then closest)
          rawDetectedKeys.add(fingerKeys[0].index);
        } else if (fingerKeys.length === 1) {
          // Single key detected, add it
          rawDetectedKeys.add(fingerKeys[0].index);
        }
      });
    });
  }

  // Limit to maximum 2 keys at a time, but always include thumb if present
  if (rawDetectedKeys.size > 2) {
    // Get thumb and other finger positions
    let thumbKeyIndex = null;
    const thumbPositions = [];
    const otherFingerPositions = [];
    
    if (stabilizedLandmarks && stabilizedLandmarks.length > 0) {
      stabilizedLandmarks.forEach(landmarks => {
        const thumbTip = landmarks[4];
        const otherFingers = [8, 12, 16, 20].map(i => landmarks[i]);
        
        if (thumbTip) {
          const transformed = transformPointForDetection(
            thumbTip.x * layoutCanvas.width,
            thumbTip.y * layoutCanvas.height
          );
          thumbPositions.push({ x: transformed.x, y: transformed.y });
          
          // Find which key the thumb is in
          for (let i = 0; i < exportKeys.length; i++) {
            if (isPointInPolygon(thumbTip.x * layoutCanvas.width, thumbTip.y * layoutCanvas.height, exportKeys[i].polygon)) {
              thumbKeyIndex = i;
            break;
          }
        }
        }
        
        otherFingers.forEach(pt => {
          const transformed = transformPointForDetection(
            pt.x * layoutCanvas.width,
            pt.y * layoutCanvas.height
          );
          otherFingerPositions.push({ x: transformed.x, y: transformed.y });
      });
    });
    }
    
    // Separate thumb key from other keys
    const thumbKey = thumbKeyIndex !== null && rawDetectedKeys.has(thumbKeyIndex) ? thumbKeyIndex : null;
    const otherKeys = Array.from(rawDetectedKeys).filter(i => i !== thumbKeyIndex);
    
    // Calculate distance from each other key to nearest finger
    const keysWithDistance = otherKeys.map(keyIndex => {
      const keyCenter = getPolygonCenter(exportKeys[keyIndex].polygon);
      let minDistance = Infinity;
      
      // Find minimum distance to any finger (thumb or other)
      [...thumbPositions, ...otherFingerPositions].forEach(finger => {
        const distance = Math.sqrt(
          Math.pow(keyCenter[0] - finger.x, 2) + Math.pow(keyCenter[1] - finger.y, 2)
        );
        minDistance = Math.min(minDistance, distance);
      });
      
      return { index: keyIndex, distance: minDistance };
    });
    
    // Sort by distance (closest first)
    keysWithDistance.sort((a, b) => a.distance - b.distance);
    
    // Build final set: thumb (if present) + 1 closest other key, or 2 closest if no thumb
    rawDetectedKeys.clear();
    if (thumbKey !== null) {
      rawDetectedKeys.add(thumbKey); // Always include thumb
      if (keysWithDistance.length > 0) {
        rawDetectedKeys.add(keysWithDistance[0].index); // Add closest other key
      }
    } else {
      // No thumb, just take 2 closest
      keysWithDistance.slice(0, 2).forEach(k => rawDetectedKeys.add(k.index));
    }
  }
  
  // Update detection history for smoothing
  exportKeys.forEach((k, i) => {
    if (!keyDetectionHistory[i]) {
      keyDetectionHistory[i] = [];
    }
    // Add current detection (1 = detected, 0 = not detected)
    keyDetectionHistory[i].push(rawDetectedKeys.has(i) ? 1 : 0);
    // Keep only last SMOOTHING_FRAMES
    if (keyDetectionHistory[i].length > SMOOTHING_FRAMES) {
      keyDetectionHistory[i].shift();
    }
  });

  // Apply smoothing: calculate average detection rate
  // Use smoothedActiveKeys to track stable state across frames
  exportKeys.forEach((k, i) => {
    const history = keyDetectionHistory[i] || [];
    
    if (history.length === 0) {
      // Initialize with current detection if no history yet
      if (rawDetectedKeys.has(i)) {
        keyDetectionHistory[i] = new Array(SMOOTHING_FRAMES).fill(1);
        smoothedActiveKeys.add(i);
      }
      return;
    }
    
    const detectionRate = history.reduce((sum, val) => sum + val, 0) / history.length;
    const isCurrentlyActive = smoothedActiveKeys.has(i);
    
    // Activate when detection rate crosses threshold
    if (!isCurrentlyActive && detectionRate >= ACTIVATION_THRESHOLD) {
      smoothedActiveKeys.add(i);
    }
    // Deactivate when detection rate drops below release threshold
    else if (isCurrentlyActive && detectionRate < DEACTIVATION_THRESHOLD) {
      smoothedActiveKeys.delete(i);
    }
    // Otherwise keep current state (hysteresis)
  });
  
  // Update hoveredKeyIndices from smoothed keys
  hoveredKeyIndices.clear();
  smoothedActiveKeys.forEach(i => hoveredKeyIndices.add(i));

    drawAllKeys(hoveredKeyIndices);

  if (stabilizedLandmarks && stabilizedLandmarks.length > 0) {
    stabilizedLandmarks.forEach(landmarks => {
      drawingUtils.drawConnectors(landmarks, HandLandmarker.HAND_CONNECTIONS, { color: '#00ffff', lineWidth: 1 });
      drawingUtils.drawLandmarks(landmarks, { color: '#ff0000', lineWidth: 1 });
    });

    // Get all detected notes (up to 10 for both hands with 5 fingers each)
    // Sort by X position to maintain sequential order (left to right)
    const sortedIndices = [...hoveredKeyIndices].sort((a, b) => {
      const aCenterX = exportKeys[a].polygon.reduce((sum, p) => sum + p[0], 0) / exportKeys[a].polygon.length;
      const bCenterX = exportKeys[b].polygon.reduce((sum, p) => sum + p[0], 0) / exportKeys[b].polygon.length;
      return aCenterX - bCenterX; // Left to right order for proper sequencing
    });
    
    const newHoveredNotes = new Set(sortedIndices.map(i => exportKeys[i].note));

    // Start new notes
    newHoveredNotes.forEach(note => {
      if (!currentlyHoveredNotes.has(note)) {
        tryPlayNote(note);
      }
    });
    // Stop notes no longer hovered
    currentlyHoveredNotes.forEach(note => {
      if (!newHoveredNotes.has(note)) {
        stopNote(note);
      }
    });
    currentlyHoveredNotes = newHoveredNotes;

    if (newHoveredNotes.size > 0) {
      const anyIndex = [...hoveredKeyIndices][0];
      const keysBounds = getKeysBoundingBox(exportKeys);
      if (keysBounds) {
        const canvasCenterX = layoutCanvas.width / 2;
        const canvasCenterY = layoutCanvas.height / 2;
        const offsetX = canvasCenterX - keysBounds.centerX;
        const offsetY = canvasCenterY - keysBounds.centerY;
        
        // Get polygon center and apply translation
        const [keyX, keyY] = getPolygonCenter(exportKeys[anyIndex].polygon);
        const displayX = keyX + offsetX;
        const displayY = keyY + offsetY;
        showHoverNote([...newHoveredNotes], displayX, displayY);
      } else {
      const [x, y] = exportKeys[anyIndex].polygon[0];
      showHoverNote([...newHoveredNotes], x, y);
      }
    } else {
      hideHoverNote();
    }
  } else {
    // No hands detected - clear raw detections but keep smoothed keys for a bit
    // This prevents keys from disappearing immediately when hand briefly leaves frame
    const allKeys = new Set(exportKeys.map((_, i) => i));
    allKeys.forEach(i => {
      if (!keyDetectionHistory[i]) {
        keyDetectionHistory[i] = [];
      }
      // Add "not detected" to history
      keyDetectionHistory[i].push(0);
      if (keyDetectionHistory[i].length > SMOOTHING_FRAMES) {
        keyDetectionHistory[i].shift();
      }
      
      // Gradually deactivate if no detection
      const history = keyDetectionHistory[i];
      if (history.length > 0) {
        const detectionRate = history.reduce((a, b) => a + b, 0) / history.length;
        if (smoothedActiveKeys.has(i) && detectionRate < DEACTIVATION_THRESHOLD) {
          smoothedActiveKeys.delete(i);
        }
      }
    });
    
    hoveredKeyIndices.clear();
    smoothedActiveKeys.forEach(i => hoveredKeyIndices.add(i));
    drawAllKeys(hoveredKeyIndices);
    
    // Stop all notes if no keys are hovered
    if (hoveredKeyIndices.size === 0) {
      currentlyHoveredNotes.forEach(note => stopNote(note));
    currentlyHoveredNotes.clear();
    }
    hideHoverNote();
  }
}

async function processLoop() {
  const mobileCameraActive = isMobileCameraActive();
  const mobileCameraImage = getMobileCameraImage();
  const streamCanvasRef = getStreamCanvas();
  const streamCtxRef = getStreamContext();

  // Always process if video is ready OR if using mobile camera
  if (video.readyState >= 2 || mobileCameraActive) {
    // For mobile camera, process every frame for smoother detection
    // For laptop camera, process every other frame (less CPU intensive)
    const shouldProcess = mobileCameraActive ? true : (++frameCount % 2 === 0);
    
    if (shouldProcess) {
      // Ensure canvas is initialized
      if (!canvasInitialized) {
        layoutCanvas.width = VIDEO_WIDTH;
        layoutCanvas.height = VIDEO_HEIGHT;
        canvasInitialized = true;
      }
      
      // For mobile camera, ensure video feed is drawn to layout canvas
      if (mobileCameraActive && mobileCameraImage && mobileCameraImage.complete) {
        try {
          ctxLC.drawImage(mobileCameraImage, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
        } catch (e) {
          console.warn('Error drawing mobile camera frame:', e);
        }
      } else if (video.readyState >= 2 && video.videoWidth > 0) {
        // For laptop camera, draw video element
        try {
          ctxLC.drawImage(video, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
        } catch (e) {
          console.warn('Error drawing video frame:', e);
        }
      }
      
      // Run MediaPipe detection if video stream is ready
      // For mobile camera, check both video element and ensure stream canvas is updated
      let videoReady = false;
      
      if (mobileCameraActive) {
        // For mobile camera, check if stream canvas is available
        if (streamCanvasRef && streamCanvasRef.width > 0 && streamCanvasRef.height > 0) {
          // Ensure stream canvas is updated with latest frame for MediaPipe
          if (mobileCameraImage && mobileCameraImage.complete && mobileCameraImage.naturalWidth > 0 && streamCtxRef) {
            try {
              streamCtxRef.clearRect(0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
              streamCtxRef.drawImage(mobileCameraImage, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
            } catch (e) {
              console.warn('Error updating stream canvas:', e);
            }
          }
          // Treat mobile stream as ready even if HTMLVideoElement metadata isn't populated yet
          videoReady = true;
        } else if (mobileCameraImage && mobileCameraImage.complete) {
          videoReady = true;
        }
      } else {
        // For laptop camera, standard check
        videoReady = video.readyState >= 2 && video.videoWidth > 0 && video.videoHeight > 0;
      }
      
      if (videoReady && handLandmarker) {
        try {
          // Use video element for MediaPipe (it has the canvas stream for mobile camera)
          // For mobile camera, ensure we're using the stream canvas feed
      const results = await handLandmarker.detectForVideo(video, performance.now());
      processLandmarkerResults(results);
        } catch (e) {
          console.warn('Hand detection error:', e);
          // Still draw keys even if detection fails
          if (exportKeys && exportKeys.length > 0) {
            drawAllKeys(hoveredKeyIndices);
          }
        }
      } else {
        // Video not ready yet - just draw keys
        if (exportKeys && exportKeys.length > 0) {
          drawAllKeys(hoveredKeyIndices);
        }
        
        // Debug: log video state for mobile camera
        if (mobileCameraActive) {
          console.log('Mobile camera video state:', {
            readyState: video.readyState,
            videoWidth: video.videoWidth,
            videoHeight: video.videoHeight,
            hasSrcObject: !!video.srcObject
          });
        }
      }
    } else {
      // On non-processing frames, just update display
      if (mobileCameraActive && mobileCameraImage && mobileCameraImage.complete) {
        try {
          ctxLC.drawImage(mobileCameraImage, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
        } catch (e) {
          // Ignore errors on non-processing frames
        }
      }
      
      // Always redraw keys to ensure they're visible
      if (exportKeys && exportKeys.length > 0) {
        drawAllKeys(hoveredKeyIndices);
      }
    }
  } else {
    // Video not ready yet, but still try to draw keys if loaded
    if (exportKeys && exportKeys.length > 0 && canvasInitialized) {
      drawAllKeys(hoveredKeyIndices);
    }
  }
  requestAnimationFrame(processLoop);
}


async function startAll() {
  try {
    await setupWebcam()
    await createLandmarker();
    requestAnimationFrame(processLoop);  
  } 
  catch (err) {
    alert('error');
  }
}
startAll();


// utilities functions for playing

// Lazy AudioContext creation to avoid autoplay warnings
let audioContext = null;
const activeNotes = {}; // note -> { source, gainNode }
const audioBuffers = {}; // Preloaded audio buffers: note -> AudioBuffer
let audioPreloadComplete = false;

// Get or create AudioContext (lazy initialization)
function getAudioContext() {
  if (!audioContext) {
    try {
      audioContext = new (window.AudioContext || window.webkitAudioContext)();
      // Suppress the autoplay warning by catching the promise rejection silently
if (audioContext.state === 'suspended') {
        audioContext.resume().catch(() => {
          // Silently handle - will be resumed on user interaction
        });
      }
    } catch (e) {
      console.error('Failed to create AudioContext:', e);
    }
  }
  return audioContext;
}

// Resume AudioContext on user interaction (required by browser autoplay policy)
function resumeAudioContext() {
  const ctx = getAudioContext();
  if (ctx && ctx.state === 'suspended') {
    ctx.resume().then(() => {
      // AudioContext resumed successfully
    }).catch(err => {
      // Silently handle - will retry on next interaction
    });
  }
}

// Resume on any user interaction
document.addEventListener('click', resumeAudioContext, { once: false });
document.addEventListener('touchstart', resumeAudioContext, { once: false });
document.addEventListener('keydown', resumeAudioContext, { once: false });

// List of all available notes
const ALL_NOTES = [
  'A3', 'A4', 'Ab3', 'Ab4', 'B3', 'B4', 'Bb3', 'Bb4',
  'C3', 'C4', 'C5', 'D3', 'D4', 'D5', 'Db3', 'Db4', 'Db5',
  'E3', 'E4', 'E5', 'Eb3', 'Eb4', 'Eb5', 'F3', 'F4',
  'G3', 'G4', 'Gb3', 'Gb4'
];

// Preload all audio files (will wait for user interaction)
async function preloadAllAudio() {
  // Wait for user interaction before creating AudioContext
  const waitForInteraction = () => {
    return new Promise((resolve) => {
      const onInteraction = async () => {
        resumeAudioContext();
        // Give AudioContext time to resume
        await new Promise(r => setTimeout(r, 100));
        const ctx = getAudioContext();
        if (ctx && ctx.state !== 'suspended') {
          resolve(ctx);
        } else {
          // If still suspended, wait for next interaction
          document.addEventListener('click', onInteraction, { once: true });
          document.addEventListener('touchstart', onInteraction, { once: true });
          document.addEventListener('keydown', onInteraction, { once: true });
        }
      };
      document.addEventListener('click', onInteraction, { once: true });
      document.addEventListener('touchstart', onInteraction, { once: true });
      document.addEventListener('keydown', onInteraction, { once: true });
    });
  };
  
  const ctx = await waitForInteraction();
  console.log('Preloading audio files...');
  const loadPromises = ALL_NOTES.map(async (note) => {
    try {
      const url = `sounds/${note}.mp3`;
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'audio/mpeg, audio/*, */*'
        }
      });
      
      if (!response.ok) {
        console.warn(`Failed to preload: ${note}.mp3`);
        return;
      }
      
      const arrayBuffer = await response.arrayBuffer();
      const audioBuffer = await ctx.decodeAudioData(arrayBuffer);
      audioBuffers[note] = audioBuffer;
      console.log(`✓ Loaded: ${note}`);
    } catch (error) {
      console.error(`Error loading ${note}:`, error);
    }
  });
  
  await Promise.all(loadPromises);
  audioPreloadComplete = true;
  console.log('All audio files preloaded!');
}

// Start preloading when page loads (will wait for user interaction)
preloadAllAudio();

async function tryPlayNote(note) {
  if (activeNotes[note]) return;

  // Ensure AudioContext is available
  const ctx = getAudioContext();
  if (!ctx) {
    console.warn('AudioContext not available yet');
    return;
  }

  // Resume AudioContext if suspended
  if (ctx.state === 'suspended') {
    await ctx.resume().catch(() => {});
  }

  // Use preloaded buffer if available
  let audioBuffer = audioBuffers[note];
  
  // If not preloaded yet, try to load it (fallback)
  if (!audioBuffer) {
    console.warn(`Audio not preloaded for ${note}, loading on demand...`);
    try {
      const url = `sounds/${note}.mp3`;
      const response = await fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'audio/mpeg, audio/*, */*'
        }
      });
      
      if (!response.ok) {
        console.error(`Failed to load audio: ${note}.mp3`);
        return;
      }
      
  const arrayBuffer = await response.arrayBuffer();
      audioBuffer = await ctx.decodeAudioData(arrayBuffer);
      audioBuffers[note] = audioBuffer; // Cache it for next time
    } catch (error) {
      console.error(`Error loading audio for ${note}:`, error);
      return;
    }
  }

  const gainNode = ctx.createGain();
  gainNode.gain.setValueAtTime(1, ctx.currentTime); // start at full volume

  const source = ctx.createBufferSource();
  source.buffer = audioBuffer;
  source.connect(gainNode).connect(ctx.destination);
  source.start();

  activeNotes[note] = { source, gainNode };
}
function stopNote(note) {
  const active = activeNotes[note];
  if (active) {
    const ctx = getAudioContext();
    if (!ctx) return;
    const { source, gainNode } = active;
    const now = ctx.currentTime;

    gainNode.gain.cancelScheduledValues(now);
    gainNode.gain.setValueAtTime(gainNode.gain.value, now);
    gainNode.gain.linearRampToValueAtTime(0, now + 2); // fade out over 2 seconds

    source.stop(now + 2);
    delete activeNotes[note];
  }
}

// Helper function to calculate center of a polygon
function getPolygonCenter(pts) {
  if (!pts || pts.length === 0) return [0, 0];
  const sumX = pts.reduce((sum, p) => sum + p[0], 0);
  const sumY = pts.reduce((sum, p) => sum + p[1], 0);
  return [sumX / pts.length, sumY / pts.length];
}

// Calculate the bounding box and center of all keys
function getKeysBoundingBox(keys) {
  if (!keys || keys.length === 0) return null;
  
  let minX = Infinity, minY = Infinity;
  let maxX = -Infinity, maxY = -Infinity;
  
  keys.forEach(k => {
    if (!k || !k.polygon || k.polygon.length === 0) return;
    k.polygon.forEach(pt => {
      minX = Math.min(minX, pt[0]);
      minY = Math.min(minY, pt[1]);
      maxX = Math.max(maxX, pt[0]);
      maxY = Math.max(maxY, pt[1]);
    });
  });
  
  if (minX === Infinity) return null;
  
  const centerX = (minX + maxX) / 2;
  const centerY = (minY + maxY) / 2;
  const width = maxX - minX;
  const height = maxY - minY;
  
  return { minX, minY, maxX, maxY, centerX, centerY, width, height };
}

function drawAllKeys(hoveredIndices = new Set()) {
  // Ensure exportKeys is loaded
  if (!exportKeys || exportKeys.length === 0) {
    return; // Keys not loaded yet
  }
  
  // Use composite operation to draw keys over video without clearing
  ctxLC.save();
  ctxLC.globalCompositeOperation = 'source-over';
  ctxLC.font = "bold 16px 'Poppins', sans-serif";
  ctxLC.textAlign = "center";
  ctxLC.textBaseline = "middle";

  // Calculate center of all keys and center of canvas
  const keysBounds = getKeysBoundingBox(exportKeys);
  if (keysBounds) {
    const canvasCenterX = layoutCanvas.width / 2;
    const canvasCenterY = layoutCanvas.height / 2;
    
    // Calculate translation to center keys in canvas
    let offsetX = canvasCenterX - keysBounds.centerX;
    let offsetY = canvasCenterY - keysBounds.centerY;
    
    // Apply user transformations
    ctxLC.translate(canvasCenterX + pianoTransform.translateX, canvasCenterY + pianoTransform.translateY);
    ctxLC.rotate((pianoTransform.rotation * Math.PI) / 180);
    ctxLC.scale(pianoTransform.scale * (pianoTransform.flipH ? -1 : 1), pianoTransform.scale * (pianoTransform.flipV ? -1 : 1));
    ctxLC.translate(-keysBounds.centerX, -keysBounds.centerY);
  }

  // Sort keys by X position (left to right) to ensure proper piano sequence
  const sortedKeys = [...exportKeys].map((k, i) => ({ key: k, index: i })).sort((a, b) => {
    if (!a.key || !b.key || !a.key.polygon || !b.key.polygon) return 0;
    const aCenterX = a.key.polygon.reduce((sum, p) => sum + p[0], 0) / a.key.polygon.length;
    const bCenterX = b.key.polygon.reduce((sum, p) => sum + p[0], 0) / b.key.polygon.length;
    return aCenterX - bCenterX;
  });

  // Draw white keys first (background layer) - sorted left to right
  sortedKeys.forEach(({ key: k, index: i }) => {
    if (!k || !k.polygon || k.polygon.length === 0) return; // Skip invalid keys
    
    const isHovered = hoveredIndices.has(i);
    if (k.type === "white") {
      // Enhanced styling for white keys with transparency
      const fill = isHovered ? "rgba(255, 215, 0, 0.6)" : "rgba(255, 255, 255, 0.7)";
      const stroke = isHovered ? "#FFD700" : "#333";
      const strokeWidth = isHovered ? 3 : 1.5;
      drawPoly(ctxLC, k.polygon, fill, stroke, k.note, isHovered, strokeWidth);
    }
  });

  // Draw black keys on top - sorted left to right
  sortedKeys.forEach(({ key: k, index: i }) => {
    if (!k || !k.polygon || k.polygon.length === 0) return; // Skip invalid keys
    
    const isHovered = hoveredIndices.has(i);
    if (k.type === "black") {
      // Enhanced styling for black keys with transparency
      const fill = isHovered ? "rgba(255, 215, 0, 0.8)" : "rgba(50, 50, 50, 0.75)";
      const stroke = isHovered ? "#FFD700" : "#000";
      const strokeWidth = isHovered ? 3 : 1.5;
      drawPoly(ctxLC, k.polygon, fill, stroke, k.note, isHovered, strokeWidth);
    }
  });
  
  // Draw red separator line (after transformations are applied)
  if (keysBounds) {
    const redLineY = keysBounds.minY + (keysBounds.height * redLinePosition);
    ctxLC.save(); // Save current transform state
    ctxLC.strokeStyle = '#FF0000';
    ctxLC.lineWidth = 4; // Make it more visible
    ctxLC.setLineDash([]); // Solid line
    ctxLC.globalAlpha = 0.8; // Slightly transparent
    ctxLC.beginPath();
    ctxLC.moveTo(keysBounds.minX, redLineY);
    ctxLC.lineTo(keysBounds.maxX, redLineY);
    ctxLC.stroke();
    ctxLC.restore(); // Restore transform state
  }
  
  ctxLC.restore();
}

function drawPoly(ctxLC, pts, fillStyle, strokeStyle, text, highlight = false, strokeWidth = 1.5) {
  ctxLC.beginPath();
  pts.forEach((p, i) => i === 0 ? ctxLC.moveTo(...p) : ctxLC.lineTo(...p));
  ctxLC.closePath();
  
  // Add shadow for depth (realistic piano key effect)
  if (highlight) {
    ctxLC.shadowColor = "rgba(255, 215, 0, 0.9)";
    ctxLC.shadowBlur = 20;
    ctxLC.shadowOffsetX = 0;
    ctxLC.shadowOffsetY = 0;
  } else {
    ctxLC.shadowColor = "rgba(0, 0, 0, 0.4)";
    ctxLC.shadowBlur = 8;
    ctxLC.shadowOffsetX = 2;
    ctxLC.shadowOffsetY = 2;
  }
  
  // Create gradient for realistic piano key appearance
  if (fillStyle.includes('255, 255, 255')) {
    // White key gradient
    const gradient = ctxLC.createLinearGradient(
      pts[0][0], pts[0][1],
      pts[0][0], pts[pts.length - 1][1]
    );
    if (highlight) {
      gradient.addColorStop(0, "rgba(255, 240, 150, 0.75)");
      gradient.addColorStop(1, "rgba(255, 215, 0, 0.75)");
    } else {
      gradient.addColorStop(0, "rgba(255, 255, 255, 0.75)");
      gradient.addColorStop(1, "rgba(240, 240, 240, 0.75)");
    }
    ctxLC.fillStyle = gradient;
  } else if (fillStyle.includes('50, 50, 50')) {
    // Black key gradient
    const gradient = ctxLC.createLinearGradient(
      pts[0][0], pts[0][1],
      pts[0][0], pts[pts.length - 1][1]
    );
    if (highlight) {
      gradient.addColorStop(0, "rgba(255, 200, 0, 0.85)");
      gradient.addColorStop(1, "rgba(255, 150, 0, 0.85)");
    } else {
      gradient.addColorStop(0, "rgba(30, 30, 30, 0.8)");
      gradient.addColorStop(1, "rgba(0, 0, 0, 0.8)");
    }
    ctxLC.fillStyle = gradient;
  } else {
    ctxLC.fillStyle = fillStyle;
  }
  
  ctxLC.fill();
  
  // Reset shadow before stroke
  ctxLC.shadowBlur = 0;
  ctxLC.shadowOffsetX = 0;
  ctxLC.shadowOffsetY = 0;
  
  if (strokeStyle) {
    ctxLC.strokeStyle = strokeStyle;
    ctxLC.lineWidth = strokeWidth;
    ctxLC.stroke();
  }
  
  // Draw note label at bottom of key (like real piano)
  if (text) {
    const cx = pts.reduce((s, p) => s + p[0], 0) / pts.length;
    const bottomY = Math.max(...pts.map(p => p[1])) - 15; // 15px from bottom
    
    // White text for black keys, dark text for white keys
    const isBlackKey = fillStyle.includes('50, 50, 50') || fillStyle.includes('30, 30, 30');
    ctxLC.fillStyle = highlight ? "#FFD700" : (isBlackKey ? "rgba(255, 255, 255, 0.9)" : "rgba(0, 0, 0, 0.7)");
    ctxLC.font = highlight ? "bold 16px 'Poppins', sans-serif" : "bold 12px 'Poppins', sans-serif";
    ctxLC.textAlign = "center";
    ctxLC.textBaseline = "bottom";
    ctxLC.fillText(text, cx, bottomY);
  }
}

function showHoverNote(notes, x, y) {
  noteDisplay.innerText = notes.join(", ");
  noteDisplay.style.left = `${x + 10}px`;
  noteDisplay.style.display = 'inline';
}

function hideHoverNote() {
  noteDisplay.style.display = 'none';
}

// Transform point according to piano transformations (inverse transform)
function transformPointForDetection(x, y) {
  const keysBounds = getKeysBoundingBox(exportKeys);
  if (!keysBounds) return { x, y };
  
  const canvasCenterX = layoutCanvas.width / 2;
  const canvasCenterY = layoutCanvas.height / 2;
  
  // Translate to center
  let tx = x - canvasCenterX - pianoTransform.translateX;
  let ty = y - canvasCenterY - pianoTransform.translateY;
  
  // Rotate back
  const angle = -(pianoTransform.rotation * Math.PI) / 180;
  const cos = Math.cos(angle);
  const sin = Math.sin(angle);
  const rx = tx * cos - ty * sin;
  const ry = tx * sin + ty * cos;
  
  // Scale back
  const sx = rx / pianoTransform.scale;
  const sy = ry / pianoTransform.scale;
  
  // Flip back
  const fx = pianoTransform.flipH ? -sx : sx;
  const fy = pianoTransform.flipV ? -sy : sy;
  
  // Translate back to key coordinates
  return {
    x: fx + keysBounds.centerX,
    y: fy + keysBounds.centerY
  };
}

function stabilizeLandmarks(rawLandmarks) {
  if (!rawLandmarks || rawLandmarks.length === 0) {
    stabilizedLandmarksCache = [];
    return [];
  }
  
  // Ensure cache array length matches detected hands
  if (!Array.isArray(stabilizedLandmarksCache)) {
    stabilizedLandmarksCache = [];
  }
  
  rawLandmarks.forEach((handLandmarks, handIndex) => {
    if (!stabilizedLandmarksCache[handIndex]) {
      stabilizedLandmarksCache[handIndex] = handLandmarks.map(pt => ({ ...pt }));
      return;
    }
    
    const cachedHand = stabilizedLandmarksCache[handIndex];
    handLandmarks.forEach((point, idx) => {
      if (!cachedHand[idx]) {
        cachedHand[idx] = { ...point };
        return;
      }
      cachedHand[idx].x += (point.x - cachedHand[idx].x) * LANDMARK_SMOOTHING_ALPHA;
      cachedHand[idx].y += (point.y - cachedHand[idx].y) * LANDMARK_SMOOTHING_ALPHA;
      cachedHand[idx].z += (point.z - cachedHand[idx].z) * LANDMARK_SMOOTHING_ALPHA;
    });
  });
  
  // Trim cache if fewer hands detected
  stabilizedLandmarksCache = stabilizedLandmarksCache.slice(0, rawLandmarks.length);
  
  // Return a copy so downstream logic doesn't mutate cache directly
  return stabilizedLandmarksCache.map(hand =>
    hand.map(pt => ({ x: pt.x, y: pt.y, z: pt.z }))
  );
}

function isPointInPolygon(x, y, poly) {
  // Transform point for detection
  const transformed = transformPointForDetection(x, y);
  x = transformed.x;
  y = transformed.y;
  let inside = false;
  for (let i = 0, j = poly.length - 1; i < poly.length; j = i++) {
    const xi = poly[i][0], yi = poly[i][1];
    const xj = poly[j][0], yj = poly[j][1];
    const intersect = ((yi > y) !== (yj > y)) &&
                      (x < (xj - xi) * (y - yi) / (yj - yi + 1e-10) + xi);
    if (intersect) inside = !inside;
  }
  return inside;
}


// model loading (printed paper mode)

const MODEL_URL = 'model.onnx';
let session;
let modelLoaded = false;
const captureBtn = document.getElementById('capture-btn');
const likeMask = document.getElementById('make-mask');
const capturedImg = document.getElementById('captured-img');
const rawCanvas = document.getElementById('raw-canvas');
const ctxRaw = rawCanvas.getContext('2d');

async function loadModel() {
  console.log('Loading ONNX model...');
  const response = await fetch(MODEL_URL);
  const arrayBuffer = await response.arrayBuffer();
  return ort.InferenceSession.create(arrayBuffer);
}

function preprocessImage(canvas) {
  const tempCanvas = document.createElement('canvas');
  tempCanvas.width = IMAGE_SIZE;
  tempCanvas.height = IMAGE_SIZE;
  const ctxTemp = tempCanvas.getContext('2d');
  ctxTemp.drawImage(canvas, 0, 0, IMAGE_SIZE, IMAGE_SIZE);
  const { data } = ctxTemp.getImageData(0, 0, IMAGE_SIZE, IMAGE_SIZE);
  const floatData = new Float32Array(IMAGE_SIZE * IMAGE_SIZE * 3);
  for (let i = 0; i < IMAGE_SIZE * IMAGE_SIZE; i++) {
    floatData[i * 3 + 0] = data[i * 4 + 2] / 255;
    floatData[i * 3 + 1] = data[i * 4 + 1] / 255;
    floatData[i * 3 + 2] = data[i * 4 + 0] / 255;
  }
  return new ort.Tensor('float32', floatData, [1, IMAGE_SIZE, IMAGE_SIZE, 3]);
}

function renderRawMask(predMask, w, h) {
  rawCanvas.width = VIDEO_WIDTH;
  rawCanvas.height = VIDEO_HEIGHT;
  const temp = document.createElement('canvas');
  temp.width = w;
  temp.height = h;
  const tempCtx = temp.getContext('2d');
  const rawData = new Uint8ClampedArray(w * h * 4);
  for (let i = 0; i < w * h; i++) {
    const color = CLASS_COLORS[predMask[i]];
    rawData[i * 4 + 0] = color[0];
    rawData[i * 4 + 1] = color[1];
    rawData[i * 4 + 2] = color[2];
    rawData[i * 4 + 3] = 255;
  }
  const imageData = new ImageData(rawData, w, h);
  tempCtx.putImageData(imageData, 0, 0);
  ctxRaw.clearRect(0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
  ctxRaw.drawImage(temp, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
}

async function runInference(canvas) {
  const inputTensor = preprocessImage(canvas);
  const feeds = {};
  feeds[session.inputNames[0]] = inputTensor;
  const results = await session.run(feeds);
  const output = results[session.outputNames[0]];
  const [_, h, w, numClasses] = output.dims;
  const data = output.data;
  const predMask = new Uint8Array(w * h);
  for (let i = 0; i < w * h; i++) {
    let maxIdx = 0, maxVal = data[i * numClasses];
    for (let c = 1; c < numClasses; c++) {
      const val = data[i * numClasses + c];
      if (val > maxVal) {
        maxVal = val;
        maxIdx = c;
      }
    }
    predMask[i] = maxIdx;
  }
  renderRawMask(predMask, w, h);
}

captureBtn.addEventListener('click', async () => {
  if (!modelLoaded) {
    captureBtn.disabled = true;
    captureBtn.textContent = 'Loading Model...';
    try {
      session = await loadModel();
      modelLoaded = true;
      captureBtn.textContent = 'Play on paper (1)';
      captureBtn.disabled = false;
      likeMask.disabled = false;
      console.log('Model loaded successfully');
    } catch (e) {
      console.error('Model load error:', e);
      captureBtn.textContent = 'Model Load Failed - Check console';
      captureBtn.disabled = false;
      alert('Failed to load model. Make sure model.onnx exists in the web_version folder.');
      return;
    }
  }
  
  // Check if video is ready
  if (video.readyState < 2) {
    alert('Camera not ready. Please wait for camera to load.');
    return;
  }
  
  try {
    const canvas = document.createElement('canvas');
    canvas.width = VIDEO_WIDTH;
    canvas.height = VIDEO_HEIGHT;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, VIDEO_WIDTH, VIDEO_HEIGHT);
    capturedImg.src = canvas.toDataURL('image/png');
    
    captureBtn.textContent = 'Processing...';
    await runInference(canvas);
    captureBtn.textContent = 'Play on paper (1)';
    console.log('Inference completed');
  } catch (e) {
    console.error('Capture/Inference error:', e);
    captureBtn.textContent = 'Error - Try Again';
    alert('Error processing image: ' + e.message);
  }
});




let opencvLoaded = false; // Flag to prevent multiple loads

function makeOpenCvReady() {
  if (opencvLoaded) {
    return Promise.resolve(); // Already loaded, skip injection
  }
  return new Promise((resolve, reject) => {
    const script = document.createElement('script');
    script.src = 'https://docs.opencv.org/4.x/opencv.js';
    script.async = true;
    script.onload = () => {
      cv['onRuntimeInitialized'] = () => {
        console.log("✅ OpenCV initialized");
        opencvLoaded = true;
        resolve();
      };
    };
    script.onerror = () => {
      reject(new Error("Failed to load OpenCV"));
    };
    document.body.appendChild(script);
  });
}

likeMask.addEventListener('click', async () => {
  likeMask.innerText = "Loading OpenCV...";
  try {
    await makeOpenCvReady();
    likeMask.innerText = "Like the mask? (2)";
    handleMakeMask();
  } catch (err) {
    likeMask.innerText = "Failed !";
  }
});


const handleMakeMask = () => {
  if (!opencvLoaded) {
    alert('OpenCV not ready yet!');
    return;
  }
  layoutCanvas.width = rawCanvas.width;
  layoutCanvas.height = rawCanvas.height;
  ctxLC.drawImage(rawCanvas, 0, 0);
  const img = new Image();
  img.onload = () => {
    ctxLC.drawImage(img, 0, 0);
    processImage();
  };
  img.src = rawCanvas.toDataURL();
};

// utility functions to process the canvas to json data
function avgX(pts) {
  return pts.reduce((sum, p) => sum + p[0], 0) / pts.length;
}

function equallySpacedPoints(p1, p2, n) {
  let points = [];
  for (let i = 0; i < n; i++) {
    let alpha = i / (n - 1);
    points.push([
      p1[0] * (1 - alpha) + p2[0] * alpha,
      p1[1] * (1 - alpha) + p2[1] * alpha
    ]);
  }
  return points;
}

function polygonArea(pts) {
  let area = 0;
  for (let i = 0; i < pts.length; i++) {
    let j = (i + 1) % pts.length;
    area += pts[i][0] * pts[j][1] - pts[j][0] * pts[i][1];
  }
  return Math.abs(area) / 2;
}

function matFromPoints(pts) {
  return cv.matFromArray(pts.length, 1, cv.CV_32SC2, [].concat(...pts.map(p => [p[0], p[1]])));
}

// main process image function, big deal

function processImage() {
  let src = cv.imread(layoutCanvas);
  const WHITE_NOTES = [
  "E5", "D5", "C5", "B4", "A4", "G4", "F4", "E4", "D4", "C4",
  "B3", "A3", "G3", "F3", "E3", "D3", "C3"
  ];
  const BLACK_NOTES = [
  "Eb5", "Db5",
  "Bb4", "Ab4", "Gb4", "Eb4", "Db4",
  "Bb3", "Ab3", "Gb3", "Eb3", "Db3"
  ];
  const colorRegions = [
    { name: "red", bgr: [0, 0, 255], num_cells: 3 },
    { name: "blue", bgr: [255, 0, 0], num_cells: 4 },
  ];
  let rawWhite = [], rawBlack = [];

  for (let region of colorRegions) {
    let lower = new cv.Mat(src.rows, src.cols, src.type(), new cv.Scalar(...region.bgr, 255));
    let upper = new cv.Mat(src.rows, src.cols, src.type(), new cv.Scalar(...region.bgr, 255));
    let mask = new cv.Mat();
    cv.inRange(src, lower, upper, mask);
    let contours = new cv.MatVector();
    let hierarchy = new cv.Mat();
    cv.findContours(mask, contours, hierarchy, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);
    for (let i = 0; i < contours.size(); i++) {
      let cnt = contours.get(i);
      if (cv.contourArea(cnt) < 500) {
        cnt.delete();
        continue;
      }

      let bestQuad = null;
      let maxArea = 0;

      for (let epsFactor = 0.01; epsFactor <= 0.05; epsFactor += 0.005) {
        let approx = new cv.Mat();
        cv.approxPolyDP(cnt, approx, epsFactor * cv.arcLength(cnt, true), true);
        if (approx.rows === 4) {
          let pts = [];
          for (let j = 0; j < 4; j++) {
            pts.push([approx.intPtr(j)[0], approx.intPtr(j)[1]]);
          }
          let area = polygonArea(pts);
          if (area > maxArea) {
            bestQuad = pts;
            maxArea = area;
          }
        }
        approx.delete();
      }
      cnt.delete();

      if (!bestQuad) continue;

      bestQuad.sort((a, b) => a[1] - b[1]);
      let top = bestQuad.slice(0, 2).sort((a, b) => a[0] - b[0]);
      let bottom = bestQuad.slice(2).sort((a, b) => a[0] - b[0]);

      let pts1 = equallySpacedPoints(bottom[0], bottom[1], region.num_cells + 1);
      let pts2 = equallySpacedPoints(top[0], top[1], region.num_cells + 1);

      for (let k = 0; k < region.num_cells; k++) {
        let poly = [ pts1[k], pts1[k+1], pts2[k+1], pts2[k] ];
        rawWhite.push({ note: "", polygon: poly, type: "white" });
      }

      let pts1b = equallySpacedPoints(bottom[0], bottom[1], region.num_cells);
      let pts2b = equallySpacedPoints(top[0], top[1], region.num_cells);
      for (let k = 0; k < region.num_cells - 1; k++) {
        let tl = pts1b[k];
        let tr = pts1b[k+1];
        const alpha = 6 / 9; // how far from top to bottom (0 = top, 1 = bottom)
        let bl = [
          tl[0] * (1 - alpha) + pts2b[k][0] * alpha,
          tl[1] * (1 - alpha) + pts2b[k][1] * alpha
        ];
        let br = [
          tr[0] * (1 - alpha) + pts2b[k+1][0] * alpha,
          tr[1] * (1 - alpha) + pts2b[k+1][1] * alpha
        ];
        rawBlack.push({ note: "", polygon: [tl, tr, br, bl], type: "black" });
      }
    }

    lower.delete(); upper.delete(); mask.delete(); contours.delete(); hierarchy.delete();
  }

  // subtract black keys from white keys
  let finalKeys = [];
  for (let wkey of rawWhite) {
    let mask = new cv.Mat.zeros(src.rows, src.cols, cv.CV_8UC1);
    let wpoly = matFromPoints(wkey.polygon);
    let wMatVec = new cv.MatVector();
    wMatVec.push_back(wpoly);
    cv.fillPoly(mask, wMatVec, new cv.Scalar(255));
    wMatVec.delete();

    for (let bkey of rawBlack) {
      let bpoly = matFromPoints(bkey.polygon);
      let bMatVec = new cv.MatVector();
      bMatVec.push_back(bpoly);
      cv.fillPoly(mask, bMatVec, new cv.Scalar(0));
      bMatVec.delete();
      bpoly.delete();
    }

    let contours = new cv.MatVector();
    let hierarchy = new cv.Mat();
    cv.findContours(mask, contours, hierarchy, cv.RETR_EXTERNAL, cv.CHAIN_APPROX_SIMPLE);

    if (contours.size() > 0) {
      // Find largest contour by area
      let largest = contours.get(0);
      let largestArea = cv.contourArea(largest);
      for (let i = 1; i < contours.size(); i++) {
        let c = contours.get(i);
        let area = cv.contourArea(c);
        if (area > largestArea) {
          largestArea = area;
          largest = c;
        }
      }
      let refinedPoly = [];
      for (let i = 0; i < largest.rows; i++) {
        let pt = largest.intPtr(i);
        refinedPoly.push([pt[0], pt[1]]);
      }
      finalKeys.push({ note: "", polygon: refinedPoly, type: "white" });
    }

    mask.delete();
    wpoly.delete();
    contours.delete();
    hierarchy.delete();
  }

  finalKeys.push(...rawBlack);

  // Assign notes sorted left to right
  let whites = finalKeys.filter(k => k.type === "white").sort((a,b) => avgX(a.polygon) - avgX(b.polygon));
  let blacks = finalKeys.filter(k => k.type === "black").sort((a,b) => avgX(a.polygon) - avgX(b.polygon));

  whites.forEach((k, i) => k.note = WHITE_NOTES[i] || `white${i}`);
  blacks.forEach((k, i) => k.note = BLACK_NOTES[i] || `black${i}`);

  exportKeys = [...whites, ...blacks];
  
  // Draw the detected keys
  drawAllKeys();
  
  // Show success message
  likeMask.textContent = "Keys Detected! ✓";
  likeMask.style.backgroundColor = '#4CAF50';
  likeMask.style.color = 'white';
  
  setTimeout(() => {
    likeMask.textContent = "Like the mask? (2)";
    likeMask.style.backgroundColor = '';
    likeMask.style.color = '';
  }, 2000);
  
  src.delete();
}

// ========== SIDEBAR (CONTROLS DRAWER) ==========
function initSidebarControls() {
  const sidebar = document.getElementById('controlSidebar');
  const toggleButton = document.getElementById('sidebarToggle');
  const closeButton = document.getElementById('sidebarClose');
  const backdrop = document.getElementById('sidebarBackdrop');

  if (!sidebar || !toggleButton) {
    return;
  }

  const openSidebar = () => {
    sidebar.classList.add('open');
    backdrop?.classList.add('visible');
    toggleButton.setAttribute('aria-expanded', 'true');
  };

  const closeSidebar = () => {
    sidebar.classList.remove('open');
    backdrop?.classList.remove('visible');
    toggleButton.setAttribute('aria-expanded', 'false');
  };

  toggleButton.addEventListener('click', () => {
    if (sidebar.classList.contains('open')) {
      closeSidebar();
    } else {
      openSidebar();
    }
  });

  closeButton?.addEventListener('click', closeSidebar);

  backdrop?.addEventListener('click', closeSidebar);

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });
}

// Initialize sidebar when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initSidebarControls);
} else {
  initSidebarControls();
}