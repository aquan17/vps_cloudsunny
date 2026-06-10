<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin VPS của bạn</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; background-color: #f9fafb; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .card { background-color: #ffffff; border-radius: 8px; padding: 30px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); border-top: 4px solid #0052cc; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { color: #111827; margin: 0; font-size: 22px; }
        .content { margin-bottom: 25px; color: #4b5563; }
        
        .info-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; }
        .info-table th, .info-table td { padding: 12px 16px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        .info-table th { background-color: #f9fafb; font-weight: 600; color: #374151; width: 35%; }
        .info-table td { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; color: #111827; }
        .info-table tr:last-child th, .info-table tr:last-child td { border-bottom: none; }
        
        .highlight { color: #0052cc; font-weight: bold; }
        .warning { background-color: #fffbeb; color: #92400e; padding: 16px; border-radius: 6px; font-size: 14px; border-left: 4px solid #f59e0b; margin-top: 20px; }
        .notice { background-color: #eff6ff; color: #1e40af; padding: 16px; border-radius: 6px; font-size: 14px; border-left: 4px solid #3b82f6; margin-top: 15px; }
        
        .btn-wrapper { text-align: center; margin-top: 30px; }
        .btn { display: inline-block; background-color: #0052cc; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 6px; font-weight: 600; font-size: 14px; }
        .footer { text-align: center; color: #9ca3af; font-size: 13px; margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h1>Khởi tạo VPS Thành Công</h1>
            </div>
            <div class="content">
                <p>Xin chào,</p>
                <p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ tại NovaCloud. Máy chủ ảo <span class="highlight">{{ $vps->label }}</span> của bạn đã được khởi tạo thành công.</p>
                
                <table class="info-table">
                    <tr>
                        <th>Địa chỉ IP:</th>
                        <td>{{ $vps->public_ip ?? 'Đang cấp phát...' }}</td>
                    </tr>
                    <tr>
                        <th>Tài khoản (User):</th>
                        <td>{{ $vps->login_username ?: 'Administrator' }}</td>
                    </tr>
                    <tr>
                        <th>Mật khẩu (Pass):</th>
                        <td>{{ $password }}</td>
                    </tr>
                </table>

                <div class="notice">
                    <strong>⏳ Trạng thái máy chủ:</strong> Quá trình cài đặt hệ điều hành đang diễn ra ở chế độ nền. <strong>Vui lòng đợi thêm 2-3 phút</strong> để VPS khởi động hoàn tất trước khi bạn có thể Ping hoặc SSH vào máy chủ.
                </div>

                <div class="warning">
                    <strong>Lưu ý bảo mật:</strong> Xin vui lòng lưu lại mật khẩu này một cách an toàn. Vì lý do bảo mật, hệ thống của chúng tôi không lưu trữ mật khẩu root của bạn dưới dạng văn bản.
                </div>

                <div class="btn-wrapper">
                    <a href="{{ route('dashboard.show', $vps->id) }}" class="btn" style="color: #ffffff !important; text-decoration: none;">Quản lý VPS của bạn</a>
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} NovaCloud. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
