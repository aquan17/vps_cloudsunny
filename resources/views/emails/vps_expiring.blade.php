<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 8px; }
        .header { background: #fef2f2; border-left: 4px solid #ef4444; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .footer { margin-top: 30px; font-size: 12px; color: #888; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #0284c7; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 15px; }
        .warning-text { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin: 0; color: #b91c1c;">Cảnh báo: VPS sắp hết hạn</h2>
        </div>
        
        <p>Chào bạn,</p>
        <p>Hệ thống NovaCloud xin thông báo VPS <strong>{{ $vps->label }}</strong> (IP: {{ $vps->public_ip }}) của bạn sẽ hết hạn trong <span class="warning-text">{{ $daysLeft }} ngày nữa</span>.</p>
        
        <ul>
            <li><strong>Tên VPS:</strong> {{ $vps->label }}</li>
            <li><strong>IP:</strong> {{ $vps->public_ip }}</li>
            <li><strong>Ngày hết hạn:</strong> {{ $vps->expires_at->format('d/m/Y H:i') }}</li>
        </ul>

        <p class="warning-text">⚠️ LƯU Ý QUAN TRỌNG:</p>
        <p>Nếu bạn không gia hạn kịp thời, hệ thống sẽ tự động <strong>tắt và xóa toàn bộ dữ liệu</strong> trên VPS vào ngày hết hạn. Vui lòng tiến hành <strong>BACKUP DỮ LIỆU</strong> hoặc nạp tiền để gia hạn ngay nhằm tránh gián đoạn dịch vụ.</p>
        
        <div style="text-align: center;">
            <a href="{{ config('app.url') }}/dashboard" class="btn" style="color: #ffffff !important; text-decoration: none;">Đăng nhập & Quản lý VPS</a>
        </div>
        
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống NovaCloud. Vui lòng không trả lời.</p>
        </div>
    </div>
</body>
</html>
