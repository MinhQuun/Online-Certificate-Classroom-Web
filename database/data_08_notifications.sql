USE Online_Certificate_Classroom;

START TRANSACTION;

-- Map user & course ids for seeded notifications
SELECT maND INTO @nd_trinh FROM nguoidung WHERE email = 'nguyenthitutrinh120504@gmail.com';
SELECT maND INTO @nd_luan  FROM nguoidung WHERE email = 'hakachi303@gmail.com';
SELECT maND INTO @nd_tri   FROM nguoidung WHERE email = 'tringhe2004@gmail.com';

SELECT maKH INTO @kh_speaking_405_600  FROM khoahoc WHERE slug = 'luyen-thi-toeic-speaking-405-600';
SELECT maKH INTO @kh_writing_405_600   FROM khoahoc WHERE slug = 'luyen-thi-toeic-writing-405-600';
SELECT maKH INTO @kh_listening_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-listening-405-600';
SELECT maKH INTO @kh_reading_605_780   FROM khoahoc WHERE slug = 'luyen-thi-toeic-reading-605-780';

SELECT maGoi INTO @goi_intermediate FROM goi_khoa_hoc WHERE slug = 'toeic-intermediate-full-pack-605-780';

INSERT INTO thongbao (
    maND, maKH, maGoi, loai, tieuDe, noiDung, action_url, action_label, hinhAnh, metadata, is_read, read_at, created_at, updated_at
) VALUES
(@nd_trinh, @kh_writing_405_600, NULL, 'GRADE',
    'Giảng viên đã chấm bài viết',
    'Feedback chi tiết cho bài viết Writing Part 2 của bạn đã sẵn sàng. Xem để cập nhật điểm và hướng dẫn nâng điểm nhanh hơn.',
    '/student/my-courses', 'Xem phản hồi', 'toeic-writing-405-600.png',
    JSON_OBJECT('cta', 'writing_grade'), 0, NULL, '2025-11-15 08:30:00', '2025-11-15 08:30:00'),
(@nd_trinh, @kh_listening_405_600, NULL, 'COURSE',
    'Lịch live Q&A Listening tuần này',
    'Giảng viên sẽ giải đáp trực tiếp các câu hỏi Listening 405-600 vào 20:00 Thứ Năm. Đặt lời nhắc và tham gia đúng giờ.',
    '/courses/luyen-thi-toeic-listening-405-600', 'Xem lịch chi tiết', 'toeic-listening-405-600.png',
    JSON_OBJECT('cta', 'live_qna'), 0, NULL, '2025-11-16 09:15:00', '2025-11-16 09:15:00'),
(@nd_trinh, NULL, @goi_intermediate, 'PROMOTION',
    'Ưu đãi TOEIC Intermediate chỉ cho học viên đã đăng ký',
    'Combo TOEIC Intermediate Full Pack giảm 25% trong tuần này. Bạn đã hoàn thành band 405-600, nâng band ngay để lấy điểm 780+.',
    '/combos/toeic-intermediate-full-pack-605-780', 'Nhận ưu đãi', 'combo_toeic_intermediate_605-780.jpg',
    JSON_OBJECT('cta', 'upgrade_combo'), 0, NULL, '2025-11-17 07:50:00', '2025-11-17 07:50:00'),

(@nd_luan, @kh_speaking_405_600, NULL, 'COURSE',
    'Nhắc học Speaking hôm nay',
    'Bạn còn 1 nhiệm vụ phát âm và 1 video Listening đã lưu trong mục Tiếp tục học. Vào học ngay để giữ tiến độ.',
    '/courses/luyen-thi-toeic-speaking-405-600', 'Tiếp tục học', 'toeic-speaking-405-600.png',
    JSON_OBJECT('cta', 'continue_learning'), 1, '2025-11-14 06:30:00', '2025-11-14 06:00:00', '2025-11-14 06:30:00'),
(@nd_luan, NULL, NULL, 'PROMOTION',
    'Mã ưu đãi học phí cuối tuần',
    'Nhập mã OCCWEEKEND để giảm 15% cho mỗi khóa lẻ đến 23:59 Chủ Nhật. Áp dụng cho cả khách hàng cũ.',
    '/student/cart', 'Dung ma ngay', NULL,
    JSON_OBJECT('code', 'OCCWEEKEND'), 0, NULL, '2025-11-15 10:10:00', '2025-11-15 10:10:00'),

(@nd_tri, @kh_reading_605_780, @goi_intermediate, 'SYSTEM',
    'Đồng bộ tiến độ và chứng chỉ nâng band',
    'Tài khoản đã được đồng bộ. Khi hoàn thành 70% nội dung Reading 605-780 bạn sẽ được đề xuất cấp chứng chỉ tích lũy.',
    '/student/progress', 'Xem tiến độ', 'toeic-reading-605-780.png',
    JSON_OBJECT('progress_target', 70), 0, NULL, '2025-11-16 12:20:00', '2025-11-16 12:20:00');

COMMIT;
