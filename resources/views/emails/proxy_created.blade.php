<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin Proxy của bạn</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-top: 4px solid #10b981; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { color: #111827; margin: 0; font-size: 22px; }
        .content { margin-bottom: 25px; color: #4b5563; }
        
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .info-table th, .info-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .info-table th { background-color: #f9fafb; font-weight: 600; color: #374151; width: 35%; }
        .info-table td { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #111827; }
        .info-table tr:last-child th, .info-table tr:last-child td { border-bottom: none; }
        
        .highlight { color: #10b981; font-weight: bold; }
        .notice { background-color: #ecfdf5; color: #047857; padding: 16px; border-radius: 6px; font-size: 14px; border-left: 4px solid #10b981; margin-top: 15px; }
        
        .btn-wrapper { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; background-color: #10b981; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; }
        .footer { text-align: center; color: #9ca3af; font-size: 13px; margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Khởi tạo Proxy Thành Công</h1>
            </div>
            <div class="content">
                <p>Xin chào,</p>
                <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ tại hệ thống của chúng tôi. Proxy <span class="highlight">#{{ $proxy->id }}</span> của bạn đã được khởi tạo và cấp phát thành công.</p>
                
                <table class="info-table">
                    <tr>
                        <th>Địa chỉ IP:</th>
                        <td>{{ $proxy->ip }}</td>
                    </tr>
                    @if($proxy->port)
                    <tr>
                        <th>HTTP Port:</th>
                        <td>{{ $proxy->port }}</td>
                    </tr>
                    @endif
                    @if($proxy->username)
                    <tr>
                        <th>HTTP User:</th>
                        <td>{{ $proxy->username }}</td>
                    </tr>
                    <tr>
                        <th>HTTP Pass:</th>
                        <td>{{ $proxy->password }}</td>
                    </tr>
                    @endif
                    @if($proxy->sock5_port)
                    <tr>
                        <th>SOCKS5 Port:</th>
                        <td>{{ $proxy->sock5_port }}</td>
                    </tr>
                    @endif
                    @if($proxy->sock5_username)
                    <tr>
                        <th>SOCKS5 User:</th>
                        <td>{{ $proxy->sock5_username }}</td>
                    </tr>
                    <tr>
                        <th>SOCKS5 Pass:</th>
                        <td>{{ $proxy->sock5_password }}</td>
                    </tr>
                    @endif
                </table>

                <div class="notice">
                    <strong>✅ Trạng thái:</strong> Proxy của bạn đã <strong>sẵn sàng để sử dụng ngay bây giờ</strong>.
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('proxy.show', $proxy->id) }}" class="btn" style="color: #ffffff !important; text-decoration: none;">Xem chi tiết Proxy</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} Proxy System. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
