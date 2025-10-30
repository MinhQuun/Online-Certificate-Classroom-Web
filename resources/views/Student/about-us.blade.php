@extends('layouts.student')

@section('title', 'Về chúng tôi | Online Certificate Classroom')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-about-us.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="about-page">
    {{-- HERO --}}
    <section class="about-hero">
        <span class="kicker">Online Certificate Classroom • OCC Platform</span>
        <h1>Về Chúng Tôi</h1>
        <p>
            <strong>Online Certificate Classroom (OCC)</strong> là nền tảng học tập trực tuyến chuyên về 
            <strong>các khóa học chứng chỉ</strong> với lộ trình rõ ràng, tài nguyên đa dạng và hệ thống theo dõi 
            tiến độ thông minh. Mục tiêu của chúng tôi là mang đến trải nghiệm học tập hiệu quả, giúp học viên 
            đạt được chứng chỉ mong muốn một cách tối ưu nhất.
        </p>

        {{-- Số liệu nhanh --}}
        <div class="stats">
            <div class="stat">
                <span class="n">3</span>
                <span class="t">Vai trò: Học viên • Giảng viên • Quản trị</span>
            </div>
            <div class="stat">
                <span class="n">Đa dạng</span>
                <span class="t">Video • Audio • PDF • Mini-test</span>
            </div>
            <div class="stat">
                <span class="n">Theo dõi</span>
                <span class="t">Tiến độ học tập & Chứng chỉ</span>
            </div>
        </div>
    </section>

    {{-- GRID 2 cột --}}
    <section class="about-grid">
        {{-- Cột trái --}}
        <article class="about-card">
            <h2>Về Dự Án</h2>
            <div class="divider"></div>
            <p>
                <strong>Online Certificate Classroom</strong> là nền tảng e-learning được xây dựng với mục đích 
                cung cấp giải pháp học tập trực tuyến toàn diện cho các khóa học chứng chỉ. Hệ thống cho phép 
                tổ chức khóa học theo chương - bài học, tích hợp tài nguyên đa dạng và đánh giá định kỳ qua mini-test.
            </p>

            <h2 style="margin-top:18px;">Tính Năng Nổi Bật</h2>
            <div class="divider"></div>
            <ul class="about-list">
                <li><strong>Lộ trình học tập rõ ràng</strong>: Khóa học được cấu trúc theo chương và bài học tuần tự.</li>
                <li><strong>Tài nguyên đa dạng</strong>: Video giảng dạy, Audio luyện nghe, PDF tài liệu, Mini-test đánh giá.</li>
                <li><strong>Theo dõi tiến độ</strong>: Hệ thống tự động ghi nhận và hiển thị tiến độ học tập chi tiết.</li>
                <li><strong>Chứng chỉ số</strong>: Cấp chứng chỉ điện tử sau khi hoàn thành khóa học với mã xác thực.</li>
                <li><strong>Thanh toán linh hoạt</strong>: Tích hợp VNPay và hỗ trợ mã kích hoạt.</li>
                <li><strong>Giao diện responsive</strong>: Tối ưu trên mọi thiết bị, dễ sử dụng.</li>
            </ul>

            <h2 style="margin-top:18px;">Đối Tượng Sử Dụng</h2>
            <div class="divider"></div>
            <ul class="about-list">
                <li><strong>Học viên</strong>: Đăng ký khóa học, học tập theo lộ trình, làm bài tập và nhận chứng chỉ.</li>
                <li><strong>Giảng viên</strong>: Quản lý nội dung khóa học, tài liệu và theo dõi tiến độ học viên.</li>
                <li><strong>Quản trị viên</strong>: Quản lý toàn bộ hệ thống, người dùng, khóa học và thanh toán.</li>
            </ul>

            <h2 style="margin-top:18px;">Công Nghệ Sử Dụng</h2>
            <div class="divider"></div>
            <ul class="tech-chips">
                <li>Laravel 11</li>
                <li>PHP 8.2+</li>
                <li>MySQL</li>
                <li>Blade Template</li>
                <li>JavaScript</li>
                <li>Bootstrap 5</li>
                <li>Font Awesome</li>
                <li>VNPay API</li>
            </ul>
        </article>

        {{-- Cột phải --}}
        <aside class="about-card">
            <h2>Sứ Mệnh</h2>
            <div class="divider"></div>
            <p>
                Chúng tôi tin rằng giáo dục trực tuyến là tương lai, và mỗi học viên đều xứng đáng có một nền tảng 
                học tập chất lượng, dễ tiếp cận và hiệu quả. OCC cam kết:
            </p>
            <ul class="about-list">
                <li>Cung cấp khóa học chất lượng cao với lộ trình rõ ràng</li>
                <li>Hỗ trợ học viên 24/7 trong suốt hành trình học tập</li>
                <li>Cập nhật nội dung liên tục theo xu hướng mới</li>
                <li>Tạo môi trường học tập tương tác và thân thiện</li>
            </ul>

            <h2 style="margin-top:18px;">Giá Trị Cốt Lõi</h2>
            <div class="divider"></div>
            <ul class="team-list">
                <li><strong>Chất lượng</strong>: Nội dung khóa học được biên soạn bởi chuyên gia</li>
                <li><strong>Linh hoạt</strong>: Học mọi lúc, mọi nơi theo tiến độ cá nhân</li>
                <li><strong>Hiệu quả</strong>: Phương pháp học tập khoa học, đo lường được</li>
                <li><strong>Tận tâm</strong>: Hỗ trợ học viên nhiệt tình, chu đáo</li>
            </ul>

            <h2 style="margin-top:18px;">Thành Tựu</h2>
            <div class="divider"></div>
            <ul class="team-list">
                <li>Hệ thống quản lý khóa học đa cấp độ</li>
                <li>Tích hợp thanh toán trực tuyến VNPay</li>
                <li>Hệ thống mini-test và đánh giá tự động</li>
                <li>Cấp chứng chỉ số có xác thực</li>
                <li>Dashboard quản lý tiến độ chi tiết</li>
            </ul>

            <h2 style="margin-top:18px;">Liên Hệ</h2>
            <div class="divider"></div>
            <p class="contact-note">
                Mọi thắc mắc và góp ý xin vui lòng liên hệ qua email: 
                <a href="mailto:support@occ.edu.vn">support@occ.edu.vn</a> hoặc hotline: 
                <a href="tel:+84901234567">+84 901 234 567</a>. Chúng tôi luôn sẵn sàng lắng nghe 
                và hỗ trợ bạn!
            </p>
        </aside>
    </section>
</main>
@endsection
