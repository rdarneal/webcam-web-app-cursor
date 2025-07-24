# Smart Webcam Assistant

A modern Laravel application with Vue.js frontend that captures webcam photos, analyzes them with AI, and provides audio descriptions using text-to-speech technology.

## Features

### 🎥 Webcam Integration
- **Real-time video feed** with MediaDevices API
- **Privacy-focused** camera controls (start/stop)
- **Browser permission handling** for camera access
- **Photo capture** functionality with high-quality output

### 🤖 AI-Powered Analysis
- **Image-to-text** conversion using OpenAI Vision API (GPT-4 Vision)
- **Detailed descriptions** of captured images
- **Contextual analysis** including objects, people, activities, and colors

### 🔊 Text-to-Speech
- **High-quality audio** generation using Eleven Labs API
- **Natural voice synthesis** with customizable voices
- **Real-time playback** of generated audio descriptions
- **Multiple voice options** available

### 💫 Modern UI/UX
- **Responsive design** built with Tailwind CSS
- **Smooth animations** and transitions
- **Intuitive controls** with visual feedback
- **Photo gallery** showing recent captures
- **Status indicators** for processing steps

## Technology Stack

### Backend
- **Laravel 11** - PHP framework
- **PHP 8.4** - Server-side language
- **SQLite** - Database (default)
- **Guzzle HTTP** - API client for external services

### Frontend
- **Vue.js 3** - JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Build tool and dev server
- **MediaDevices API** - Webcam access

### AI Services
- **OpenAI Vision API** - Image analysis and description
- **Eleven Labs API** - Text-to-speech conversion

## Installation

1. **Clone and setup Laravel**:
   ```bash
   # Dependencies are already installed via Composer
   php artisan key:generate
   php artisan migrate
   php artisan storage:link
   ```

2. **Install Node.js dependencies**:
   ```bash
   npm install
   npm run build
   ```

3. **Configure API keys** (see API_SETUP.md):
   ```env
   OPENAI_API_KEY=your_openai_api_key_here
   ELEVEN_LABS_API_KEY=your_eleven_labs_api_key_here
   ELEVEN_LABS_VOICE_ID=pNInz6obpgDQGcFmaJgB
   ```

4. **Start the development server**:
   ```bash
   php artisan serve
   ```

5. **Visit the application**:
   Open `http://127.0.0.1:8000` in your browser

## Usage

1. **Grant camera permissions** when prompted by your browser
2. **Click "Start Camera"** to activate the webcam feed
3. **Position yourself or objects** in the camera view
4. **Click "Capture Photo"** to take a picture
5. **Wait for AI analysis** - the image will be processed automatically
6. **Listen to the audio description** that plays automatically
7. **View captured photos** in the gallery below

## API Configuration

The application requires API keys from two services:

### OpenAI (Image Analysis)
- Sign up at [OpenAI Platform](https://platform.openai.com/)
- Create an API key
- Add to `.env`: `OPENAI_API_KEY=your_key_here`

### Eleven Labs (Text-to-Speech)
- Sign up at [Eleven Labs](https://elevenlabs.io/)
- Get your API key
- Add to `.env`: `ELEVEN_LABS_API_KEY=your_key_here`

## Project Structure

```
├── app/Http/Controllers/
│   └── ImageProcessingController.php    # Main processing logic
├── resources/
│   ├── js/
│   │   ├── app.js                      # Vue app entry point
│   │   └── components/
│   │       └── WebcamApp.vue           # Main Vue component
│   ├── css/
│   │   └── app.css                     # Tailwind CSS styles
│   └── views/
│       └── welcome.blade.php           # Main HTML template
├── routes/
│   └── api.php                         # API routes
├── storage/app/public/
│   ├── audio/                          # Generated audio files
│   └── temp/                           # Temporary image storage
└── public/storage/                     # Symlinked storage
```

## Features in Detail

### Camera Controls
- **Start/Stop Camera**: Toggle webcam access with permission handling
- **Privacy Indicators**: Clear visual feedback about camera status
- **Error Handling**: Graceful fallbacks for permission denied or hardware issues

### Image Processing Pipeline
1. **Capture**: High-resolution photo capture from video stream
2. **Upload**: Secure file upload to Laravel backend
3. **Analysis**: OpenAI Vision API analyzes image content
4. **Speech**: Eleven Labs converts description to natural audio
5. **Playback**: Automatic audio playback in the browser

### User Experience
- **Loading States**: Visual indicators during processing
- **Error Messages**: Clear feedback for any issues
- **Photo History**: Gallery of recent captures with timestamps
- **Responsive Design**: Works on desktop and mobile devices

## Browser Compatibility

- **Chrome/Chromium** ✅ (Recommended)
- **Firefox** ✅
- **Safari** ✅
- **Edge** ✅

**Note**: Requires HTTPS for webcam access in production environments.

## Security Features

- **CSRF Protection**: Laravel's built-in CSRF tokens
- **File Validation**: Image type and size validation
- **Temporary Storage**: Automatic cleanup of uploaded images
- **API Rate Limiting**: Configurable limits for external API calls

## Development

### Running in Development
```bash
# Frontend development with hot reload
npm run dev

# Backend development
php artisan serve
```

### Building for Production
```bash
npm run build
php artisan optimize
```

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For questions, issues, or contributions, please refer to the API_SETUP.md file for configuration details.
