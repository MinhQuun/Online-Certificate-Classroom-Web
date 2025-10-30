@extends('layouts.student')

@section('title', 'Dịch vụ | Online Certificate Classroom')

@push('styles')
    @php
        $pageStyle = 'css/Student/pages-services.css';
    @endphp
    <link rel="stylesheet" href="{{ asset($pageStyle) }}?v={{ student_asset_version($pageStyle) }}">
@endpush

@section('content')
<main class="services-page">
    <!-- HERO -->
    <section class="sv-hero">
        <span class="kicker"><i class="fa-solid fa-certificate"></i> Dịch vụ học tập</span>
        <h1>Giải pháp học chứng chỉ toàn diện cho cá nhân & doanh nghiệp</h1>
        <p>Chúng tôi đồng hành từ lộ trình học tập đến cấp chứng chỉ: khóa học cá nhân, đào tạo doanh nghiệp, tư vấn lộ trình và luyện thi chuyên sâu.</p>
        <div class="hero-actions">
            <a href="#contact" class="btn-primary"><i class="fa-regular fa-paper-plane"></i> Liên hệ tư vấn</a>
            <a href="{{ route('student.courses.index') }}" class="btn-outline"><i class="fa-regular fa-compass"></i> Xem khóa học</a>
        </div>
    </section>

    <!-- SERVICE CARDS -->
    <section class="sv-grid oc-container">
        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-trophy"></i></div>
            <h3>Lộ trình chứng chỉ TOEIC</h3>
            <p>Chương trình học TOEIC từ cơ bản đến nâng cao với lộ trình rõ ràng, mini-test theo chương và mock test đầy đủ 4 kỹ năng.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Tài liệu đa dạng (video, audio, PDF)</li>
                <li><i class="fa-regular fa-circle-check"></i> Đánh giá định kỳ theo chương</li>
                <li><i class="fa-regular fa-circle-check"></i> Giảng viên hỗ trợ 24/7</li>
            </ul>
        </article>

        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-building"></i></div>
            <h3>Đào tạo doanh nghiệp</h3>
            <p>Thiết kế khóa học theo nhu cầu doanh nghiệp: tiếng Anh giao tiếp, TOEIC, IELTS cho nhân viên với quản lý tiến độ tập trung.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Tùy chỉnh nội dung theo ngành</li>
                <li><i class="fa-regular fa-circle-check"></i> Báo cáo tiến độ chi tiết</li>
                <li><i class="fa-regular fa-circle-check"></i> Cấp chứng chỉ nội bộ</li>
            </ul>
        </article>

        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-user-graduate"></i></div>
            <h3>Tư vấn lộ trình cá nhân</h3>
            <p>Đánh giá năng lực hiện tại, xây dựng lộ trình học tập phù hợp với mục tiêu và thời gian của từng học viên.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Test đầu vào miễn phí</li>
                <li><i class="fa-regular fa-circle-check"></i> Lộ trình cá nhân hóa</li>
                <li><i class="fa-regular fa-circle-check"></i> Mentor 1-1 theo dõi</li>
            </ul>
        </article>

        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-book-open"></i></div>
            <h3>Lộ trình chứng chỉ IELTS</h3>
            <p>Khóa học IELTS toàn diện với luyện thi 4 kỹ năng, chấm bài Writing/Speaking bởi giảng viên và mock test định kỳ.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Luyện đề Cambridge thực tế</li>
                <li><i class="fa-regular fa-circle-check"></i> Chấm bài tự luận chi tiết</li>
                <li><i class="fa-regular fa-circle-check"></i> Feedback cải thiện cụ thể</li>
            </ul>
        </article>

        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-clipboard-check"></i></div>
            <h3>Luyện thi chuyên sâu</h3>
            <p>Các khóa luyện đề, tips làm bài nhanh, phân tích dạng câu hỏi và chiến thuật tối ưu điểm số.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Đề thi mô phỏng sát thực tế</li>
                <li><i class="fa-regular fa-circle-check"></i> Giải đề chi tiết từng phần</li>
                <li><i class="fa-regular fa-circle-check"></i> Tips nâng band nhanh</li>
            </ul>
        </article>

        <article class="sv-card">
            <div class="sv-icon"><i class="fa-solid fa-medal"></i></div>
            <h3>Cấp chứng chỉ hoàn thành</h3>
            <p>Chứng chỉ điện tử được cấp sau khi hoàn thành khóa học và vượt qua bài kiểm tra cuối kỳ với điểm đạt yêu cầu.</p>
            <ul class="sv-feats">
                <li><i class="fa-regular fa-circle-check"></i> Chứng chỉ có mã xác thực</li>
                <li><i class="fa-regular fa-circle-check"></i> Tra cứu online mọi lúc</li>
                <li><i class="fa-regular fa-circle-check"></i> Tải xuống định dạng PDF</li>
            </ul>
        </article>
    </section>

    <!-- PROCESS -->
    <section class="sv-process oc-container">
        <h2 class="sec-title">Quy trình học tập</h2>
        <div class="steps">
            <div class="step">
                <span class="dot">1</span>
                <h4>Đánh giá năng lực</h4>
                <p>Test đầu vào để xác định trình độ hiện tại và mục tiêu cần đạt.</p>
            </div>
            <div class="step">
                <span class="dot">2</span>
                <h4>Lựa chọn khóa học</h4>
                <p>Chọn khóa học phù hợp hoặc nhận tư vấn lộ trình cá nhân hóa.</p>
            </div>
            <div class="step">
                <span class="dot">3</span>
                <h4>Học tập & luyện tập</h4>
                <p>Học qua video, tài liệu, làm mini-test và nhận feedback từ giảng viên.</p>
            </div>
            <div class="step">
                <span class="dot">4</span>
                <h4>Kiểm tra & cấp chứng chỉ</h4>
                <p>Thi cuối khóa, đạt điểm yêu cầu và nhận chứng chỉ hoàn thành.</p>
            </div>
        </div>
    </section>

    <!-- USP STRIP -->
    <section class="sv-usp">
        <div class="usp">
            <i class="fa-regular fa-clock"></i>
            <span>Học mọi lúc, mọi nơi</span>
        </div>
        <div class="usp">
            <i class="fa-regular fa-file-lines"></i>
            <span>Tài liệu đa dạng</span>
        </div>
        <div class="usp">
            <i class="fa-regular fa-user"></i>
            <span>Mentor đồng hành</span>
        </div>
        <div class="usp">
            <i class="fa-regular fa-face-smile"></i>
            <span>Hỗ trợ 24/7</span>
        </div>
    </section>

    <!-- FAQ -->
    <section class="sv-faq oc-container">
        <h2 class="sec-title">Câu hỏi thường gặp</h2>
        <div class="faq-list">
            <details class="faq">
                <summary>Thời gian hoàn thành một khóa học là bao lâu?</summary>
                <p>Tùy thuộc vào khóa học và tiến độ cá nhân, thông thường từ 2-6 tháng. Bạn có thể học theo lộ trình linh hoạt phù hợp với thời gian của mình.</p>
            </details>
            <details class="faq">
                <summary>Chứng chỉ có được công nhận không?</summary>
                <p>Chứng chỉ hoàn thành khóa học của OCC xác nhận bạn đã hoàn thành chương trình. Đối với chứng chỉ quốc tế như TOEIC/IELTS, bạn cần đăng ký thi tại các trung tâm được ETS/IDP công nhận.</p>
            </details>
            <details class="faq">
                <summary>Có được hoàn tiền nếu không phù hợp?</summary>
                <p>Chúng tôi hỗ trợ hoàn tiền trong 7 ngày đầu nếu khóa học chưa phù hợp với bạn, theo chính sách hoàn tiền của OCC.</p>
            </details>
            <details class="faq">
                <summary>Học liệu có bị giới hạn thời gian truy cập không?</summary>
                <p>Sau khi kích hoạt khóa học, bạn có quyền truy cập trọn đời vào tài liệu và cập nhật nội dung mới.</p>
            </details>
        </div>
    </section>

    <!-- CTA -->
    <section class="sv-cta">
        <h3>Đăng ký tư vấn miễn phí ngay hôm nay</h3>
        <p>Để lại thông tin liên hệ—chúng tôi sẽ tư vấn lộ trình phù hợp nhất cho bạn.</p>
        <a href="#contact" class="btn-primary"><i class="fa-regular fa-calendar-check"></i> Liên hệ ngay</a>
    </section>
</main>
@endsection