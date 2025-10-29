<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hóa đơn #{{ $invoice->maHD }}</title>
    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 13px;
            color: #1b1b1b;
        }
        .header {
            text-align: center;
            margin-bottom: 24px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .meta {
            margin-bottom: 24px;
        }
        .meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta td {
            padding: 6px 0;
            vertical-align: top;
        }
        .meta strong {
            display: inline-block;
            min-width: 120px;
        }
        .section-title {
            font-size: 16px;
            margin: 24px 0 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        table.detail {
            width: 100%;
            border-collapse: collapse;
        }
        table.detail th,
        table.detail td {
            border: 1px solid #d0d0d0;
            padding: 8px 10px;
            text-align: left;
        }
        table.detail th {
            background: #f4f6f8;
        }
        table.detail td.text-end {
            text-align: right;
        }
        .totals {
            margin-top: 16px;
            text-align: right;
        }
        .totals strong {
            font-size: 15px;
        }
        .note {
            margin-top: 18px;
            padding: 10px;
            border: 1px dashed #bbb;
            background: #fafafa;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Hóa đơn #{{ $invoice->maHD }}</h1>
        <p>Online Certificate Classroom</p>
    </div>

    <div class="meta">
        <table>
            <tr>
                <td>
                    <strong>Ngày lập:</strong>
                    {{ $issuedAt ? $issuedAt->format('d/m/Y H:i') : 'N/A' }}
                </td>
                <td>
                    <strong>Phương thức:</strong>
                    {{ $invoice->paymentMethod->tenPhuongThuc ?? ($invoice->maTT ?: 'N/A') }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Học viên:</strong>
                    {{ $student->hoTen ?? $user->name ?? 'Chưa xác định' }}
                </td>
                <td>
                    <strong>Email:</strong>
                    {{ $user->email ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Số điện thoại:</strong>
                    {{ $user->sdt ?? $user->phone ?? 'N/A' }}
                </td>
                <td>
                    <strong>Mã học viên:</strong>
                    {{ $student->maHV ?? 'N/A' }}
                </td>
            </tr>
            <tr>
                <td>
                    <strong>Người xử lý:</strong>
                    {{ $invoice->maND ? 'User #' . $invoice->maND : 'Tự động' }}
                </td>
                <td>
                    <strong>Ghi chú:</strong>
                    {{ $invoice->ghiChu ?? 'Không có' }}
                </td>
            </tr>
        </table>
    </div>

    <h2 class="section-title">Danh sách khóa học</h2>
    <table class="detail">
        <thead>
            <tr>
                <th>#</th>
                <th>Khóa học</th>
                <th>Mã KH</th>
                <th class="text-end">Số lượng</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['course_name'] }}</td>
                    <td>{{ $item['course_id'] }}</td>
                    <td class="text-end">{{ $item['quantity'] }}</td>
                    <td class="text-end">{{ number_format($item['unit_price']) }} VND</td>
                    <td class="text-end">{{ number_format($item['line_total']) }} VND</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div>Tổng số khóa học: <strong>{{ $totalQuantity }}</strong></div>
        <div>Tổng thanh toán: <strong>{{ number_format($totalAmount) }} VND</strong></div>
    </div>

    <div class="note">
        <strong>Lưu ý:</strong>
        <div>Hóa đơn được phát hành bởi hệ thống Online Certificate Classroom. Vui lòng liên hệ bộ phận hỗ trợ nếu cần thông tin bổ sung.</div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Online Certificate Classroom. Tất cả các quyền được bảo lưu.
    </div>
</body>
</html>
