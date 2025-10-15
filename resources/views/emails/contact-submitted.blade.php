<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New Contact Form Submission</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .field { margin-bottom: 15px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; }
        .meta { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Contact Form Submission</h1>
            <p>You have received a new message through your contact form.</p>
        </div>
        
        <div class="content">
            <div class="field">
                <div class="label">Name:</div>
                <div class="value">{{ $submission->name }}</div>
            </div>
            
            <div class="field">
                <div class="label">Email:</div>
                <div class="value">{{ $submission->email }}</div>
            </div>
            
            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">{{ $submission->subject }}</div>
            </div>
            
            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{{ $submission->message }}</div>
            </div>
            
            <div class="meta">
                <p><strong>Submitted:</strong> {{ $submission->created_at->format('F j, Y \a\t g:i A') }}</p>
                @if($submission->meta)
                    <p><strong>IP Address:</strong> {{ $submission->meta['ip_address'] ?? 'N/A' }}</p>
                    <p><strong>User Agent:</strong> {{ $submission->meta['user_agent'] ?? 'N/A' }}</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
