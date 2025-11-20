<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daily Widget Report</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .header { background: #059669; color: white; padding: 20px; border-radius: 5px; margin-bottom: 20px; }
        .content { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .section { margin-bottom: 30px; }
        .section-title { font-size: 1.2em; font-weight: bold; color: #059669; margin-bottom: 15px; border-bottom: 2px solid #059669; padding-bottom: 5px; }
        .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-top: 15px; }
        .stat-box { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #059669; }
        .stat-label { font-size: 0.9em; color: #666; margin-bottom: 5px; }
        .stat-value { font-size: 1.5em; font-weight: bold; color: #333; }
        .comparison { font-size: 0.85em; color: #666; margin-top: 5px; }
        .footer { margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 0.9em; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Daily Widget Report</h1>
            <p>Report Date: {{ \Carbon\Carbon::parse($reportData['report_date'])->format('F j, Y') }}</p>
        </div>
        
        <div class="content">
            <div class="section">
                <div class="section-title">Today's Activity</div>
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-label">Widgets Created</div>
                        <div class="stat-value">{{ $reportData['today']['created'] }}</div>
                        @if(isset($reportData['yesterday']['created']))
                            <div class="comparison">
                                Yesterday: {{ $reportData['yesterday']['created'] }}
                                @php
                                    $change = $reportData['today']['created'] - $reportData['yesterday']['created'];
                                    $sign = $change >= 0 ? '+' : '';
                                @endphp
                                ({{ $sign }}{{ $change }})
                            </div>
                        @endif
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Widgets Updated</div>
                        <div class="stat-value">{{ $reportData['today']['updated'] }}</div>
                        @if(isset($reportData['yesterday']['updated']))
                            <div class="comparison">
                                Yesterday: {{ $reportData['yesterday']['updated'] }}
                                @php
                                    $change = $reportData['today']['updated'] - $reportData['yesterday']['updated'];
                                    $sign = $change >= 0 ? '+' : '';
                                @endphp
                                ({{ $sign }}{{ $change }})
                            </div>
                        @endif
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Widgets Deleted</div>
                        <div class="stat-value">{{ $reportData['today']['deleted'] }}</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Widgets Processed</div>
                        <div class="stat-value">{{ $reportData['today']['processed'] }}</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Follow-up Emails Sent</div>
                        <div class="stat-value">{{ $reportData['today']['emails_sent'] }}</div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <div class="section-title">Total Statistics</div>
                <div class="stats-grid">
                    <div class="stat-box">
                        <div class="stat-label">Total Widgets</div>
                        <div class="stat-value">{{ $reportData['totals']['total'] }}</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Active</div>
                        <div class="stat-value">{{ $reportData['totals']['active'] }}</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Inactive</div>
                        <div class="stat-value">{{ $reportData['totals']['inactive'] }}</div>
                    </div>
                    
                    <div class="stat-box">
                        <div class="stat-label">Archived</div>
                        <div class="stat-value">{{ $reportData['totals']['archived'] }}</div>
                    </div>
                </div>
            </div>
            
            <div class="footer">
                <p>This is an automated daily report generated by the Widget Management System.</p>
                <p>Generated at: {{ now()->format('F j, Y \a\t g:i A') }}</p>
            </div>
        </div>
    </div>
</body>
</html>

