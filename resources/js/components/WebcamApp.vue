<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-4">
    <div class="max-w-4xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800 mb-2">Smart Webcam Assistant</h1>
        <p class="text-lg text-gray-600">Secure backend proxy with encrypted API key management</p>
      </div>

      <!-- Authentication Section -->
      <div v-if="!isAuthenticated" class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex justify-center mb-6">
          <div class="flex border-b border-gray-200">
            <button
              @click="authMode = 'login'"
              :class="[
                'px-6 py-2 text-sm font-medium',
                authMode === 'login' 
                  ? 'text-blue-600 border-b-2 border-blue-600' 
                  : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              Login
            </button>
            <button
              @click="authMode = 'register'"
              :class="[
                'px-6 py-2 text-sm font-medium',
                authMode === 'register' 
                  ? 'text-blue-600 border-b-2 border-blue-600' 
                  : 'text-gray-500 hover:text-gray-700'
              ]"
            >
              Register
            </button>
          </div>
        </div>

        <!-- Login Form -->
        <form v-if="authMode === 'login'" @submit.prevent="login" class="space-y-4">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Login to Your Account</h2>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="loginForm.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="your@email.com"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              v-model="loginForm.password"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="••••••••"
            >
          </div>

          <div class="flex items-center">
            <input
              v-model="loginForm.remember"
              type="checkbox"
              class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <label class="ml-2 text-sm text-gray-700">Remember me</label>
          </div>

          <button
            type="submit"
            :disabled="authLoading"
            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-medium"
          >
            {{ authLoading ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <!-- Register Form -->
        <form v-if="authMode === 'register'" @submit.prevent="register" class="space-y-4">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Create New Account</h2>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
            <input
              v-model="registerForm.name"
              type="text"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="John Doe"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input
              v-model="registerForm.email"
              type="email"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="your@email.com"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input
              v-model="registerForm.password"
              type="password"
              required
              minlength="8"
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="••••••••"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <input
              v-model="registerForm.password_confirmation"
              type="password"
              required
              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              placeholder="••••••••"
            >
          </div>

          <button
            type="submit"
            :disabled="authLoading"
            class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-medium"
          >
            {{ authLoading ? 'Creating Account...' : 'Create Account' }}
          </button>
        </form>

        <!-- Auth Status Messages -->
        <div v-if="authMessage" class="mt-4">
          <div 
            :class="[
              'p-3 rounded-lg text-sm',
              authMessageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
              'bg-red-50 text-red-700 border border-red-200'
            ]"
          >
            {{ authMessage }}
          </div>
        </div>
      </div>

      <!-- Main App (Authenticated Users) -->
      <div v-if="isAuthenticated">
        <!-- User Dashboard -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
          <div class="flex justify-between items-center mb-4">
            <div>
              <h2 class="text-xl font-bold text-gray-800">Welcome, {{ user.name }}!</h2>
              <p class="text-gray-600">{{ user.email }}</p>
            </div>
            <div class="flex space-x-2">
              <button
                @click="showApiKeyManager = !showApiKeyManager"
                class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-200 rounded hover:bg-blue-50"
              >
                {{ showApiKeyManager ? 'Hide' : 'Manage' }} API Keys
              </button>
              <button
                @click="logout"
                class="px-3 py-1 text-sm text-red-600 hover:text-red-800 border border-red-200 rounded hover:bg-red-50"
              >
                Logout
              </button>
            </div>
          </div>

          <!-- API Key Status -->
          <div class="grid md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-3">
              <div class="flex items-center space-x-2">
                <svg :class="['w-4 h-4', user.has_openai_key ? 'text-green-500' : 'text-red-500']" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="user.has_openai_key" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
                  <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/>
                </svg>
                <span class="text-sm font-medium">OpenAI API</span>
                <span :class="['text-xs px-2 py-1 rounded', user.has_openai_key ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                  {{ user.has_openai_key ? 'Configured' : 'Not Configured' }}
                </span>
              </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
              <div class="flex items-center space-x-2">
                <svg :class="['w-4 h-4', user.has_elevenlabs_key ? 'text-green-500' : 'text-red-500']" fill="currentColor" viewBox="0 0 24 24">
                  <path v-if="user.has_elevenlabs_key" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
                  <path v-else d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11H7v-2h10v2z"/>
                </svg>
                <span class="text-sm font-medium">ElevenLabs API</span>
                <span :class="['text-xs px-2 py-1 rounded', user.has_elevenlabs_key ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700']">
                  {{ user.has_elevenlabs_key ? 'Configured' : 'Not Configured' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- API Key Manager -->
        <div v-if="showApiKeyManager" class="bg-white rounded-2xl shadow-xl p-6 mb-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
            </svg>
            Secure API Key Management
          </h2>
          
          <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <div class="flex items-start">
              <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.58L19 8l-9 9z"/>
              </svg>
              <div>
                <p class="text-blue-800 font-medium">🔒 Maximum Security</p>
                <p class="text-blue-700 text-sm">API keys are encrypted and stored securely on our backend. All API calls go through our secure proxy.</p>
              </div>
            </div>
          </div>

          <form @submit.prevent="saveApiKeys" class="space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  OpenAI API Key (for image analysis)
                </label>
                <div class="flex space-x-2">
                  <input
                    v-model="apiKeyForm.openai_key"
                    type="password"
                    placeholder="sk-..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                  <button
                    type="button"
                    @click="saveIndividualKey('openai')"
                    :disabled="!apiKeyForm.openai_key || apiKeyLoading"
                    class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg text-sm"
                  >
                    Save
                  </button>
                  <button
                    type="button"
                    @click="testIndividualKey('openai')"
                    :disabled="!user.has_openai_key || apiKeyLoading"
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg text-sm"
                  >
                    Test
                  </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  Get yours at <a href="https://platform.openai.com/api-keys" target="_blank" class="text-blue-600 hover:underline">platform.openai.com</a>
                </p>
                <div v-if="openaiStatus" class="mt-1">
                  <div 
                    :class="[
                      'p-2 rounded text-xs',
                      openaiStatus.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
                      'bg-red-50 text-red-700 border border-red-200'
                    ]"
                  >
                    {{ openaiStatus.message }}
                  </div>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  ElevenLabs API Key (for speech)
                </label>
                <div class="flex space-x-2">
                  <input
                    v-model="apiKeyForm.elevenlabs_key"
                    type="password"
                    placeholder="..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                  <button
                    type="button"
                    @click="saveIndividualKey('elevenlabs')"
                    :disabled="!apiKeyForm.elevenlabs_key || apiKeyLoading"
                    class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg text-sm"
                  >
                    Save
                  </button>
                  <button
                    type="button"
                    @click="testIndividualKey('elevenlabs')"
                    :disabled="!user.has_elevenlabs_key || apiKeyLoading"
                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg text-sm"
                  >
                    Test
                  </button>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                  Get yours at <a href="https://elevenlabs.io/app/api-keys" target="_blank" class="text-blue-600 hover:underline">elevenlabs.io</a>
                </p>
                <div v-if="elevenLabsStatus" class="mt-1">
                  <div 
                    :class="[
                      'p-2 rounded text-xs',
                      elevenLabsStatus.type === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
                      'bg-red-50 text-red-700 border border-red-200'
                    ]"
                  >
                    {{ elevenLabsStatus.message }}
                  </div>
                </div>
              </div>
            </div>

            <div class="flex space-x-2">
              <button
                type="submit"
                :disabled="apiKeyLoading"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg font-medium"
              >
                {{ apiKeyLoading ? 'Saving...' : 'Save All API Keys' }}
              </button>
              <button
                type="button"
                @click="validateApiKeys"
                :disabled="apiKeyLoading || (!user.has_openai_key && !user.has_elevenlabs_key)"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-medium"
              >
                {{ apiKeyLoading ? 'Validating...' : 'Test All Keys' }}
              </button>
              <button
                type="button"
                @click="deleteApiKeys"
                :disabled="apiKeyLoading || (!user.has_openai_key && !user.has_elevenlabs_key)"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white rounded-lg font-medium"
              >
                Clear All Keys
              </button>
            </div>
          </form>

          <div v-if="apiKeyMessage" class="mt-3">
            <div 
              :class="[
                'p-3 rounded-lg text-sm',
                apiKeyMessageType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
                apiKeyMessageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' :
                'bg-yellow-50 text-yellow-700 border border-yellow-200'
              ]"
            >
              {{ apiKeyMessage }}
            </div>
          </div>
        </div>

        <!-- Advanced Settings Panel -->
        <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 15.5A3.5 3.5 0 0 1 8.5 12A3.5 3.5 0 0 1 12 8.5a3.5 3.5 0 0 1 3.5 3.5 3.5 3.5 0 0 1-3.5 3.5m7.43-2.53c.04-.32.07-.64.07-.97c0-.33-.03-.66-.07-1l2.11-1.63c.19-.15.24-.42.12-.64l-2-3.46c-.12-.22-.39-.31-.61-.22l-2.49 1c-.52-.39-1.06-.73-1.69-.98l-.37-2.65A.506.506 0 0 0 14 2h-4c-.25 0-.46.18-.5.42l-.37 2.65c-.63.25-1.17.59-1.69.98l-2.49-1c-.22-.09-.49 0-.61.22l-2 3.46c-.13.22-.07.49.12.64L4.57 11c-.04.34-.07.67-.07 1c0 .33.03.65.07.97l-2.11 1.66c-.19.15-.25.42-.12.64l2 3.46c.12.22.39.3.61.22l2.49-1.01c.52.4 1.06.74 1.69.99l.37 2.65c.04.24.25.42.5.42h4c.25 0 .46-.18.5-.42l.37-2.65c.63-.26 1.17-.59 1.69-.99l2.49 1.01c.22.08.49 0 .61-.22l2-3.46c.12-.22.07-.49-.12-.64l-2.11-1.66Z"/>
              </svg>
              Advanced Settings
            </h2>
            <button
              @click="showAdvancedSettings = !showAdvancedSettings"
              class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 border border-blue-200 rounded hover:bg-blue-50"
            >
              {{ showAdvancedSettings ? 'Hide' : 'Show' }}
            </button>
          </div>

          <div v-show="showAdvancedSettings" class="space-y-4">
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
              <p class="text-sm text-gray-600 mb-4">
                ⚙️ Customize the AI behavior and voice settings for backend processing.
              </p>
              
              <div class="grid md:grid-cols-3 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    GPT Model
                  </label>
                  <select
                    v-model="advancedSettings.gptModel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="gpt-4o-mini">GPT-4o Mini (Fast & Cheap)</option>
                    <option value="gpt-4o">GPT-4o (High Quality)</option>
                    <option value="gpt-4-turbo">GPT-4 Turbo</option>
                    <option value="gpt-4">GPT-4 (Legacy)</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Model used for image analysis</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Voice ID
                  </label>
                  <select
                    v-model="advancedSettings.voiceId"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                    <option value="pNInz6obpgDQGcFmaJgB">Adam (Male, Warm)</option>
                    <option value="21m00Tcm4TlvDq8ikWAM">Rachel (Female, Calm)</option>
                    <option value="AZnzlk1XvdvUeBnXmlld">Domi (Female, Strong)</option>
                    <option value="EXAVITQu4vr4xnSDxMaL">Bella (Female, Soft)</option>
                    <option value="ErXwobaYiN019PkySvjV">Antoni (Male, Deep)</option>
                    <option value="MF3mGyEYCl7XYWbV9V6O">Elli (Female, Young)</option>
                    <option value="TxGEqnHWrfWFTfGW9XjX">Josh (Male, Casual)</option>
                    <option value="VR6AewLTigWG4xSOukaG">Arnold (Male, Confident)</option>
                    <option value="pqHfZKP75CvOlQylNhV4">Bill (Male, Documentarian)</option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">ElevenLabs voice for speech</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Custom Voice ID
                  </label>
                  <input
                    v-model="advancedSettings.customVoiceId"
                    type="text"
                    placeholder="Enter custom voice ID..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  >
                  <p class="text-xs text-gray-500 mt-1">Override with custom voice</p>
                </div>
              </div>

              <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Custom Prompt
                </label>
                <textarea
                  v-model="advancedSettings.promptMessage"
                  rows="3"
                  placeholder="Enter custom prompt for image analysis..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                ></textarea>
                <p class="text-xs text-gray-500 mt-1">Instructions for how the AI should describe images</p>
              </div>

              <div class="mt-4 flex space-x-2">
                <button
                  @click="resetAdvancedSettings"
                  class="px-3 py-1 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded hover:bg-gray-50"
                >
                  Reset to Defaults
                </button>
                <button
                  @click="saveAdvancedSettings"
                  class="px-3 py-1 text-sm text-white bg-green-600 hover:bg-green-700 rounded"
                >
                  Save Settings
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Webcam Interface -->
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
                :disabled="!isVideoActive || isLoading || !hasValidApiKeys"
                class="px-6 py-3 bg-blue-500 hover:bg-blue-600 disabled:bg-gray-400 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-all duration-200 flex items-center space-x-2 hover:scale-105 shadow-lg"
              >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 15.2l-3.5-2.7L12 9.8l3.5 2.7L12 15.2zM9 2l3 3h4c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H6c-1.1 0-2-.9-2-2V7c0-1.1.9-2 2-2h4l3-3z"/>
                </svg>
                <span>Capture Photo</span>
              </button>
            </div>
            
            <div v-if="!hasValidApiKeys" class="text-center mt-3">
              <p class="text-sm text-orange-600">Please configure your API keys above to use the assistant</p>
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

          <!-- API Error Details -->
          <div v-if="apiError" class="mb-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
              <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                  <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                  </svg>
                </div>
                <div class="flex-1">
                  <h3 class="text-sm font-medium text-red-800">
                    {{ apiError.service === 'openai' ? 'OpenAI' : 'ElevenLabs' }} API Error
                  </h3>
                  <div class="mt-2 text-sm text-red-700">
                    <div class="mb-2">
                      <span class="font-medium">Type:</span> 
                      <span 
                        :class="[
                          'px-2 py-1 rounded text-xs ml-1',
                          apiError.type === 'insufficient_quota' || apiError.type === 'quota_exceeded' ? 'bg-orange-100 text-orange-800' :
                          apiError.type === 'ivc_not_permitted' ? 'bg-purple-100 text-purple-800' :
                          'bg-red-100 text-red-800'
                        ]"
                      >
                        {{ apiError.type }}
                      </span>
                    </div>
                    <div class="mb-3">
                      <span class="font-medium">Details:</span> {{ apiError.message }}
                    </div>
                    
                    <!-- Helpful suggestions based on error type -->
                    <div v-if="getErrorSuggestion(apiError)" class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded">
                      <div class="flex items-start space-x-2">
                        <div class="flex-shrink-0">
                          <svg class="w-4 h-4 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                          </svg>
                        </div>
                        <div class="text-sm text-blue-700">
                          <span class="font-medium">Suggestion:</span> {{ getErrorSuggestion(apiError) }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <button 
                    @click="apiError = null" 
                    class="mt-3 text-sm text-red-600 hover:text-red-500 font-medium"
                  >
                    Dismiss
                  </button>
                </div>
              </div>
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
                  :disabled="isProcessing || !hasValidApiKeys"
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
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed, watch } from 'vue';

// Authentication state
const isAuthenticated = ref(false);
const user = ref(null);
const authMode = ref('login');
const authLoading = ref(false);
const authMessage = ref('');
const authMessageType = ref('success');

// Auth forms
const loginForm = ref({
  email: '',
  password: '',
  remember: false
});

const registerForm = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
});

// API Key management
const showApiKeyManager = ref(false);
const apiKeyForm = ref({
  openai_key: '',
  elevenlabs_key: ''
});
const apiKeyLoading = ref(false);
const apiKeyMessage = ref('');
const apiKeyMessageType = ref('');
const openaiStatus = ref(null);
const elevenLabsStatus = ref(null);

// Webcam and processing state
const videoElement = ref(null);
const canvasElement = ref(null);
const isVideoActive = ref(false);
const isLoading = ref(false);
const isProcessing = ref(false);
const processingStatus = ref('');
const statusMessage = ref('');
const statusType = ref('info');
const apiError = ref(null);
const capturedPhotos = ref([]);
const audioUrl = ref('');

// Advanced Settings
const showAdvancedSettings = ref(false);
const advancedSettings = ref({
  gptModel: 'gpt-4o-mini',
  promptMessage: 'Please describe this image in detail. Focus on what you see, including objects, people, activities, colors, and the overall scene. Make it engaging and descriptive as if you\'re helping someone who cannot see the image.',
  voiceId: 'pNInz6obpgDQGcFmaJgB',
  customVoiceId: ''
});

// Computed properties
const hasValidApiKeys = computed(() => {
  return user.value?.has_openai_key && user.value?.has_elevenlabs_key;
});

let mediaStream = null;

// Authentication functions
const login = async () => {
  authLoading.value = true;
  authMessage.value = '';
  
  try {
    const response = await fetch('/api/auth/login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify(loginForm.value)
    });

    const data = await response.json();

    if (data.success) {
      isAuthenticated.value = true;
      user.value = data.user;
      authMessage.value = data.message;
      authMessageType.value = 'success';
      await fetchUser(); // Refresh user data
    } else {
      authMessage.value = data.message;
      authMessageType.value = 'error';
    }
  } catch (error) {
    authMessage.value = 'Login failed. Please try again.';
    authMessageType.value = 'error';
  } finally {
    authLoading.value = false;
  }
};

const register = async () => {
  authLoading.value = true;
  authMessage.value = '';
  
  try {
    const response = await fetch('/api/auth/register', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify(registerForm.value)
    });

    const data = await response.json();

    if (data.success) {
      isAuthenticated.value = true;
      user.value = data.user;
      authMessage.value = data.message;
      authMessageType.value = 'success';
    } else {
      authMessage.value = data.message || 'Registration failed';
      authMessageType.value = 'error';
    }
  } catch (error) {
    authMessage.value = 'Registration failed. Please try again.';
    authMessageType.value = 'error';
  } finally {
    authLoading.value = false;
  }
};

const logout = async () => {
  try {
    await fetch('/api/auth/logout', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    isAuthenticated.value = false;
    user.value = null;
    showApiKeyManager.value = false;
    stopCamera();
  } catch (error) {
    console.error('Logout failed:', error);
  }
};

const fetchUser = async () => {
  try {
    const response = await fetch('/api/auth/user');
    const data = await response.json();
    
    if (data.success) {
      user.value = data.user;
    }
  } catch (error) {
    console.error('Failed to fetch user:', error);
  }
};

const checkAuthStatus = async () => {
  try {
    const response = await fetch('/api/auth/check');
    const data = await response.json();
    
    if (data.authenticated) {
      isAuthenticated.value = true;
      user.value = data.user;
      await fetchUser(); // Get full user data
    }
  } catch (error) {
    console.error('Auth check failed:', error);
  }
};

// API Key management functions
const saveApiKeys = async () => {
  apiKeyLoading.value = true;
  apiKeyMessage.value = '';
  
  try {
    const response = await fetch('/api/api-keys', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify(apiKeyForm.value)
    });

    const data = await response.json();

    if (data.success) {
      apiKeyMessage.value = data.message;
      apiKeyMessageType.value = 'success';
      apiKeyForm.value.openai_key = '';
      apiKeyForm.value.elevenlabs_key = '';
      await fetchUser(); // Refresh user data
    } else {
      apiKeyMessage.value = data.message;
      apiKeyMessageType.value = 'error';
    }
  } catch (error) {
    apiKeyMessage.value = 'Failed to save API keys';
    apiKeyMessageType.value = 'error';
  } finally {
    apiKeyLoading.value = false;
  }
};

const validateApiKeys = async () => {
  apiKeyLoading.value = true;
  apiKeyMessage.value = '';
  
  try {
    const response = await fetch('/api/api-keys/validate', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    const data = await response.json();

    apiKeyMessage.value = data.message;
    apiKeyMessageType.value = data.success ? 'success' : 'error';
  } catch (error) {
    apiKeyMessage.value = 'Failed to validate API keys';
    apiKeyMessageType.value = 'error';
  } finally {
    apiKeyLoading.value = false;
  }
};

const deleteApiKeys = async () => {
  if (!confirm('Are you sure you want to delete all API keys?')) return;
  
  apiKeyLoading.value = true;
  apiKeyMessage.value = '';
  
  try {
    const response = await fetch('/api/api-keys', {
      method: 'DELETE',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({
        services: ['openai', 'elevenlabs']
      })
    });

    const data = await response.json();

    if (data.success) {
      apiKeyMessage.value = data.message;
      apiKeyMessageType.value = 'success';
      await fetchUser(); // Refresh user data
    } else {
      apiKeyMessage.value = data.message;
      apiKeyMessageType.value = 'error';
    }
  } catch (error) {
    apiKeyMessage.value = 'Failed to delete API keys';
    apiKeyMessageType.value = 'error';
  } finally {
    apiKeyLoading.value = false;
  }
};

const saveIndividualKey = async (service) => {
  apiKeyLoading.value = true;
  
  // Clear individual status messages
  if (service === 'openai') {
    openaiStatus.value = null;
  } else {
    elevenLabsStatus.value = null;
  }
  
  try {
    const keyField = service === 'openai' ? 'openai_key' : 'elevenlabs_key';
    const response = await fetch(`/api/api-keys/${service}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: JSON.stringify({ [keyField]: apiKeyForm.value[keyField] })
    });

    const data = await response.json();

    if (data.success) {
      if (service === 'openai') {
        openaiStatus.value = { type: 'success', message: data.message };
        apiKeyForm.value.openai_key = ''; // Clear the input after successful save
      } else {
        elevenLabsStatus.value = { type: 'success', message: data.message };
        apiKeyForm.value.elevenlabs_key = ''; // Clear the input after successful save
      }
      await fetchUser(); // Refresh user data
    } else {
      if (service === 'openai') {
        openaiStatus.value = { type: 'error', message: data.message };
      } else {
        elevenLabsStatus.value = { type: 'error', message: data.message };
      }
    }
  } catch (error) {
    const errorMessage = `Failed to save ${service === 'openai' ? 'OpenAI' : 'ElevenLabs'} API key`;
    if (service === 'openai') {
      openaiStatus.value = { type: 'error', message: errorMessage };
    } else {
      elevenLabsStatus.value = { type: 'error', message: errorMessage };
    }
  } finally {
    apiKeyLoading.value = false;
  }
};

const testIndividualKey = async (service) => {
  apiKeyLoading.value = true;
  
  // Clear individual status messages
  if (service === 'openai') {
    openaiStatus.value = null;
  } else {
    elevenLabsStatus.value = null;
  }
  
  try {
    const response = await fetch(`/api/api-keys/${service}/test`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      }
    });

    const data = await response.json();

    if (service === 'openai') {
      openaiStatus.value = { type: data.success ? 'success' : 'error', message: data.message };
    } else {
      elevenLabsStatus.value = { type: data.success ? 'success' : 'error', message: data.message };
    }
  } catch (error) {
    const errorMessage = `Failed to test ${service === 'openai' ? 'OpenAI' : 'ElevenLabs'} API key`;
    if (service === 'openai') {
      openaiStatus.value = { type: 'error', message: errorMessage };
    } else {
      elevenLabsStatus.value = { type: 'error', message: errorMessage };
    }
  } finally {
    apiKeyLoading.value = false;
  }
};

// Advanced Settings Functions
const saveAdvancedSettings = () => {
  localStorage.setItem('webcam-assistant-advanced', JSON.stringify(advancedSettings.value));
  showStatus('Advanced settings saved', 'success');
};

const loadAdvancedSettings = () => {
  try {
    const stored = localStorage.getItem('webcam-assistant-advanced');
    if (stored) {
      const settings = JSON.parse(stored);
      advancedSettings.value = { ...advancedSettings.value, ...settings };
    }
  } catch (error) {
    console.warn('Failed to load advanced settings:', error);
  }
};

const resetAdvancedSettings = () => {
  advancedSettings.value = {
    gptModel: 'gpt-4o-mini',
    promptMessage: 'Please describe this image in detail. Focus on what you see, including objects, people, activities, colors, and the overall scene. Make it engaging and descriptive as if you\'re helping someone who cannot see the image.',
    voiceId: 'pNInz6obpgDQGcFmaJgB',
    customVoiceId: ''
  };
  localStorage.removeItem('webcam-assistant-advanced');
  showStatus('Settings reset to defaults', 'info');
};

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
  if (!isVideoActive.value || !hasValidApiKeys.value) return;
  
  try {
    const video = videoElement.value;
    const canvas = canvasElement.value;
    
    // Set canvas dimensions to match video
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Draw video frame to canvas
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);
    
    // Convert to blob
    canvas.toBlob(async (blob) => {
      // Store photo for gallery
      const dataUrl = canvas.toDataURL('image/jpeg', 0.9);
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
      await processPhotoBlob(blob, photo);
    }, 'image/jpeg', 0.9);
    
  } catch (error) {
    console.error('Error capturing photo:', error);
    showStatus('Failed to capture photo', 'error');
  }
};

const processPhoto = async (photo) => {
  // Convert data URL back to blob for processing
  const response = await fetch(photo.dataUrl);
  const blob = await response.blob();
  await processPhotoBlob(blob, photo);
};

const processPhotoBlob = async (blob, photo) => {
  if (!hasValidApiKeys.value) {
    showStatus('Please configure your API keys first', 'error');
    return;
  }

  try {
    isProcessing.value = true;
    processingStatus.value = 'Analyzing image with backend proxy...';
    
    // Create form data for the backend
    const formData = new FormData();
    formData.append('image', blob, 'captured_photo.jpg');
    
    // Add advanced settings
    const effectiveVoiceId = advancedSettings.value.customVoiceId || advancedSettings.value.voiceId;
    formData.append('gpt_model', advancedSettings.value.gptModel);
    formData.append('prompt_message', advancedSettings.value.promptMessage);
    formData.append('voice_id', effectiveVoiceId);
    
    const response = await fetch('/api/process-image-proxy', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
      },
      body: formData
    });

    const data = await response.json();
    
    if (data.success) {
      audioUrl.value = data.audio_url;
      apiError.value = null; // Clear any previous API errors
      showStatus('Image analyzed and audio generated successfully via secure backend proxy!', 'success');
      photo.processed = true;
    } else {
      // Check if this is a structured API error
      if (data.api_error) {
        apiError.value = data.api_error;
        showStatus(data.error || 'API Error occurred', 'error');
      } else {
        apiError.value = null;
        throw new Error(data.error || 'Processing failed');
      }
    }
    
  } catch (error) {
    console.error('Error processing photo:', error);
    apiError.value = null; // Clear any API errors for generic errors
    showStatus('Failed to process photo: ' + error.message, 'error');
  } finally {
    isProcessing.value = false;
    processingStatus.value = '';
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

const getErrorSuggestion = (error) => {
  if (!error) return null;
  
  const { service, type } = error;
  
  if (service === 'openai') {
    switch (type) {
      case 'insufficient_quota':
        return 'Check your OpenAI billing and add credits to your account, or wait for your quota to reset.';
      case 'invalid_request_error':
        return 'There may be an issue with the image format or request. Try capturing a new photo.';
      case 'rate_limit_exceeded':
        return 'You are sending requests too quickly. Please wait a moment and try again.';
      default:
        return 'Check your OpenAI API key configuration and account status.';
    }
  } else if (service === 'elevenlabs') {
    switch (type) {
      case 'quota_exceeded':
        return 'Your ElevenLabs character limit has been reached. Upgrade your plan or wait for your quota to reset.';
      case 'ivc_not_permitted':
        return 'Instant voice cloning requires a paid ElevenLabs subscription. Please upgrade your account.';
      case 'unauthorized':
        return 'Your ElevenLabs API key may be invalid or expired. Please check and update it.';
      default:
        return 'Check your ElevenLabs API key configuration and account status.';
    }
  }
  
  return null;
};

// Lifecycle
onMounted(async () => {
  // Check if getUserMedia is supported
  if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
    showStatus('Webcam access is not supported in this browser', 'error');
  }
  
  // Check authentication status
  await checkAuthStatus();
  
  // Load advanced settings
  loadAdvancedSettings();
});

onUnmounted(() => {
  stopCamera();
  
  // Clean up audio URLs to prevent memory leaks
  if (audioUrl.value && audioUrl.value.startsWith('blob:')) {
    URL.revokeObjectURL(audioUrl.value);
  }
});
</script> 