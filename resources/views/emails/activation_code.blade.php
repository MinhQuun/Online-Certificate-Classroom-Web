<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Mã kích hoạt khóa học</title>
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
            .activation-table {
                width: 100%;
                border-collapse: collapse;
                margin: 24px 0;
                background: #f8f9fa;
                border-radius: 12px;
                overflow: hidden;
            }
            .activation-table thead {
                background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            }
            .activation-table th {
                padding: 16px;
                text-align: left;
                font-weight: 700;
                color: #2563eb;
                font-size: 14px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                border-bottom: 2px solid #2563eb;
            }
            .activation-table td {
                padding: 16px;
                border-bottom: 1px solid #e5e7eb;
                font-size: 15px;
            }
            .activation-table tbody tr:last-child td {
                border-bottom: none;
            }
            .activation-table tbody tr:hover {
                background: #f9fafb;
            }
            .activation-code {
                display: inline-block;
                background: linear-gradient(135deg, #2563eb 0%, #60a5fa 100%);
                color: #ffffff !important;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 700;
                font-family: 'Courier New', monospace;
                font-size: 16px;
                letter-spacing: 1px;
            }
            .activation-steps {
                background: #eff6ff;
                border-left: 4px solid #2563eb;
                padding: 20px 20px 20px 40px;
                margin: 24px 0;
                border-radius: 8px;
            }
            .activation-steps li {
                margin-bottom: 12px;
                font-size: 15px;
                color: #333333;
            }
            .activation-steps li:last-child {
                margin-bottom: 0;
            }
            .activation-button {
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
            .activation-button:hover {
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
                .activation-table th,
                .activation-table td {
                    padding: 12px;
                    font-size: 14px;
                }
                .activation-code {
                    font-size: 14px;
                    padding: 6px 12px;
                }
            }
        </style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-container">
                <header class="email-header">
                    <h1>🎓 Online Certificate Classroom</h1>
                    <p>Mã kích hoạt cho đơn hàng mới của bạn</p>
                </header>

                <div class="email-body">
                    <p>Chào {{ $hocVienName ?? 'bạn' }},</p>
                    <p>Cảm ơn bạn đã lựa chọn đồng hành cùng Online Certificate Classroom. Dưới đây là mã kích hoạt tương ứng với từng khóa học bạn vừa thanh toán.</p>

                    @if(!empty($courseCodes))
                        <table class="activation-table" role="presentation" cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th align="left" width="60%">Khóa học</th>

                                    <th align="left" width="40%">Mã kích hoạt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($courseCodes as $item)
                                    <tr>
                                        <td style="vertical-align: top;">
                                            {{ $item['course_name'] ?? 'Khóa học OCC' }}
                                        </td>

                                        <td style="vertical-align: top; white-space: nowrap;">
                                            <span class="activation-code">{{ $item['code'] ?? '---' }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>Hiện chưa có mã kích hoạt nào trong email này. Nếu bạn cần hỗ trợ, vui lòng liên hệ đội ngũ OCC.</p>
                    @endif

                    <p>Để kích hoạt nhanh chóng, hãy thực hiện các bước sau:</p>
                    <ol class="activation-steps">
                        <li>Truy cập trang <strong>Mã kích hoạt</strong> của OCC.</li>
                        <li>Nhập mã tương ứng với từng khóa học.</li>
                        <li>Hoàn tất và bắt đầu học ngay khi mã hiển thị trạng thái "Đã sử dụng".</li>
                    </ol>

                    <a href="{{ url('/student/activation-codes') }}" class="activation-button">Nhập mã kích hoạt</a>

                    <div class="email-note">
                        <strong>⚠️ Lưu ý:</strong> Mỗi mã chỉ sử dụng được một lần cho tài khoản của bạn. Vui lòng không chia sẻ mã kích hoạt cho người khác.
                    </div>
                </div>

                <footer class="email-footer">
                    <p><strong>Online Certificate Classroom</strong></p>
                    <p>Địa chỉ: 140 Lê Trọng Tấn, Tây Thạnh, Tân Phú, TP.HCM</p>
                    <p>Hotline: +84 901 234 567 · Email: support@occ.edu.vn</p>
                    <p style="margin-top: 16px;">© {{ date('Y') }} Online Certificate Classroom. All rights reserved.</p>
                    <p style="margin-top: 8px; font-size: 12px; font-style: italic;">Đây là email tự động, vui lòng không trả lời trực tiếp.</p>
                </footer>
            </div>
        </div>
    </body>
</html>
