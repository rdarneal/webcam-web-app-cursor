# Smart Webcam Assistant - API Setup (Backend Proxy Architecture)

This application uses an **industry-standard backend proxy architecture** for maximum security and API key management.

## 🔒 Secure Backend Proxy Architecture

**Maximum Security & Control** - Your API keys are encrypted and stored securely on our backend.

### Benefits
- **🛡️ Enterprise security** - API keys encrypted and stored in secure database
- **💰 Individual cost control** - Each user manages their own API keys and billing
- **📊 Usage transparency** - Monitor usage through your API dashboards
- **🚀 Optimized performance** - Backend caching and request optimization
- **🔐 Zero client risk** - API keys never exist in browser or frontend code
- **👥 Multi-user support** - Individual accounts with isolated API key storage

### How It Works
1. **Create an account** - Register for secure access to the application
2. **Configure your keys** - Enter API keys once, stored encrypted in database
3. **Backend proxy** - All API calls made securely from our backend
4. **Session management** - Secure authentication with Laravel sessions

---

## 🚀 Getting Started

### Step 1: Create Your Account
Visit the application and register for a new account:
- **Name** - Your display name
- **Email** - Used for login (must be unique)
- **Password** - Minimum 8 characters

### Step 2: Get Your API Keys

#### OpenAI API Key (Required)
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign in or create an account
3. Click "Create new secret key"
4. Copy the key (starts with `sk-`)
5. **Set usage limits** in your account for safety

#### ElevenLabs API Key (Required)
1. Visit [ElevenLabs API Keys](https://elevenlabs.io/app/api-keys)
2. Sign in or create an account
3. Copy your API key
4. **Monitor usage** in your dashboard

### Step 3: Configure API Keys in Application
1. **Login** to your account
2. **Click "Manage API Keys"** in the dashboard
3. **Enter your keys** in the secure form
4. **Save keys** - they are immediately encrypted and stored
5. **Test connectivity** with the "Test Keys" button

### Step 4: Start Using the Assistant
- **Grant camera permissions** when prompted
- **Click "Start Camera"** to activate webcam
- **Capture photos** and get AI-powered descriptions
- **Listen to audio** generated from image analysis

---

## 🔐 Security Features

### Backend Proxy Protection
- **Encrypted storage** - All API keys encrypted using Laravel's encryption
- **User isolation** - Each user's keys completely separate
- **Session-based auth** - Secure login with CSRF protection
- **Server-side calls** - API keys never leave the secure backend
- **Audit trails** - Track API key usage and last access times

### Data Privacy
- **No frontend exposure** - API keys never sent to or stored in browser
- **Secure transmission** - All data encrypted in transit with HTTPS
- **User control** - Users can update or delete their keys anytime
- **No logging** - API keys not logged in application logs

### Access Controls
- **Authentication required** - Must be logged in to use API features
- **Individual accounts** - Each user manages only their own keys
- **Session management** - Automatic logout and session expiration
- **CSRF protection** - Prevents cross-site request forgery attacks

---

## 📊 API Key Management

### Adding/Updating Keys
```
Dashboard → Manage API Keys → Enter keys → Save All
OR
Dashboard → Manage API Keys → Enter individual key → Save (for individual keys)
```
- Keys are validated before saving
- Previous keys are securely overwritten
- Changes take effect immediately
- Both bulk and individual key management supported

### Testing Keys
```
API Keys Panel → Test All Keys button (bulk test)
OR
API Keys Panel → Test button (individual key test)
```
- Validates connectivity to OpenAI and ElevenLabs
- Tests actual API endpoints
- Shows detailed error messages if issues exist
- Supports testing individual keys or all keys at once

### Monitoring Usage
- **Status indicators** - Visual confirmation of key configuration
- **Real-time validation** - Individual key testing with immediate feedback
- **Error suggestions** - Context-aware error resolution guidance
- **API provider dashboards** - Monitor usage directly in OpenAI/ElevenLabs accounts

### Removing Keys
```
API Keys Panel → Clear Keys button
```
- Permanently deletes encrypted keys from database
- Requires confirmation
- Takes effect immediately

---

## ⚙️ Advanced Configuration

### Custom AI Settings
- **GPT Model Selection** - Choose from GPT-4o Mini, GPT-4o, GPT-4 Turbo
- **Custom Prompts** - Personalize how images are described
- **Voice Selection** - Choose from 9 different preset ElevenLabs voices or use custom voice IDs
- **Custom Voice IDs** - Use your own trained voices or any valid ElevenLabs voice ID

### Performance Options
- **Model Selection** - Balance speed vs quality based on your needs
- **Voice Customization** - Select voices that match your preference
- **Prompt Engineering** - Customize descriptions for specific use cases

---

## 🔧 API Endpoints

### Authentication Endpoints
```
POST /api/auth/register     - Create new user account
POST /api/auth/login        - Login with email/password
POST /api/auth/logout       - Secure logout
GET  /api/auth/user         - Get current user info
GET  /api/auth/check        - Check authentication status
```

### API Key Management (Protected)
```
POST   /api/api-keys         - Store/update encrypted API keys (bulk)
GET    /api/api-keys/status  - Get key configuration status
POST   /api/api-keys/validate - Validate stored keys (bulk test)
DELETE /api/api-keys         - Delete stored keys

# Individual API Key Management
POST   /api/api-keys/openai     - Store/update individual OpenAI key
POST   /api/api-keys/elevenlabs - Store/update individual ElevenLabs key
POST   /api/api-keys/openai/test     - Test individual OpenAI key
POST   /api/api-keys/elevenlabs/test - Test individual ElevenLabs key
```

### Image Processing (Protected)
```
POST /api/process-image-proxy - Process image with user's encrypted keys
```

---

## 💰 Cost Considerations

### With User API Keys
- **Direct billing** to your OpenAI/ElevenLabs accounts
- **Full cost control** and transparency
- **Usage monitoring** in your API dashboards
- **Set your own limits** and budgets
- **No markup** - pay API providers directly

### Usage Estimation
- **Image analysis** - ~$0.01-0.03 per image (depending on model)
- **Speech generation** - ~$0.10-0.30 per audio minute
- **Total cost** - Typically $0.11-0.33 per processed image with audio

---

## 🛠️ Troubleshooting

### Common Issues

#### "Missing API Keys" Error
- **Solution**: Configure both OpenAI and ElevenLabs keys using the secure form
- **Check**: API Keys panel shows both services as "Configured"
- **Individual setup**: Use individual key save/test buttons for granular control

#### "Authentication Required" Error
- **Solution**: Login to your account using the authentication form
- **Check**: Dashboard shows your name and "Manage API Keys" button is visible

#### "API Key Invalid" Error
- **Solution**: Use individual key testing to identify which key is invalid
- **Check**: Test each key individually using the "Test" buttons
- **Fix**: Update the problematic key through the individual key management

#### "Processing Failed" Error
- **Solution**: Check the detailed error message for specific guidance
- **Check**: Look for service-specific error details (OpenAI vs ElevenLabs)
- **Context**: Use provided error suggestions for resolution steps

#### Quota/Billing Issues
- **OpenAI quota exceeded**: Add credits or wait for quota reset
- **ElevenLabs character limit**: Upgrade plan or wait for reset
- **Rate limiting**: Wait before making additional requests
- **Instant voice cloning**: Requires paid ElevenLabs subscription

### Performance Issues
- **Slow processing** - Try GPT-4o Mini model for faster responses
- **Audio generation fails** - Verify ElevenLabs account has sufficient credits
- **Connection timeouts** - Check internet connection and API service status

---

## 🔄 Migration from Direct API Approach

If migrating from client-side API key approach:

### Benefits of Backend Proxy
- **Enhanced security** - No risk of API key exposure
- **Better performance** - Server-side optimizations
- **User management** - Individual accounts and isolation
- **Audit capabilities** - Usage tracking and monitoring

### Migration Steps
1. **Create account** in the new system
2. **Configure API keys** in secure backend storage
3. **Verify functionality** with test image capture
4. **Remove old configuration** from browser storage

---

## 📋 Security Best Practices

### For Users
1. **Use strong passwords** for account registration
2. **Monitor API usage** in OpenAI/ElevenLabs dashboards
3. **Set usage limits** in your API accounts
4. **Keep API keys private** - never share with others
5. **Log out** when using shared computers

### For Organizations
1. **Individual accounts** - Each user should have their own account
2. **Key rotation** - Periodically update API keys
3. **Usage monitoring** - Review API costs and usage patterns
4. **Access control** - Ensure only authorized users have accounts

---

## 🔮 Future Enhancements

Planned security and functionality improvements:
- **Team management** - Organization accounts with user roles
- **API key rotation** - Automated key rotation capabilities
- **Usage analytics** - Detailed usage reporting and insights
- **Webhook support** - Real-time notifications and integrations
- **Bulk processing** - Batch image processing capabilities

---

## 📞 Support

For technical issues or security questions:
- **Check troubleshooting** section above
- **Review error messages** for specific guidance
- **Verify API account status** in provider dashboards
- **Test connectivity** using built-in validation tools

This backend proxy architecture provides enterprise-grade security while maintaining ease of use and cost transparency. Your API keys are protected with industry-standard encryption, and all processing happens securely on our backend infrastructure. 

---

## 🛡️ Enhanced Error Handling

### Intelligent Error Detection
- **Service-specific errors** - Differentiated OpenAI vs ElevenLabs error handling
- **Error type classification** - Quota exceeded, rate limits, invalid keys, etc.
- **Contextual suggestions** - Specific guidance for each error type

### Real-time Validation
- **Individual key testing** - Test OpenAI and ElevenLabs keys separately
- **Bulk validation** - Test all configured keys simultaneously  
- **Immediate feedback** - Visual status indicators for each service
- **Detailed error messages** - Clear explanations of API issues

### Error Resolution Guidance
- **Quota exceeded** - Billing and upgrade guidance
- **Rate limiting** - Wait time recommendations
- **Invalid keys** - Key configuration help
- **Service outages** - Alternative approaches

--- 

---

## 🔑 Google OAuth Setup (Optional)

The application supports Google OAuth authentication as an alternative to traditional email/password registration. This allows users to sign in with their Google accounts for a streamlined experience.

### Benefits of Google OAuth
- **Simplified registration** - No need to create separate passwords
- **Enhanced security** - Leverages Google's robust authentication system
- **User convenience** - Single sign-on experience
- **Profile integration** - Automatically imports name and avatar from Google

### Step 1: Create Google Cloud Project
1. **Visit Google Cloud Console**
   - Go to [Google Cloud Console](https://console.cloud.google.com/)
   - Sign in with your Google account

2. **Create a New Project**
   - Click "Select a project" dropdown at the top
   - Click "New Project"
   - Enter project name (e.g., "Webcam Assistant")
   - Click "Create"

3. **Enable Google+ API**
   - In the left sidebar, go to "APIs & Services" → "Library"
   - Search for "Google+ API" or "Google Identity"
   - Click on "Google+ API" and click "Enable"

### Step 2: Configure OAuth Consent Screen
1. **Go to OAuth Consent Screen**
   - In the left sidebar, go to "APIs & Services" → "OAuth consent screen"

2. **Choose User Type**
   - Select "External" for public applications
   - Click "Create"

3. **Fill Required Information**
   - **App name**: Your application name (e.g., "Smart Webcam Assistant")
   - **User support email**: Your email address
   - **App logo**: Optional - upload your app logo
   - **App domain**: Your domain (for local development, you can skip this)
   - **Authorized domains**: Add your domain (for local development, you can skip this)
   - **Developer contact information**: Your email address
   - Click "Save and Continue"

4. **Configure Scopes** (optional for basic setup)
   - Click "Save and Continue" to skip for now

5. **Add Test Users** (for development)
   - Add email addresses that will be allowed to test the OAuth flow
   - Click "Save and Continue"

### Step 3: Create OAuth 2.0 Credentials
1. **Go to Credentials**
   - In the left sidebar, go to "APIs & Services" → "Credentials"

2. **Create Credentials**
   - Click "+ Create Credentials" at the top
   - Select "OAuth 2.0 Client IDs"

3. **Configure Application Type**
   - **Application type**: Web application
   - **Name**: Give it a descriptive name (e.g., "Webcam App OAuth")

4. **Set Authorized Redirect URIs**
   - Click "Add URI" under "Authorized redirect URIs"
   - Add: `http://127.0.0.1:8000/auth/google/callback`
   - For production: `https://yourdomain.com/auth/google/callback`
   - Click "Create"

5. **Save Your Credentials**
   - Copy the **Client ID** and **Client Secret**
   - Store them securely - you'll need these for your `.env` file

### Step 4: Configure Environment Variables
Add the following variables to your `.env` file:

```bash
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here
GOOGLE_REDIRECT_URL=http://127.0.0.1:8000/auth/google/callback
```

**Important Notes:**
- Replace `your_google_client_id_here` with your actual Client ID
- Replace `your_google_client_secret_here` with your actual Client Secret
- For production, update `GOOGLE_REDIRECT_URL` to use your domain and HTTPS

### Step 5: Install Dependencies (if needed)
The application requires Laravel Socialite for OAuth functionality:

```bash
composer require laravel/socialite
```

### Step 6: Test Google OAuth
1. **Start the development server**:
   ```bash
   php artisan serve
   ```

2. **Visit the application**:
   - Go to `http://127.0.0.1:8000`
   - Click "Login with Google"
   - Complete the Google OAuth flow
   - Verify successful login and account creation

### Production Configuration

#### Domain Setup
For production deployment:

1. **Update OAuth consent screen**
   - Add your production domain to authorized domains
   - Update app domain information

2. **Update redirect URIs**
   - Add production callback URL: `https://yourdomain.com/auth/google/callback`
   - Remove development URLs for security

3. **Environment variables**
   ```bash
   GOOGLE_CLIENT_ID=your_google_client_id_here
   GOOGLE_CLIENT_SECRET=your_google_client_secret_here
   GOOGLE_REDIRECT_URL=https://yourdomain.com/auth/google/callback
   ```

#### Security Considerations
- **Use HTTPS** in production for OAuth callbacks
- **Restrict domains** in Google Cloud Console to your actual domains
- **Monitor OAuth usage** in Google Cloud Console
- **Rotate secrets** periodically for enhanced security

### Troubleshooting

#### Common Issues

**"OAuth Error: redirect_uri_mismatch"**
- **Solution**: Ensure the redirect URI in Google Cloud Console exactly matches your `GOOGLE_REDIRECT_URL`
- **Check**: URLs must match exactly (including http/https, port numbers, and paths)

**"This app isn't verified"**
- **Solution**: This is normal for development. Click "Advanced" → "Go to App Name (unsafe)"
- **Production**: Submit your app for Google verification if needed

**"Access blocked: This app's request is invalid"**
- **Solution**: Check that Google+ API is enabled in your Google Cloud project
- **Check**: Verify OAuth consent screen is properly configured

**User data not saving properly**
- **Solution**: Check database migrations are run: `php artisan migrate`
- **Check**: Verify User model has google_id and avatar fields

#### Development vs Production

**Development**
- Can use localhost URLs
- "Unverified app" warnings are normal
- Test with limited user accounts

**Production**
- Must use HTTPS
- Should complete app verification for public use
- Can support unlimited users

### Integration Details

The Google OAuth implementation includes:

#### Database Schema
```sql
-- User table includes Google OAuth fields
google_id VARCHAR(255) NULLABLE
avatar VARCHAR(255) NULLABLE
```

#### OAuth Flow
1. **User clicks "Login with Google"**
2. **Redirect to Google** (`/api/auth/google/redirect`)
3. **Google authentication** (handled by Google)
4. **Callback processing** (`/auth/google/callback`)
5. **User creation/login** (automatic account linking)
6. **Session establishment** (standard Laravel auth)

#### User Experience
- **New users**: Account created automatically with Google profile information
- **Existing users**: Linked to existing account by email address
- **Profile data**: Name and avatar imported from Google
- **API keys**: Must still be configured after OAuth login

This Google OAuth integration provides a seamless authentication experience while maintaining the secure backend proxy architecture for API key management. 