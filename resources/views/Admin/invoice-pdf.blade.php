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
            min-width: 125px;
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
        table.detail th.text-end,
        table.detail td.text-end {
            text-align: right;
        }
        .item-name {
            font-weight: 600;
        }
        .item-meta {
            margin-top: 4px;
            font-size: 11px;
            color: #6f6f6f;
        }
        .course-list {
            margin: 6px 0 0;
            padding-left: 18px;
            color: #4a4a4a;
            font-size: 12px;
        }
        .course-list li {
            margin-bottom: 2px;
        }
        .course-list .course-code {
            color: #888;
            font-size: 11px;
        }
        .type-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .type-badge.course {
            background: #e8f1ff;
            color: #1f5bd8;
        }
        .type-badge.combo {
            background: #ecf8f6;
            color: #1a7a6a;
        }
        .totals {
            margin-top: 16px;
            text-align: right;
        }
        .totals strong {
            font-size: 15px;
        }
        .totals span.meta {
            display: block;
            font-size: 12px;
            color: #6f6f6f;
            margin-top: 4px;
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

    <h2 class="section-title">Danh sách sản phẩm</h2>
    <table class="detail">
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Mã</th>
                <th>Loại</th>
                <th class="text-end">Số lượng</th>
                <th class="text-end">Đơn giá</th>
                <th class="text-end">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="item-name">{{ $item['product_name'] }}</div>
                        @if (!empty($item['promotion_name']) || !empty($item['promotion_code']))
                            <div class="item-meta">
                                Khuyến mãi: {{ $item['promotion_name'] ?? ('Mã ' . $item['promotion_code']) }}
                            </div>
                        @endif
                        @if (!empty($item['courses']))
                            <ul class="course-list">
                                @foreach ($item['courses'] as $course)
                                    <li>{{ $course['name'] }} <span class="course-code">(#{{ $course['id'] }})</span></li>
                                @endforeach
                            </ul>
                        @endif
                    </td>
                    <td>{{ $item['product_id'] }}</td>
                    <td><span class="type-badge {{ $item['type'] === 'combo' ? 'combo' : 'course' }}">{{ $item['type_label'] }}</span></td>
                    <td class="text-end">{{ $item['quantity'] }}</td>
                    <td class="text-end">{{ number_format($item['unit_price']) }} VND</td>
                    <td class="text-end">{{ number_format($item['line_total']) }} VND</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @php
        $breakdown = $breakdown ?? ['courses' => 0, 'combos' => 0];
    @endphp
    <div class="totals">
        <div>Tổng số sản phẩm: <strong>{{ $totalQuantity }}</strong></div>
        <span class="meta">Khóa học: {{ $breakdown['courses'] ?? 0 }} • Combo: {{ $breakdown['combos'] ?? 0 }}</span>
        <div>Tổng thanh toán: <strong>{{ number_format($totalAmount) }} VND</strong></div>
    </div>

    <div class="note">
        <strong>Lưu ý:</strong>
        <div>Hóa đơn được phát hành bởi hệ thống Online Certificate Classroom. Vui lòng liên hệ bộ phận hỗ trợ nếu cần thêm thông tin.</div>
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Online Certificate Classroom. Tất cả các quyền được bảo lưu.
    </div>
</body>
</html>