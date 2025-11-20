<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank You for Your Widget</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4f46e5; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .widget-info { background: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .field { margin-bottom: 10px; }
        .label { font-weight: bold; color: #555; }
        .value { margin-top: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You for Your Widget!</h1>
        </div>
        
        <div class="content">
            <p>Hello,</p>
            
            <p>Thank you for creating the widget <strong>{{ $widget->name }}</strong>. We wanted to follow up and let you know that your widget has been successfully processed.</p>
            
            <div class="widget-info">
                <h3>Widget Details</h3>
                <div class="field">
                    <div class="label">Name:</div>
                    <div class="value">{{ $widget->name }}</div>
                </div>
                
                @if($widget->description)
                <div class="field">
                    <div class="label">Description:</div>
                    <div class="value">{{ $widget->description }}</div>
                </div>
                @endif
                
                @if($widget->price)
                <div class="field">
                    <div class="label">Price:</div>
                    <div class="value">${{ number_format($widget->price, 2) }}</div>
                </div>
                @endif
                
                <div class="field">
                    <div class="label">Status:</div>
                    <div class="value">{{ ucfirst($widget->status) }}</div>
                </div>
                
                <div class="field">
                    <div class="label">Created:</div>
                    <div class="value">{{ $widget->created_at->format('F j, Y \a\t g:i A') }}</div>
                </div>
            </div>
            
            <p>If you have any questions or need assistance, please don't hesitate to reach out.</p>
            
            <div class="footer">
                <p>Best regards,<br>The Widget Team</p>
            </div>
        </div>
    </div>
</body>
</html>

