@extends('layouts.student')

@section('title', 'Về chúng tôi')

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
                <span class="n">100%</span>
                <span class="t">Học Online • Linh hoạt thời gian</span>
            </div>
            <div class="stat">
                <span class="n">Đa dạng</span>
                <span class="t">Video • Audio • PDF • Mini-test</span>
            </div>
            <div class="stat">
                <span class="n">Chứng chỉ</span>
                <span class="t">Được xác thực sau khi hoàn thành</span>
            </div>
        </div>
    </section>

    {{-- GRID 2 cột --}}
    <section class="about-grid">
        {{-- Cột trái --}}
        <article class="about-card">
            <div class="section__header">
                <h2>Về Dự Án</h2>
            </div>
            <div class="divider"></div>
            <p>
                <strong>Online Certificate Classroom</strong> là nền tảng e-learning được xây dựng với mục đích
                cung cấp giải pháp học tập trực tuyến toàn diện cho các khóa học chứng chỉ. Hệ thống cho phép
                tổ chức khóa học theo chương - bài học, tích hợp tài nguyên đa dạng và đánh giá định kỳ qua mini-test.
            </p>
            <div class="section__header">
                <h2>Tính Năng Nổi Bật</h2>
            </div>
            <div class="divider"></div>
            <ul class="about-list">
                <li><strong>Lộ trình học tập rõ ràng</strong>: Khóa học được cấu trúc theo chương và bài học tuần tự.</li>
                <li><strong>Tài nguyên đa dạng</strong>: Video giảng dạy, Audio luyện nghe, PDF tài liệu, Mini-test đánh giá.</li>
                <li><strong>Theo dõi tiến độ</strong>: Hệ thống tự động ghi nhận và hiển thị tiến độ học tập chi tiết.</li>
                <li><strong>Chứng chỉ số</strong>: Cấp chứng chỉ điện tử sau khi hoàn thành khóa học với mã xác thực.</li>
                <li><strong>Thanh toán linh hoạt</strong>: Tích hợp VNPay và hỗ trợ mã kích hoạt.</li>
                <li><strong>Giao diện responsive</strong>: Tối ưu trên mọi thiết bị, dễ sử dụng.</li>
            </ul>
            <div class="section__header">
                <h2>Quy Trình Học Tập</h2>
            </div>
            <div class="divider"></div>
            <ul class="about-list">
                <li><strong>Bước 1 - Đăng ký</strong>: Chọn khóa học phù hợp và đăng ký qua thanh toán trực tuyến hoặc mã kích hoạt.</li>
                <li><strong>Bước 2 - Học tập</strong>: Theo dõi lộ trình học, xem video, nghe audio, đọc tài liệu PDF.</li>
                <li><strong>Bước 3 - Thực hành</strong>: Làm mini-test sau mỗi chương để củng cố kiến thức.</li>
                <li><strong>Bước 4 - Hoàn thành</strong>: Đạt đủ tiêu chuẩn và nhận chứng chỉ số có mã xác thực.</li>
            </ul>
            <div class="section__header">
                <h2>Giá Trị Cốt Lõi</h2>
            </div>
            <div class="divider"></div>
            <ul class="team-list">
                <li><strong>Chất lượng</strong>: Nội dung khóa học được biên soạn bởi chuyên gia</li>
                <li><strong>Linh hoạt</strong>: Học mọi lúc, mọi nơi theo tiến độ cá nhân</li>
                <li><strong>Hiệu quả</strong>: Phương pháp học tập khoa học, đo lường được</li>
                <li><strong>Tận tâm</strong>: Hỗ trợ học viên nhiệt tình, chu đáo</li>
            </ul>
        </article>

        {{-- Cột phải --}}
        <aside class="about-card">
            <div class="section__header">
                <h2>Đội Ngũ Phát Triển</h2>
            </div>
            <div class="divider"></div>
            <div class="user-roles">
                <div class="role-item">
                    <div class="role-icon">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <h3>Học linh hoạt</h3>
                    <p>Học mọi lúc, mọi nơi theo lịch trình riêng. Không bị ràng buộc thời gian cố định.</p>
                </div>
                <div class="role-item">
                    <div class="role-icon">
                        <i class="fa-solid fa-certificate"></i>
                    </div>
                    <h3>Chứng chỉ uy tín</h3>
                    <p>Nhận chứng chỉ số có mã xác thực sau khi hoàn thành khóa học, được công nhận rộng rãi.</p>
                </div>
                <div class="role-item">
                    <div class="role-icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <h3>Giảng viên chất lượng</h3>
                    <p>Đội ngũ giảng viên giàu kinh nghiệm, nhiệt tình hỗ trợ và giải đáp thắc mắc.</p>
                </div>
            </div>

            <div class="section__header">
                <h2>Sứ Mệnh</h2>
            </div>
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
            <div class="section__header">
                <h2>Cam Kết Chất Lượng</h2>
            </div>
            <div class="divider"></div>
            <ul class="team-list">
                <li><strong>Nội dung chuẩn mực</strong>: Khóa học được biên soạn kỹ lưỡng, cập nhật thường xuyên</li>
                <li><strong>Hỗ trợ tận tình</strong>: Giải đáp thắc mắc nhanh chóng qua nhiều kênh</li>
                <li><strong>Theo dõi tiến độ</strong>: Hệ thống ghi nhận chi tiết quá trình học tập của bạn</li>
                <li><strong>Bài tập thực hành</strong>: Mini-test sau mỗi chương giúp củng cố kiến thức</li>
                <li><strong>Chứng chỉ có giá trị</strong>: Được xác thực và có thể chia sẻ dễ dàng</li>
            </ul>
            <div class="section__header">
                <h2>Liên Hệ</h2>
            </div>
            <div class="divider"></div>
            <p class="contact-note">
                Mọi thắc mắc và góp ý xin vui lòng liên hệ qua email:
                <a href="mailto:support@occ.edu.vn">support@occ.edu.vn</a> hoặc hotline:
                <a href="tel:+84968000000">0968 000 000</a>. Chúng tôi luôn sẵn sàng lắng nghe
                và hỗ trợ bạn!
            </p>
        </aside>
    </section>
</main>
@endsection
