# Smart Webcam Assistant - API Setup

This application now supports **two secure ways** to configure API access for OpenAI and Eleven Labs.

## 🔒 Option 1: User-Provided Keys (Recommended)

**Maximum Security & Control** - Your API keys never leave your browser.

### Benefits
- **🛡️ Complete security** - Keys stored only in your browser
- **💰 Cost control** - Direct billing to your API accounts  
- **📊 Usage transparency** - Monitor usage in your dashboards
- **🚀 Direct performance** - No server bottlenecks
- **🔐 Zero server risk** - Keys can't be intercepted by our servers

### How It Works
1. **Enter your keys** in the secure interface at the top of the app
2. **Test connectivity** with the "Test Keys" button
3. **Optional persistence** - choose to remember keys locally
4. **Direct API calls** - Your browser communicates directly with OpenAI/ElevenLabs

### Getting Your API Keys

#### OpenAI API Key
1. Visit [OpenAI Platform](https://platform.openai.com/api-keys)
2. Sign in or create an account
3. Click "Create new secret key"
4. Copy the key (starts with `sk-`)
5. **Set usage limits** in your account for safety

#### ElevenLabs API Key  
1. Visit [ElevenLabs API Keys](https://elevenlabs.io/app/api-keys)
2. Sign in or create an account
3. Copy your API key
4. **Monitor usage** in your dashboard

### Security Features
- 🔒 **Browser-only storage** - Keys never sent to our servers
- 👁️ **Show/hide toggles** - Secure key entry interface
- 🧹 **Instant clearing** - Remove keys with one click
- ✅ **Key validation** - Test before use
- 🔄 **Memory cleanup** - Automatic cleanup of sensitive data

---

## 🖥️ Option 2: Server-Managed Keys (Fallback)

For users who prefer not to manage their own API keys.

### How to Configure Server Keys

Add these lines to your `.env` file:

```env
# AI Service API Keys (Server-side)
OPENAI_API_KEY=your_openai_api_key_here
ELEVEN_LABS_API_KEY=your_eleven_labs_api_key_here
ELEVEN_LABS_VOICE_ID=pNInz6obpgDQGcFmaJgB
```

### Default Voice
The application uses the "Adam" voice by default (ID: `pNInz6obpgDQGcFmaJgB`). You can find other voice IDs in your Eleven Labs dashboard.

### API Endpoints
- `/api/process-image` - Uses user keys if provided, fallback to server keys
- `/api/process-image-server` - Always uses server keys
- `/api/validate-api-keys` - Validates user-provided keys

---

## 🚀 Getting Started

### With User Keys (Secure Mode)
1. **Get your API keys** from OpenAI and ElevenLabs
2. **Enter them in the app** using the secure interface
3. **Test the connection** with the "Test Keys" button
4. **Start capturing** photos immediately
5. **Optional**: Check "Remember keys" to persist across sessions

### With Server Keys (Fallback Mode)
1. **Copy your API keys** to the `.env` file
2. **Run `npm run build`** to build the frontend
3. **Start the Laravel server**: `php artisan serve`
4. **Visit the application** and grant camera permissions

---

## 🔄 Switching Between Modes

The application seamlessly supports both modes:

- **User keys provided**: Direct browser-to-API communication
- **No user keys**: Automatic fallback to server-side processing
- **Mixed usage**: Different users can use different approaches

---

## 💰 Cost Considerations

### With User Keys
- **Direct billing** to your accounts
- **Full cost control** and transparency
- **Usage monitoring** in your API dashboards
- **Set your own limits** and budgets

### With Server Keys  
- **Shared costs** across all users
- **Server owner** manages billing and limits
- **Less transparency** for individual usage

---

## 🛠️ Testing Without API Keys

The application will still work without any API keys configured:
- **Camera functionality** works normally
- **Photo capture** works normally  
- **Image analysis** returns a generic message
- **No audio generation** occurs

---

## 🔐 Security Comparison

| Feature | User Keys | Server Keys |
|---------|-----------|-------------|
| **Key Security** | ✅ Maximum - never leave browser | ⚠️ Stored on server |
| **Cost Control** | ✅ Direct billing to you | ❌ Shared server costs |
| **Usage Monitoring** | ✅ Your dashboards | ❌ Server owner only |
| **Performance** | ✅ Direct API calls | ⚠️ Through server |
| **Privacy** | ✅ Data never touches server | ❌ Images processed server-side |
| **Setup Complexity** | ⚠️ Need to get own keys | ✅ Just use the app |

---

## 🚨 Important Security Notes

### For User Keys
- **Never share your API keys** with anyone
- **Use HTTPS only** - never enter keys on non-secure sites
- **Monitor your usage** regularly in API dashboards
- **Set usage limits** in your API accounts
- **Clear keys** when using shared computers

### For Server Keys
- **Server administrators** have access to keys in `.env`
- **Images are processed** server-side with server keys
- **Shared rate limits** apply to all users
- **Less control** over individual usage

---

## 📊 Monitoring Usage

### With User Keys
Monitor usage directly in your API provider dashboards:
- [OpenAI Usage Dashboard](https://platform.openai.com/usage)
- [ElevenLabs Usage Dashboard](https://elevenlabs.io/subscription)

### With Server Keys
Contact your server administrator for usage information.

---

## 🆘 Troubleshooting

### User Key Issues
- **"Invalid key" error**: Check key format and permissions
- **CORS errors**: Ensure you're using HTTPS
- **Rate limit errors**: Check your API account limits
- **Keys not saving**: Check browser storage permissions

### Server Key Issues
- **Generic responses**: Check if server keys are configured
- **Audio not generating**: Verify ElevenLabs key in `.env`
- **Processing failures**: Check server logs for API errors

---

## 🔮 Future Enhancements

Planned security and usability improvements:
- **Key encryption** for local storage
- **Multiple voice options** for ElevenLabs
- **Usage analytics** dashboard
- **Team key management** features
- **API quota monitoring** integration

---

For additional security information, see [SECURITY.md](SECURITY.md). 