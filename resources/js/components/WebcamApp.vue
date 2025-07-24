<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Smart Webcam Assistant</h1>
        <p class="text-lg text-gray-600">Capture photos and get AI-powered audio descriptions</p>
      </div>

      <!-- API Configuration Panel -->
      <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
          </svg>
          Secure API Configuration
        </h2>
        
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
          <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
            </svg>
            <div>
              <p class="text-blue-800 font-medium">🔒 Your keys stay secure</p>
              <p class="text-blue-700 text-sm">API keys are stored only in your browser and never sent to our servers. All API calls go directly from your browser to OpenAI/ElevenLabs.</p>
            </div>
          </div>
        </div>

        <div class="grid md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              OpenAI API Key (for image analysis)
            </label>
            <div class="relative">
              <input
                v-model="userApiKeys.openai"
                :type="showKeys.openai ? 'text' : 'password'"
                placeholder="sk-..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <button
                @click="showKeys.openai = !showKeys.openai"
                type="button"
                class="absolute right-2 top-2 text-gray-500 hover:text-gray-700"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="!showKeys.openai" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                  <path v-else d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                </svg>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              Get yours at <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">platform.openai.com</a>
            </p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              ElevenLabs API Key (for speech)
            </label>
            <div class="relative">
              <input
                v-model="userApiKeys.elevenlabs"
                :type="showKeys.elevenlabs ? 'text' : 'password'"
                placeholder="..."
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
              <button
                @click="showKeys.elevenlabs = !showKeys.elevenlabs"
                type="button"
                class="absolute right-2 top-2 text-gray-500 hover:text-gray-700"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="!showKeys.elevenlabs" d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/>
                  <path v-else d="M12 7c2.76 0 5 2.24 5 5 0 .65-.13 1.26-.36 1.83l2.92 2.92c1.51-1.26 2.7-2.89 3.43-4.75-1.73-4.39-6-7.5-11-7.5-1.4 0-2.74.25-3.98.7l2.16 2.16C10.74 7.13 11.35 7 12 7zM2 4.27l2.28 2.28.46.46C3.08 8.3 1.78 10.02 1 12c1.73 4.39 6 7.5 11 7.5 1.55 0 3.03-.3 4.38-.84l.42.42L19.73 22 21 20.73 3.27 3 2 4.27zM7.53 9.8l1.55 1.55c-.05.21-.08.43-.08.65 0 1.66 1.34 3 3 3 .22 0 .44-.03.65-.08l1.55 1.55c-.67.33-1.41.53-2.2.53-2.76 0-5-2.24-5-5 0-.79.2-1.53.53-2.2zm4.31-.78l3.15 3.15.02-.16c0-1.66-1.34-3-3-3l-.17.01z"/>
                </svg>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">
              Get yours at <a href="https://elevenlabs.io/app/api-keys" target="_blank" class="text-blue-600 hover:underline">elevenlabs.io</a>
            </p>
          </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <label class="flex items-center">
              <input
                v-model="persistKeys"
                type="checkbox"
                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              >
              <span class="ml-2 text-sm text-gray-700">Remember keys (stored locally in browser)</span>
            </label>
          </div>
          
          <div class="flex space-x-2">
            <button
              @click="clearKeys"
              class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded hover:bg-gray-50"
            >
              Clear Keys
            </button>
            <button
              @click="validateKeys"
              :disabled="!userApiKeys.openai || !userApiKeys.elevenlabs"
              class="px-3 py-1 text-sm text-white bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 rounded"
            >
              Test Keys
            </button>
          </div>
        </div>

        <div v-if="keyValidationStatus" class="mt-3">
          <div 
            :class="[
              'p-3 rounded-lg text-sm',
              keyValidationStatus.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
              keyValidationStatus.type === 'error' ? 'bg-red-50 text-red-700 border border-red-200' :
              'bg-yellow-50 text-yellow-700 border border-yellow-200'
            ]"
          >
            {{ keyValidationStatus.message }}
          </div>
        </div>
      </div>

      <!-- Main Interface -->
      <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <!-- Webcam Section -->
        <div class="relative mb-6">
          <div class="aspect-video bg-gray-900 rounded-xl overflow-hidden relative">
            <!-- Video Element -->
            <video
              ref="videoElement"
              v-show="isVideoActive"
              autoplay
              playsinline
              muted
              class="w-full h-full object-cover"
            ></video>
            
            <!-- Placeholder when video is off -->
            <div
              v-show="!isVideoActive"
              class="w-full h-full flex items-center justify-center bg-gray-800"
            >
              <div class="text-center">
                <svg class="w-24 h-24 text-gray-600 mx-auto mb-4" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4zM14 13h-3v3H9v-3H6v-2h3V8h2v3h3v2z"/>
                </svg>
                <p class="text-gray-400 text-lg">Camera is off</p>
                <p class="text-gray-500 text-sm">Click "Start Camera" to begin</p>
              </div>
            </div>

            <!-- Canvas for photo capture (hidden) -->
            <canvas ref="canvasElement" class="hidden"></canvas>
          </div>

          <!-- Camera Controls -->
          <div class="flex justify-center space-x-4 mt-6">
            <button
              @click="toggleCamera"
              :disabled="isLoading"
              :class="[
                'px-6 py-3 rounded-lg font-medium transition-all duration-200 flex items-center space-x-2',
                isVideoActive 
                  ? 'bg-red-500 hover:bg-red-600 text-white' 
                  : 'bg-green-500 hover:bg-green-600 text-white',
                isLoading ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 shadow-lg'
              ]"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path v-if="!isVideoActive" d="M17 10.5V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11l-4 4z"/>
                <path v-else d="M21 6.5l-4 4V7c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1v10c0 .55.45 1 1 1h12c.55 0 1-.45 1-1v-3.5l4 4v-11z"/>
              </svg>
              <span>{{ isVideoActive ? 'Stop Camera' : 'Start Camera' }}</span>
            </button>

            <button
              @click="capturePhoto"
              :disabled="!isVideoActive || isLoading || !hasValidKeys"
              class="px-6 py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 hover:scale-105 shadow-lg"
            >
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 15.2l-3.5-2.7L12 9.8l3.5 2.7L12 15.2zM9 2l3 3h4c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V7c0-1.1.9-2 2-2h4l3-3z"/>
              </svg>
              <span>Capture Photo</span>
            </button>
          </div>
          
          <div v-if="!hasValidKeys" class="text-center mt-3">
            <p class="text-sm text-orange-600">Please enter your API keys above to use the assistant</p>
          </div>
        </div>

        <!-- Status Messages -->
        <div v-if="statusMessage" class="mb-6">
          <div 
            :class="[
              'p-4 rounded-lg border',
              statusType === 'error' ? 'bg-red-50 border-red-200 text-red-700' :
              statusType === 'success' ? 'bg-green-50 border-green-200 text-green-700' :
              'bg-blue-50 border-blue-200 text-blue-700'
            ]"
          >
            {{ statusMessage }}
          </div>
        </div>

        <!-- Processing Status -->
        <div v-if="isProcessing" class="mb-6">
          <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center space-x-3">
              <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-yellow-600"></div>
              <span class="text-yellow-700 font-medium">{{ processingStatus }}</span>
            </div>
          </div>
        </div>

        <!-- Audio Playback -->
        <div v-if="audioUrl" class="mb-6">
          <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-green-800 mb-3">Audio Description</h3>
            <audio controls class="w-full" :src="audioUrl" autoplay></audio>
          </div>
        </div>
      </div>

      <!-- Captured Photos Section -->
      <div v-if="capturedPhotos.length > 0" class="bg-white rounded-2xl shadow-xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Recent Captures</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <div
            v-for="(photo, index) in capturedPhotos"
            :key="index"
            class="relative group"
          >
            <img 
              :src="photo.dataUrl" 
              :alt="`Captured photo ${index + 1}`"
              class="w-full h-48 object-cover rounded-lg shadow-md group-hover:shadow-lg transition-shadow"
            >
            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all rounded-lg flex items-center justify-center">
              <button
                @click="processPhoto(photo)"
                :disabled="isProcessing || !hasValidKeys"
                class="opacity-0 group-hover:opacity-100 transition-opacity bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 disabled:bg-gray-300 disabled:text-gray-500"
              >
                Process Again
              </button>
            </div>
            <div class="absolute top-2 right-2">
              <span class="bg-black bg-opacity-70 text-white text-xs px-2 py-1 rounded">
                {{ new Date(photo.timestamp).toLocaleTimeString() }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';

// Reactive state
const videoElement = ref(null);
const canvasElement = ref(null);
const isVideoActive = ref(false);
const isLoading = ref(false);
const isProcessing = ref(false);
const processingStatus = ref('');
const statusMessage = ref('');
const statusType = ref('info');
const capturedPhotos = ref([]);
const audioUrl = ref('');

// API Key Management
const userApiKeys = ref({
  openai: '',
  elevenlabs: ''
});

const showKeys = ref({
  openai: false,
  elevenlabs: false
});

const persistKeys = ref(false);
const keyValidationStatus = ref(null);

// Computed properties
const hasValidKeys = computed(() => {
  return userApiKeys.value.openai.trim() !== '' && userApiKeys.value.elevenlabs.trim() !== '';
});

let mediaStream = null;

// API Key Management Functions
const saveKeysToStorage = () => {
  if (persistKeys.value) {
    localStorage.setItem('webcam-assistant-keys', JSON.stringify({
      openai: userApiKeys.value.openai,
      elevenlabs: userApiKeys.value.elevenlabs,
      persist: true
    }));
  } else {
    localStorage.removeItem('webcam-assistant-keys');
  }
};

const loadKeysFromStorage = () => {
  try {
    const stored = localStorage.getItem('webcam-assistant-keys');
    if (stored) {
      const keys = JSON.parse(stored);
      if (keys.persist) {
        userApiKeys.value.openai = keys.openai || '';
        userApiKeys.value.elevenlabs = keys.elevenlabs || '';
        persistKeys.value = true;
      }
    }
  } catch (error) {
    console.warn('Failed to load stored keys:', error);
  }
};

const clearKeys = () => {
  userApiKeys.value.openai = '';
  userApiKeys.value.elevenlabs = '';
  persistKeys.value = false;
  localStorage.removeItem('webcam-assistant-keys');
  keyValidationStatus.value = null;
  showStatus('API keys cleared', 'info');
};

const validateKeys = async () => {
  keyValidationStatus.value = { type: 'info', message: 'Testing API keys...' };
  
  try {
    // Test OpenAI key with a simple request
    const openaiTest = await fetch('https://api.openai.com/v1/models', {
      headers: {
        'Authorization': `Bearer ${userApiKeys.value.openai}`,
      }
    });

    if (!openaiTest.ok) {
      throw new Error('OpenAI API key is invalid');
    }

    // Test ElevenLabs key
    const elevenTest = await fetch('https://api.elevenlabs.io/v1/voices', {
      headers: {
        'xi-api-key': userApiKeys.value.elevenlabs
      }
    });

    if (!elevenTest.ok) {
      throw new Error('ElevenLabs API key is invalid');
    }

    keyValidationStatus.value = { 
      type: 'success', 
      message: '✅ Both API keys are valid and working!' 
    };

    if (persistKeys.value) {
      saveKeysToStorage();
    }

  } catch (error) {
    keyValidationStatus.value = { 
      type: 'error', 
      message: `❌ Validation failed: ${error.message}` 
    };
  }
};

// Watch for key persistence changes
watch(persistKeys, (newValue) => {
  if (newValue) {
    saveKeysToStorage();
  } else {
    localStorage.removeItem('webcam-assistant-keys');
  }
});

// Camera functionality
const toggleCamera = async () => {
  if (isVideoActive.value) {
    stopCamera();
  } else {
    await startCamera();
  }
};

const startCamera = async () => {
  try {
    isLoading.value = true;
    statusMessage.value = '';
    
    mediaStream = await navigator.mediaDevices.getUserMedia({ 
      video: { 
        width: { ideal: 1280 },
        height: { ideal: 720 },
        facingMode: 'user'
      },
      audio: false 
    });
    
    videoElement.value.srcObject = mediaStream;
    isVideoActive.value = true;
    showStatus('Camera started successfully!', 'success');
  } catch (error) {
    console.error('Error accessing camera:', error);
    showStatus('Failed to access camera. Please check permissions.', 'error');
  } finally {
    isLoading.value = false;
  }
};

const stopCamera = () => {
  if (mediaStream) {
    mediaStream.getTracks().forEach(track => track.stop());
    mediaStream = null;
  }
  isVideoActive.value = false;
  videoElement.value.srcObject = null;
  showStatus('Camera stopped', 'info');
};

const capturePhoto = async () => {
  if (!isVideoActive.value || !hasValidKeys.value) return;
  
  try {
    const video = videoElement.value;
    const canvas = canvasElement.value;
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    // Convert to data URL
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
    
    // Store photo
    const photo = {
      dataUrl,
      timestamp: Date.now(),
      processed: false
    };
    
    capturedPhotos.value.unshift(photo);
    
    // Keep only last 10 photos
    if (capturedPhotos.value.length > 10) {
      capturedPhotos.value = capturedPhotos.value.slice(0, 10);
    }
    
    showStatus('Photo captured! Processing...', 'success');
    
    // Process the photo automatically
    await processPhoto(photo);
    
  } catch (error) {
    console.error('Error capturing photo:', error);
    showStatus('Failed to capture photo', 'error');
  }
};

const processPhoto = async (photo) => {
  if (!hasValidKeys.value) {
    showStatus('Please enter your API keys first', 'error');
    return;
  }

  try {
    isProcessing.value = true;
    processingStatus.value = 'Analyzing image with OpenAI...';
    
    // Convert data URL to base64
    const base64Data = photo.dataUrl.split(',')[1];
    
    // Step 1: Analyze image with OpenAI Vision API (direct from browser)
    const imageDescription = await analyzeImageWithOpenAI(base64Data);
    
    if (!imageDescription) {
      throw new Error('Failed to analyze image');
    }

    processingStatus.value = 'Generating speech with ElevenLabs...';
    
    // Step 2: Convert text to speech with ElevenLabs (direct from browser)
    const audioBlob = await convertTextToSpeechWithElevenLabs(imageDescription);
    
    if (audioBlob) {
      // Create local URL for audio playback
      audioUrl.value = URL.createObjectURL(audioBlob);
      showStatus('Image analyzed and audio generated successfully!', 'success');
    } else {
      showStatus('Image analyzed, but audio generation failed', 'error');
    }
    
    photo.processed = true;
    
  } catch (error) {
    console.error('Error processing photo:', error);
    showStatus('Failed to process photo: ' + error.message, 'error');
  } finally {
    isProcessing.value = false;
    processingStatus.value = '';
  }
};

// Direct API calls (no server involvement)
const analyzeImageWithOpenAI = async (base64Data) => {
  try {
    const response = await fetch('https://api.openai.com/v1/chat/completions', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${userApiKeys.value.openai}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        model: 'gpt-4o-mini',
        messages: [
          {
            role: 'user',
            content: [
              {
                type: 'text',
                text: 'Please describe this image in detail. Focus on what you see, including objects, people, activities, colors, and the overall scene. Make it engaging and descriptive as if you\'re helping someone who cannot see the image.'
              },
              {
                type: 'image_url',
                image_url: {
                  url: `data:image/jpeg;base64,${base64Data}`
                }
              }
            ]
          }
        ],
        max_tokens: 300
      })
    });

    if (!response.ok) {
      throw new Error(`OpenAI API error: ${response.statusText}`);
    }

    const data = await response.json();
    return data.choices?.[0]?.message?.content;
  } catch (error) {
    console.error('OpenAI API error:', error);
    throw error;
  }
};

const convertTextToSpeechWithElevenLabs = async (text) => {
  try {
    const voiceId = 'pNInz6obpgDQGcFmaJgB'; // Adam voice
    
    const response = await fetch(`https://api.elevenlabs.io/v1/text-to-speech/${voiceId}`, {
      method: 'POST',
      headers: {
        'Accept': 'audio/mpeg',
        'Content-Type': 'application/json',
        'xi-api-key': userApiKeys.value.elevenlabs
      },
      body: JSON.stringify({
        text: text,
        model_id: 'eleven_monolingual_v1',
        voice_settings: {
          stability: 0.5,
          similarity_boost: 0.5
        }
      })
    });

    if (!response.ok) {
      throw new Error(`ElevenLabs API error: ${response.statusText}`);
    }

    return await response.blob();
  } catch (error) {
    console.error('ElevenLabs API error:', error);
    throw error;
  }
};

const showStatus = (message, type = 'info') => {
  statusMessage.value = message;
  statusType.value = type;
  
  // Auto-hide after 5 seconds
  setTimeout(() => {
    statusMessage.value = '';
  }, 5000);
};

// Lifecycle
onMounted(() => {
  // Check if getUserMedia is supported
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    showStatus('Webcam access is not supported in this browser', 'error');
  }
  
  // Load stored API keys
  loadKeysFromStorage();
});

onUnmounted(() => {
  stopCamera();
  
  // Clean up audio URLs to prevent memory leaks
  if (audioUrl.value && audioUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(audioUrl.value);
  }
});
</script> 