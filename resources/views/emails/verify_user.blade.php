<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Email</title>
</head>
<body>
<h1>Welcome, {{ $userName }}!</h1>
<p>Thank you for registering. Please verify your email address by clicking the link below:</p>
<a href="{{ $verificationUrl }}">Verify Email</a>
</body>
</html>
