<footer class="site-footer" id="contact">
    <div class="site-footer__top oc-container">
        <div class="footer-column">
            <h3>Online Certificate Classroom</h3>
            <p>Hệ sinh thái học chứng chỉ trực tuyến với lộ trình rõ ràng, tài nguyên đa định dạng và theo dõi tiến độ thông minh.</p>
            <div class="footer-contact">
                <a href="mailto:support@occ.edu.vn">support@occ.edu.vn</a>
                <a href="tel:+84968000000">0968 000 000</a>
            </div>
        </div>
        <div class="footer-column">
            <h4>Dịch vụ</h4>
            <ul>
                <li><a href="{{ route('student.services') }}#toeic">Lộ trình chứng chỉ TOEIC</a></li>
                <li><a href="{{ route('student.services') }}#ielts">Lộ trình chứng chỉ IELTS</a></li>
                <li><a href="{{ route('student.services') }}#corporate">Đào tạo doanh nghiệp</a></li>
                <li><a href="{{ route('student.services') }}#consulting">Tư vấn cá nhân</a></li>
            </ul>
        </div>
        <div class="footer-column" id="about">
            <h4>Về OCC</h4>
            <ul>
                <li><a href="#">Giới thiệu</a></li>
                <li><a href="#">Đội ngũ giảng viên</a></li>
                <li><a href="#">Hỏi đáp</a></li>
                <li><a href="#">Điều khoản và bảo mật</a></li>
            </ul>
        </div>
        <div class="footer-column">
            <h4>Kết nối</h4>
            <p>Đăng ký email để nhận tài nguyên và tin tức mới mỗi tuần.</p>
            <form class="footer-subscribe" action="#" method="post">
                <label for="subscribe-email" class="sr-only">Email</label>
                <input type="email" id="subscribe-email" name="email" placeholder="Nhập email của bạn">
                <button type="submit">Đăng ký</button>
            </form>
        </div>
    </div>
    <div class="site-footer__bottom">
        <div class="oc-container">
            <span>&copy; {{ date('Y') }} Online Certificate Classroom. All rights reserved.</span>
            <div class="footer-links">
                <a href="#">Chính sách bảo mật</a>
                <a href="#">Điều khoản sử dụng</a>
            </div>
        </div>
    </div>
</footer>
