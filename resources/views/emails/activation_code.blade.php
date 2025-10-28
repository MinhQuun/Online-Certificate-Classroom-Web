<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Mã kích hoạt khóa học</title>
        @php
            $styles = file_exists(public_path('css/Student/email-activation-code.css'))
                ? file_get_contents(public_path('css/Student/email-activation-code.css'))
                : '';
        @endphp
        <style>{{ $styles }}</style>
    </head>
    <body>
        <div class="email-wrapper">
            <div class="email-container">
                <header class="email-header">
                    <h1>Online Certificate Classroom</h1>
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

                    <p class="email-note">Lưu ý: Mỗi mã chỉ sử dụng được một lần cho tài khoản của bạn. Vui lòng không chia sẻ mã kích hoạt cho người khác.</p>
                </div>

                <footer class="email-footer">
                    <p><strong>Online Certificate Classroom</strong><br>
                    Hotline: 0999.999.999 · Email: support@occ.edu.vn</p>
                    <p>Đây là email tự động, vui lòng không trả lời trực tiếp.</p>
                </footer>
            </div>
        </div>
    </body>
</html>
