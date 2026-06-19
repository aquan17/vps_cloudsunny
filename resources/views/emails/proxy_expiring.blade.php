<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông báo hết hạn Proxy</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-top: 4px solid #ef4444; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { color: #111827; margin: 0; font-size: 22px; }
        .content { margin-bottom: 25px; color: #4b5563; }
        
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .info-table th, .info-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .info-table th { background-color: #f9fafb; font-weight: 600; color: #374151; width: 35%; }
        .info-table td { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #111827; }
        .info-table tr:last-child th, .info-table tr:last-child td { border-bottom: none; }
        
        .highlight { color: #ef4444; font-weight: bold; }
        .warning { background-color: #fef2f2; color: #b91c1c; padding: 16px; border-radius: 6px; font-size: 14px; border-left: 4px solid #ef4444; margin-top: 20px; }
        
        .btn-wrapper { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; background-color: #ef4444; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; }
        .footer { text-align: center; color: #9ca3af; font-size: 13px; margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Proxy sắp hết hạn</h1>
            </div>
            <div class="content">
                <p>Xin chào,</p>
                <p>Hệ thống thông báo Proxy <span class="highlight">#{{ $proxy->id }}</span> của bạn sẽ hết hạn trong vòng <strong>{{ $daysRemaining }} ngày</strong> tới.</p>
                
                <table class="info-table">
                    <tr>
                        <th>Địa chỉ IP:</th>
                        <td>{{ $proxy->ip }}</td>
                    </tr>
                    <tr>
                        <th>Ngày hết hạn:</th>
                        <td><span class="highlight">{{ \Carbon\Carbon::parse($proxy->expires_at)->format('H:i d/m/Y') }}</span></td>
                    </tr>
                </table>

                <div class="warning">
                    <strong>Lưu ý quan trọng:</strong> Nếu bạn không gia hạn trước thời điểm trên, Proxy sẽ bị thu hồi tự động và không thể khôi phục lại IP này. Vui lòng nạp tiền và gia hạn sớm để tránh gián đoạn dịch vụ.
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('proxy.show', $proxy->id) }}" class="btn" style="color: #ffffff !important; text-decoration: none;">Gia hạn Proxy ngay</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Proxy System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
