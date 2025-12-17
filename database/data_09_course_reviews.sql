USE Online_Certificate_Classroom;

START TRANSACTION;

-- Enroll thêm học viên vào các khóa và cập nhật tiến độ mẫu
INSERT INTO HOCVIEN_KHOAHOC (maHV, maKH, ngayNhapHoc, trangThai, activated_at, progress_percent, completed_at)
VALUES
(@hv_luan, @kh_writing_405_600, '2025-02-12', 'ACTIVE', '2025-02-12 08:00:00', 85, '2025-03-10 09:00:00'),
(@hv_luan, @kh_speaking_405_600, '2025-02-15', 'ACTIVE', '2025-02-15 09:30:00', 70, NULL),
(@hv_luan, @kh_listening_405_600, '2025-02-16', 'ACTIVE', '2025-02-16 07:45:00', 90, '2025-03-05 10:00:00'),
(@hv_tri,  @kh_writing_405_600, '2025-02-18', 'ACTIVE', '2025-02-18 08:15:00', 92, '2025-03-12 08:00:00'),
(@hv_tri,  @kh_reading_405_600, '2025-02-20', 'ACTIVE', '2025-02-20 10:00:00', 78, NULL),
(@hv_anh,  @kh_writing_405_600, '2025-02-22', 'ACTIVE', '2025-02-22 08:45:00', 88, '2025-03-18 09:30:00'),
(@hv_my,   @kh_speaking_405_600, '2025-02-24', 'ACTIVE', '2025-02-24 09:00:00', 82, '2025-03-16 08:40:00'),
(@hv_chau, @kh_listening_405_600, '2025-02-25', 'ACTIVE', '2025-02-25 07:50:00', 76, NULL),
(@hv_huy,  @kh_reading_405_600,   '2025-02-26', 'ACTIVE', '2025-02-26 07:50:00', 83, '2025-03-22 11:10:00'),
(@hv_bao,  @kh_writing_405_600,   '2025-02-28', 'ACTIVE', '2025-02-28 08:10:00', 65, NULL)
ON DUPLICATE KEY UPDATE
    trangThai        = VALUES(trangThai),
    ngayNhapHoc      = VALUES(ngayNhapHoc),
    activated_at     = VALUES(activated_at),
    progress_percent = VALUES(progress_percent),
    completed_at     = VALUES(completed_at),
    updated_at       = CURRENT_TIMESTAMP;

-- Thêm đánh giá khóa học mẫu từ các học viên
INSERT INTO danhgiakh (maHV, maKH, diemSo, ngayDG, nhanxet)
VALUES
(@hv_trinh, @kh_writing_405_600, 4, '2025-03-15 09:00:00', 'Nội dung rõ ràng, mentor hỗ trợ nhiệt tình.'),
(@hv_trinh, @kh_listening_405_600, 4, '2025-03-16 10:00:00', 'Bài tập nghe thực tế, dễ làm quen.'),
(@hv_luan,  @kh_writing_405_600, 5, '2025-03-18 08:30:00', 'Chủ đề phong phú, được feedback chi tiết.'),
(@hv_luan,  @kh_speaking_405_600, 4, '2025-03-19 07:50:00', 'Luyện nói có nhiều ví dụ, nên thêm video mẫu.'),
(@hv_tri,   @kh_writing_405_600, 5, '2025-03-20 09:15:00', 'Giảng viên chấm bài kĩ lưỡng, tiến bộ rõ.'),
(@hv_tri,   @kh_reading_405_600, 4, '2025-03-21 11:00:00', 'Phần giải thích đáp án chi tiết, dễ ôn tập.'),
(@hv_anh,   @kh_writing_405_600, 5, '2025-03-22 10:00:00', 'Nội dung bài viết thực tế.'),
(@hv_my,    @kh_speaking_405_600, 4, '2025-03-23 08:20:00', 'Luyện nói và nhận feedback audio hợp lý.'),
(@hv_chau,  @kh_listening_405_600, 4, '2025-03-24 09:40:00', 'audio rõ nét.'),
(@hv_huy,   @kh_reading_405_600, 5, '2025-03-25 14:10:00', 'Bài đọc gần đề thi, giải thích kĩ.'),
(@hv_bao,   @kh_writing_405_600, 4, '2025-03-26 15:00:00', 'Cần thêm bài tập band 7+, nhưng tổng quan tốt.')
ON DUPLICATE KEY UPDATE
    diemSo  = VALUES(diemSo),
    ngayDG  = VALUES(ngayDG),
    nhanxet = VALUES(nhanxet),
    updated_at = CURRENT_TIMESTAMP;

COMMIT;
