USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 12) PHƯƠNG THỨC THANH TOÁN (mẫu)
-- =========================================================
INSERT INTO PHUONGTHUCTHANHTOAN (maTT, tenPhuongThuc) VALUES
('TT01', 'Chuyển khoản ngân hàng'),
('TT02', 'Ví điện tử'),
('TT03', 'Thẻ tín dụng'),
('TT04', 'Thanh toán qua VNPAY')
ON DUPLICATE KEY UPDATE tenPhuongThuc=VALUES(tenPhuongThuc);

-- =========================================================
-- 13) ENROLL 3 HỌC VIÊN VÀO 2 KHÓA TOEIC - SỬA LỖI VÀ CẬP NHẬT NGÀY
-- =========================================================
INSERT INTO HOCVIEN_KHOAHOC (maHV, maKH, ngayNhapHoc, trangThai, activated_at) VALUES
(@hv_trinh, @kh_reading_405_600, '2025-02-01', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_trinh, @kh_writing_405_600, '2025-02-10', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_trinh, @kh_speaking_405_600, '2025-02-10', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_trinh, @kh_listening_405_600, '2025-02-10', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_trinh, @kh_writing_605_780, '2025-02-10', 'ACTIVE', '2025-10-15 00:00:00');

COMMIT;
