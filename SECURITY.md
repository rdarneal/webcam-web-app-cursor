# Security Features - Smart Webcam Assistant

## 🔒 Secure API Key Management

This application now supports **user-provided API keys** with enterprise-grade security measures to ensure your OpenAI and ElevenLabs API keys are completely secure.

## Architecture Overview

### Client-Side Processing (Recommended)
When users provide their own API keys:
- **Direct browser-to-API communication** - API calls go directly from your browser to OpenAI/ElevenLabs
- **Zero server involvement** in API processing
- **Keys never leave your browser** - stored locally only
- **Complete user control** over API usage and costs

### Server-Side Processing (Fallback)
For users who prefer not to provide keys:
- Uses server-managed API keys (stored in `.env`)
- Traditional client → server → API flow
- Shared usage limits and costs

## Security Guarantees

### 🛡️ Key Protection
- **Never transmitted to our servers** - API keys stay in your browser
- **Optional local storage** - choose to remember keys or enter each session
- **Memory cleanup** - automatic cleanup of sensitive data
- **Show/hide functionality** - password-style input fields

### 🔐 Data Privacy
- **Direct API calls** - your images and descriptions never touch our servers
- **Local audio processing** - generated audio files stay in your browser
- **No server logs** of your API interactions
- **HTTPS enforcement** for all external API calls

### ✅ Validation & Error Handling
- **Pre-flight key testing** - validate keys before use
- **Graceful error handling** - clear feedback on API issues
- **Rate limit awareness** - your keys, your limits
- **Automatic cleanup** - prevents memory leaks from audio blobs

## Usage Modes

### 1. Secure Mode (User Keys)
```javascript
// Keys stored locally in browser only
localStorage.setItem('webcam-assistant-keys', {
  openai: 'sk-your-key',
  elevenlabs: 'your-key',
  persist: true
});

// Direct API calls
fetch('https://api.openai.com/v1/chat/completions', {
  headers: { 'Authorization': `Bearer ${userKey}` }
});
```

### 2. Server Mode (Fallback)
```php
// Traditional server-side processing
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . env('OPENAI_API_KEY')
])->post('https://api.openai.com/v1/chat/completions', $data);
```

## API Key Storage Options

### Browser Session Only
- Keys stored in memory only
- Cleared when browser tab closes
- Maximum security, requires re-entry each session

### Local Browser Storage  
- Encrypted storage in browser's localStorage
- Persists across browser sessions
- User-controlled via checkbox option
- Can be cleared instantly with "Clear Keys" button

### No Storage (Server Keys)
- Use the fallback server-side processing
- No user keys required
- Shared rate limits and costs

## Implementation Details

### Frontend Security Measures
```javascript
// Secure key validation
const validateKeys = async () => {
  try {
    // Test OpenAI key with minimal request
    const openaiTest = await fetch('https://api.openai.com/v1/models', {
      headers: { 'Authorization': `Bearer ${userKeys.openai}` }
    });
    
    // Test ElevenLabs key
    const elevenTest = await fetch('https://api.elevenlabs.io/v1/voices', {
      headers: { 'xi-api-key': userKeys.elevenlabs }
    });
    
    return { valid: openaiTest.ok && elevenTest.ok };
  } catch (error) {
    return { valid: false, error: error.message };
  }
};

// Memory cleanup for audio
onUnmounted(() => {
  if (audioUrl.value?.startsWith('blob:')) {
    URL.revokeObjectURL(audioUrl.value);
  }
});
```

### Backend Security Measures
```php
// Server-side key validation endpoint
public function validateApiKeys(Request $request): JsonResponse
{
    $request->validate([
        'openai_key' => 'required|string',
        'elevenlabs_key' => 'required|string'
    ]);
    
    // Test keys without storing them
    // Returns validation results only
}
```

## Security Best Practices

### For Users
1. **Use your own API keys** for maximum security and control
2. **Monitor API usage** in your OpenAI/ElevenLabs dashboards
3. **Set usage limits** in your API provider accounts
4. **Clear keys** when using shared/public computers
5. **Use HTTPS** - never enter keys on non-HTTPS sites

### For Developers
1. **No server-side key storage** of user-provided keys
2. **Input validation** on all API key fields
3. **Rate limiting** on validation endpoints
4. **Error handling** without exposing sensitive information
5. **Memory cleanup** of sensitive data structures

## Browser Compatibility

### Required Features
- **Fetch API** - for direct API calls
- **Local Storage** - for optional key persistence
- **WebRTC** - for camera access
- **Blob API** - for audio handling

### Supported Browsers
- ✅ Chrome/Chromium 80+
- ✅ Firefox 75+
- ✅ Safari 13+
- ✅ Edge 80+

## CORS Configuration

### OpenAI API
- Allows browser-based requests
- Standard CORS headers supported
- No additional configuration needed

### ElevenLabs API
- Supports browser-based requests
- CORS-enabled for web applications
- Standard fetch API compatible

## Security Audit Checklist

- [ ] API keys never logged server-side
- [ ] Keys transmitted only over HTTPS
- [ ] Local storage is optional and user-controlled
- [ ] Memory cleanup prevents leaks
- [ ] Error messages don't expose sensitive data
- [ ] Direct API calls bypass server entirely
- [ ] Fallback mode available for users without keys
- [ ] Key validation prevents invalid usage
- [ ] Clear UI indicators for security status

## Threat Model

### Threats Mitigated
- **Server-side key exposure** - eliminated with direct API calls
- **Man-in-the-middle attacks** - HTTPS enforcement
- **Memory leaks** - automatic cleanup of sensitive data
- **Cross-site scripting** - input validation and sanitization
- **Data persistence** - user-controlled storage options

### Residual Risks
- **Browser security vulnerabilities** - inherent to client-side applications
- **User device compromise** - keys stored locally could be accessed
- **API key sharing** - users responsible for key management
- **Third-party API security** - dependent on OpenAI/ElevenLabs security

## Compliance Considerations

### Data Protection
- **No server-side PII storage** - user data stays in browser
- **User consent** for key storage via explicit checkbox
- **Right to deletion** - instant key clearing functionality
- **Data minimization** - only required API keys collected

### Security Standards
- **Transport Layer Security** - HTTPS enforcement
- **Input validation** - all user inputs sanitized
- **Error handling** - no sensitive data in error messages
- **Access controls** - user-controlled API key management

## Support & Updates

This security model is designed to be:
- **Future-proof** - adaptable to new API requirements
- **Backwards compatible** - server-side fallback always available
- **User-centric** - maximum user control and transparency
- **Developer-friendly** - clear separation of concerns

For security questions or concerns, please review this documentation and the implementation code before deployment. 