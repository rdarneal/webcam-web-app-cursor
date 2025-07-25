# Smart Webcam Assistant

A modern Laravel application with Vue.js frontend that captures webcam photos, analyzes them with AI, and provides audio descriptions using text-to-speech technology. Features enterprise-grade backend proxy architecture with user authentication and secure API key management.

## Features

### 🔐 Secure Backend Proxy Architecture
- **User authentication** with individual account management
- **Encrypted API key storage** per user in secure database
- **Backend proxy** for all external API calls (zero frontend exposure)
- **Session-based authentication** with CSRF protection
- **Multi-user support** with complete data isolation

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
- **Laravel 11** - PHP framework with authentication
- **PHP 8.4** - Server-side language
- **SQLite** - Database with user accounts and encrypted key storage
- **Guzzle HTTP** - API client for external services
- **Laravel Encryption** - Secure API key storage

### Frontend
- **Vue.js 3** - JavaScript framework
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Build tool and dev server
- **MediaDevices API** - Webcam access

### AI Services
- **OpenAI Vision API** - Image analysis and description
- **Eleven Labs API** - Text-to-speech conversion

### Security & Authentication
- **Laravel Authentication** - User registration and login
- **Session Management** - Secure session handling
- **Database Encryption** - API key encryption at rest
- **CSRF Protection** - Cross-site request forgery prevention

## Installation

### 1. Laravel Backend Setup
```bash
# Generate application key
php artisan key:generate

# Run database migrations (creates users, user_api_keys, sessions tables)
php artisan migrate

# Create storage symlink
php artisan storage:link
```

### 2. Optional: Create Test User
```bash
# Creates test user: test@example.com / password: 'password'
php artisan db:seed
```

### 3. Frontend Dependencies
```bash
# Install and build frontend assets
npm install
npm run build
```

### 4. Start Development Server
```bash
php artisan serve
```

### 5. Access Application
Open `http://127.0.0.1:8000` in your browser

## Usage

### Initial Setup
1. **Register an Account** or **Login**
   - Visit `http://127.0.0.1:8000`
   - Click "Register" to create a new account
   - Or use test account: `test@example.com` / `password` (if seeded)

2. **Configure API Keys**
   - After login, click "Manage API Keys"
   - Enter your OpenAI API key
   - Enter your Eleven Labs API key
   - Keys are encrypted and stored securely in your account

### Using the Webcam Assistant
1. **Grant camera permissions** when prompted by your browser
2. **Click "Start Camera"** to activate the webcam feed
3. **Position yourself or objects** in the camera view
4. **Click "Capture Photo"** to take a picture
5. **Wait for AI analysis** - the image will be processed via backend proxy
6. **Listen to the audio description** that plays automatically
7. **View captured photos** in the gallery below

## API Configuration

The application uses a **secure backend proxy architecture** where API keys are managed per-user through the web interface.

### Getting Your API Keys

#### OpenAI (Image Analysis)
1. Sign up at [OpenAI Platform](https://platform.openai.com/)
2. Create an API key
3. **Set usage limits** in your OpenAI account for safety

#### Eleven Labs (Text-to-Speech)
1. Sign up at [Eleven Labs](https://elevenlabs.io/)
2. Get your API key from the dashboard
3. **Monitor usage** in your ElevenLabs dashboard

### Secure Key Management
- **Web Interface**: Enter keys through the application's secure web interface
- **Encryption**: Keys are encrypted using Laravel's encryption before database storage
- **User Isolation**: Each user's keys are completely separate and secure
- **No Environment Variables**: API keys are NOT stored in `.env` files
- **Backend Proxy**: All API calls are made from the backend using your encrypted keys

## Project Structure

```
├── app/Http/Controllers/
│   ├── Auth/
│   │   ├── AuthController.php          # User authentication
│   │   └── ApiKeyController.php        # API key management
│   └── ImageProcessingController.php   # Main processing logic
├── app/Models/
│   ├── User.php                        # User model with API key methods
│   └── UserApiKey.php                  # Encrypted API key storage
├── database/migrations/
│   ├── create_users_table.php          # User accounts
│   └── create_user_api_keys_table.php  # Encrypted API key storage
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
│   └── api.php                         # Authentication & proxy API routes
├── storage/app/public/
│   ├── audio/                          # Generated audio files
│   └── temp/                           # Temporary image storage
└── public/storage/                     # Symlinked storage
```

## Features in Detail

### Authentication & Security
- **User Registration**: Secure account creation with validation
- **Session Management**: Laravel's built-in session authentication
- **API Key Encryption**: Database-level encryption for all API keys
- **Access Control**: All endpoints protected by authentication middleware

### Camera Controls
- **Start/Stop Camera**: Toggle webcam access with permission handling
- **Privacy Indicators**: Clear visual feedback about camera status
- **Error Handling**: Graceful fallbacks for permission denied or hardware issues

### Image Processing Pipeline (Backend Proxy)
1. **Authentication**: User must be logged in
2. **Capture**: High-resolution photo capture from video stream
3. **Upload**: Secure file upload to authenticated Laravel backend
4. **Key Retrieval**: Backend retrieves user's encrypted API keys
5. **Analysis**: Backend makes OpenAI API call using user's decrypted key
6. **Speech**: Backend makes Eleven Labs API call using user's decrypted key
7. **Playback**: Audio returned to frontend for playback
8. **Cleanup**: Temporary files cleaned up automatically

### User Experience
- **Account Dashboard**: Manage API keys and view usage
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

### Backend Proxy Architecture
- **Zero Frontend API Exposure**: API keys never exist in browser or frontend code
- **Encrypted Storage**: All API keys encrypted using Laravel's encryption
- **User Isolation**: Complete separation of user data and API keys
- **Session Authentication**: Secure Laravel session management
- **Backend API Calls**: All external API calls made from secure backend

### Traditional Security
- **CSRF Protection**: Laravel's built-in CSRF tokens
- **File Validation**: Image type and size validation
- **Temporary Storage**: Automatic cleanup of uploaded images
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Input validation and output escaping

### Operational Security
- **Audit Trails**: API key usage tracking with timestamps
- **Account Management**: Users can update or delete their keys anytime
- **Secure Logout**: Session invalidation on logout
- **Error Handling**: No sensitive data exposed in error messages

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

### Database Management
```bash
# Reset database and reseed
php artisan migrate:fresh --seed

# Create additional test users
php artisan tinker
>>> User::factory()->create(['email' => 'your@email.com'])
```

## Migration from Previous Version

If upgrading from a version that used environment variables for API keys:

1. **Remove old environment variables** from `.env`:
   ```bash
   # Remove these lines:
   # OPENAI_API_KEY=...
   # ELEVEN_LABS_API_KEY=...
   # ELEVEN_LABS_VOICE_ID=...
   ```

2. **Run migrations** to create user authentication tables:
   ```bash
   php artisan migrate
   ```

3. **Register a user account** and configure API keys through the web interface

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For detailed API setup instructions and security information, see:
- **API_SETUP.md** - Detailed API key configuration guide
- **SECURITY.md** - Complete security architecture documentation

For questions, issues, or contributions, please refer to these documentation files.
