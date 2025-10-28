<!DOCTYPE html>
<html>
<body>
    <p>Chào {{ $hocVienName }},</p>

    <p>Cảm ơn bạn đã đăng ký khóa học. Đây là mã kích hoạt của bạn:</p>

    <ul>
        @foreach ($courseCodes as $item)
            <li>
                Khóa học: <strong>{{ $item['course_name'] }}</strong><br>
                Mã kích hoạt: <code>{{ $item['code'] }}</code>
            </li>
        @endforeach
    </ul>

    <p>Truy cập trang /kich-hoat và nhập mã để mở khóa khoá học.</p>

    <p>Trân trọng.</p>
</body>
</html>
