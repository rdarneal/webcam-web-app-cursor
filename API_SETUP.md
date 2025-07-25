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
Dashboard → Manage API Keys → Enter keys → Save
```
- Keys are validated before saving
- Previous keys are securely overwritten
- Changes take effect immediately

### Testing Keys
```
API Keys Panel → Test Keys button
```
- Validates connectivity to OpenAI and ElevenLabs
- Tests actual API endpoints
- Shows detailed error messages if issues exist

### Monitoring Usage
- **Last used timestamps** - See when keys were last accessed
- **API dashboard links** - Direct links to OpenAI/ElevenLabs usage
- **Status indicators** - Visual confirmation of key configuration

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
- **Voice Selection** - Choose from 9 different ElevenLabs voices
- **Custom Voice IDs** - Use your own trained voices

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
POST   /api/api-keys         - Store/update encrypted API keys
GET    /api/api-keys/status  - Get key configuration status
POST   /api/api-keys/validate - Validate stored keys
DELETE /api/api-keys         - Delete stored keys
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
- **Solution**: Configure both OpenAI and ElevenLabs keys
- **Check**: API Keys panel shows both services as "Configured"

#### "Authentication Required" Error
- **Solution**: Login to your account
- **Check**: Dashboard shows your name and email

#### "API Key Invalid" Error
- **Solution**: Verify keys in your API provider dashboards
- **Check**: Use "Test Keys" button to validate connectivity

#### "Processing Failed" Error
- **Solution**: Check API key limits and billing status
- **Check**: Review error messages for specific issues

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