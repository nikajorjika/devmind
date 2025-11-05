<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workspace Invitation</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .container {
            background-color: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 32px;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            color: #111827;
            font-size: 24px;
            margin: 0;
        }
        .content {
            margin-bottom: 32px;
        }
        .content p {
            margin: 16px 0;
            color: #4b5563;
        }
        .button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
        }
        .button:hover {
            background-color: #2563eb;
        }
        .button-container {
            text-align: center;
            margin: 32px 0;
        }
        .details {
            background-color: #f9fafb;
            border-radius: 6px;
            padding: 16px;
            margin: 24px 0;
        }
        .details p {
            margin: 8px 0;
            font-size: 14px;
        }
        .footer {
            text-align: center;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 You've Been Invited!</h1>
        </div>
        
        <div class="content">
            <p>Hi there,</p>
            
            <p>
                <strong>{{ $inviterName }}</strong> has invited you to join the 
                <strong>{{ $workspaceName }}</strong> workspace as a <strong>{{ $roleName }}</strong>.
            </p>
            
            <div class="details">
                <p><strong>Workspace:</strong> {{ $workspaceName }}</p>
                <p><strong>Role:</strong> {{ $roleName }}</p>
                <p><strong>Invited by:</strong> {{ $inviterName }}</p>
                @if($expiresAt)
                <p><strong>Expires:</strong> {{ $expiresAt->format('F j, Y \a\t g:i A') }}</p>
                @endif
            </div>
            
            <p>Click the button below to accept your invitation and get started:</p>
        </div>
        
        <div class="button-container">
            <a href="{{ $acceptUrl }}" class="button">Accept Invitation</a>
        </div>
        
        <div class="footer">
            <p>
                If you're having trouble clicking the button, copy and paste the URL below into your web browser:
            </p>
            <p style="word-break: break-all; color: #3b82f6;">
                {{ $acceptUrl }}
            </p>
            <p style="margin-top: 16px;">
                If you didn't expect this invitation, you can safely ignore this email.
            </p>
        </div>
    </div>
</body>
</html>
