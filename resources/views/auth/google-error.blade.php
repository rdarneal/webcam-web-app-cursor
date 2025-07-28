<!DOCTYPE html>
<html>
<head>
    <title>Authentication Failed</title>
</head>
<body>
    <script>
        // Send error message to parent window
        if (window.opener) {
            window.opener.postMessage({
                type: 'GOOGLE_AUTH_ERROR',
                error: @json($error)
            }, window.location.origin);
            window.close();
        } else {
            // Fallback: redirect to main app
            window.location.href = '/';
        }
    </script>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h2>Authentication Failed</h2>
        <p>{{ $error }}</p>
        <p>This window will close automatically...</p>
    </div>
</body>
</html> 