<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Mã OTP đặt lại mật khẩu</title>
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background: linear-gradient(120deg, #2563eb 0%, #60a5fa 100%);
                padding: 40px 20px;
                line-height: 1.6;
                color: #1f2937;
            }
            .email-wrapper {
                max-width: 640px;
                margin: 0 auto;
            }
            .email-card {
                background: #ffffff;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 25px 60px rgba(15, 23, 42, 0.25);
            }
            .email-header {
                background: linear-gradient(120deg, #1d4ed8 0%, #3b82f6 100%);
                color: #ffffff;
                padding: 36px 30px;
                text-align: center;
            }
            .email-header h1 {
                font-size: 26px;
                font-weight: 700;
                margin-bottom: 8px;
            }
            .email-body {
                padding: 40px 30px 32px;
            }
            .greeting {
                font-size: 18px;
                font-weight: 600;
                color: #1d4ed8;
                margin-bottom: 18px;
            }
            .lead-text {
                margin-bottom: 18px;
                font-size: 15px;
                color: #475569;
            }
            .otp-badge {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border: 2px dashed #2563eb;
                border-radius: 16px;
                padding: 26px;
                margin: 30px 0;
                text-align: center;
            }
            .otp-badge span {
                display: block;
                font-size: 14px;
                letter-spacing: 1px;
                color: #2563eb;
                text-transform: uppercase;
                margin-bottom: 12px;
            }
            .otp-code {
                font-size: 42px;
                font-weight: 700;
                letter-spacing: 12px;
                color: #1e3a8a;
            }
            .otp-meta {
                margin-top: 16px;
                font-size: 14px;
                color: #475569;
            }
            .reminder {
                background: #fef3c7;
                border-left: 4px solid #f59e0b;
                padding: 16px 20px;
                border-radius: 12px;
                font-size: 14px;
                color: #92400e;
                margin-bottom: 24px;
            }
            .next-step {
                font-size: 14px;
                color: #475569;
                margin-bottom: 10px;
                display: flex;
                gap: 10px;
            }
            .next-step i {
                color: #2563eb;
            }
            .email-footer {
                background: #f8fafc;
                padding: 24px 30px;
                font-size: 13px;
                color: #64748b;
                text-align: center;
                border-top: 1px solid #e2e8f0;
            }
            .email-footer strong {
                color: #1f2937;
            }
            @media (max-width: 600px) {
                body {
                    padding: 20px 12px;
                }
                .email-body {
                    padding: 30px 20px;
                }
                .otp-code {
                    font-size: 34px;
                    letter-spacing: 8px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-card">
                <header class="email-header">
                    <h1>Online Certificate Classroom</h1>
                    <p>Yêu cầu đặt lại mật khẩu của bạn</p>
                </header>
                <main class="email-body">
                    <p class="greeting">Xin chào,</p>
                    <p class="lead-text">
                        Chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản có email
                        <strong>{{ $email }}</strong>. Vui lòng sử dụng mã OTP bên dưới để hoàn tất bước xác minh.
                    </p>

                    <div class="otp-badge">
                        <span>Mã OTP</span>
                        <div class="otp-code">{{ $otp }}</div>
                        <p class="otp-meta">
                            Mã sẽ hết hạn vào
                            <strong>{{ optional($expiresAt)->format('H:i d/m/Y') }}</strong>
                            (hiệu lực {{ optional($expiresAt)->diffForHumans($sentAt ?? now(), true) }}).
                        </p>
                    </div>

                    <div class="reminder">
                        Không chia sẻ mã OTP cho bất kỳ ai. Đội ngũ {{ $appName ?? 'Online Certificate Classroom' }} sẽ
                        không bao giờ yêu cầu bạn cung cấp mã OTP qua điện thoại hoặc mạng xã hội.
                    </div>

                    <p class="next-step"><i>•</i> Quay lại cửa sổ đặt lại mật khẩu và nhập mã OTP trên.</p>
                    <p class="next-step"><i>•</i> Nếu bạn không thực hiện yêu cầu này, hãy bỏ qua email hoặc thông báo cho chúng tôi.</p>

                    <p style="margin-top: 24px;">
                        Cần hỗ trợ? Gửi email cho chúng tôi qua <strong>{{ $supportEmail ?? 'support@example.com' }}</strong>.
                    </p>
                </main>
                <footer class="email-footer">
                    <p><strong>{{ $appName ?? 'Online Certificate Classroom' }}</strong></p>
                    <p>An toàn thông tin của bạn là ưu tiên của chúng tôi.</p>
                </footer>
            </div>
        </div>
    </body>
</html>
