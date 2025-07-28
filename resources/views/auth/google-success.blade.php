<!DOCTYPE html>
<html>
<head>
    <title>Authentication Successful</title>
</head>
<body>
    <script>
        // Send success message to parent window
        if (window.opener) {
            window.opener.postMessage({
                type: 'GOOGLE_AUTH_SUCCESS',
                user: @json($user)
            }, window.location.origin);
            window.close();
        } else {
            // Fallback: redirect to main app
            window.location.href = '/';
        }
    </script>
    <div style="text-align: center; padding: 50px; font-family: Arial, sans-serif;">
        <h2>Authentication Successful!</h2>
        <p>This window will close automatically...</p>
    </div>
</body>
</html> 