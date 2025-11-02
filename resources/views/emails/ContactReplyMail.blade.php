<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Phản hồi từ Online Certificate Classroom</title>
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
            }
            .email-wrapper {
                max-width: 640px;
                margin: 0 auto;
            }
            .email-container {
                background: #ffffff;
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                overflow: hidden;
            }
            .email-header {
                background: linear-gradient(120deg, #2563eb 0%, #60a5fa 100%);
                color: #ffffff;
                padding: 40px 30px;
                text-align: center;
            }
            .email-header h1 {
                font-size: 28px;
                font-weight: 700;
                margin-bottom: 8px;
            }
            .email-header p {
                font-size: 16px;
                opacity: 0.95;
            }
            .email-body {
                padding: 40px 30px;
                color: #333333;
            }
            .email-body p {
                margin-bottom: 16px;
                font-size: 15px;
            }
            .greeting {
                font-size: 18px;
                font-weight: 600;
                color: #2563eb;
                margin-bottom: 20px;
            }
            .original-message {
                background: #f8f9fa;
                border-left: 4px solid #2563eb;
                padding: 20px;
                margin: 24px 0;
                border-radius: 8px;
            }
            .original-message h3 {
                font-size: 14px;
                font-weight: 600;
                color: #2563eb;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 12px;
            }
            .original-message pre {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                white-space: pre-wrap;
                word-wrap: break-word;
                font-size: 14px;
                color: #555555;
                margin: 0;
            }
            .reply-message {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
                border-radius: 12px;
                padding: 24px;
                margin: 24px 0;
                border: 2px solid #2563eb;
            }
            .reply-message h3 {
                font-size: 16px;
                font-weight: 700;
                color: #2563eb;
                margin-bottom: 12px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            .reply-message h3:before {
                content: "💬";
                font-size: 20px;
            }
            .reply-message pre {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                white-space: pre-wrap;
                word-wrap: break-word;
                font-size: 15px;
                color: #222222;
                margin: 0;
                line-height: 1.7;
            }
            .reply-time {
                font-size: 13px;
                color: #666666;
                margin-top: 16px;
                font-style: italic;
            }
            .contact-button {
                display: inline-block;
                background: linear-gradient(120deg, #2563eb 0%, #60a5fa 100%);
                color: #ffffff !important;
                text-decoration: none;
                padding: 14px 32px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 15px;
                margin-top: 20px;
                transition: transform 0.2s, box-shadow 0.2s;
            }
            .contact-button:hover {
                color: #ffffff !important;
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
            }
            .email-note {
                background: #fff3cd;
                border-left: 4px solid #ffc107;
                padding: 16px;
                margin: 24px 0;
                border-radius: 8px;
                font-size: 14px;
                color: #856404;
            }
            .email-footer {
                background: #f8f9fa;
                padding: 30px;
                text-align: center;
                color: #6c757d;
                font-size: 13px;
                border-top: 1px solid #e9ecef;
            }
            .email-footer p {
                margin-bottom: 8px;
            }
            .email-footer strong {
                color: #495057;
            }
            @media (max-width: 600px) {
                body {
                    padding: 20px 10px;
                }
                .email-header {
                    padding: 30px 20px;
                }
                .email-header h1 {
                    font-size: 24px;
                }
                .email-body {
                    padding: 30px 20px;
                }
                .original-message,
                .reply-message {
                    padding: 16px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-container">
                <header class="email-header">
                    <h1>📧 Online Certificate Classroom</h1>
                    <p>Phản hồi từ đội ngũ hỗ trợ</p>
                </header>

                <div class="email-body">
                    <p class="greeting">Xin chào {{ $name }},</p>
                    
                    <p>Cảm ơn bạn đã liên hệ với Online Certificate Classroom. Chúng tôi đã nhận được tin nhắn của bạn và xin gửi lại phản hồi dưới đây.</p>

                    @if(!empty($originalMessage))
                    <div class="original-message">
                        <h3>📝 Tin nhắn của bạn</h3>
                        <pre>{{ $originalMessage }}</pre>
                    </div>
                    @endif

                    @if(!empty($replyMessage))
                    <div class="reply-message">
                        <h3>Phản hồi từ chúng tôi</h3>
                        <pre>{{ $replyMessage }}</pre>
                        <p class="reply-time">Phản hồi lúc: {{ $repliedAt->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif

                    <p>Nếu bạn còn thắc mắc hoặc cần hỗ trợ thêm, đừng ngại liên hệ lại với chúng tôi qua form liên hệ hoặc email trực tiếp.</p>

                    <a href="{{ url('/contact') }}" class="contact-button">Gửi tin nhắn mới</a>

                    <div class="email-note">
                        <strong>💡 Lưu ý:</strong> Bạn có thể trả lời trực tiếp email này để tiếp tục cuộc trò chuyện với đội ngũ hỗ trợ của chúng tôi.
                    </div>
                </div>

                <footer class="email-footer">
                    <p><strong>Online Certificate Classroom</strong></p>
                    <p>Địa chỉ: 140 Lê Trọng Tấn, Tây Thạnh, Tân Phú, TP.HCM</p>
                    <p>Hotline: +84 901 234 567 · Email: support@occ.edu.vn</p>
                    <p style="margin-top: 16px;">© {{ date('Y') }} Online Certificate Classroom. All rights reserved.</p>
                </footer>
            </div>
        </div>
    </body>
</html>
