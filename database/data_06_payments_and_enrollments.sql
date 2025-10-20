USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 12) PHƯƠNG THỨC THANH TOÁN (mẫu)
-- =========================================================
INSERT INTO PHUONGTHUCTHANHTOAN (maTT, tenPhuongThuc) VALUES
('TT01', 'Chuyển khoản ngân hàng'),
('TT02', 'Ví điện tử'),
('TT03', 'Thẻ tín dụng')
ON DUPLICATE KEY UPDATE tenPhuongThuc=VALUES(tenPhuongThuc);

-- =========================================================
-- 13) ENROLL 3 HỌC VIÊN VÀO 2 KHÓA TOEIC - SỬA LỖI VÀ CẬP NHẬT NGÀY
-- =========================================================
INSERT INTO HOCVIEN_KHOAHOC (maHV, maKH, ngayNhapHoc, trangThai, activated_at) VALUES
(@hv_trinh, @kh_speaking, '2025-02-01', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_trinh, @kh_writing, '2025-02-10', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_luan,  @kh_listening, '2025-02-05', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_luan,  @kh_reading, '2025-02-12', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_tri,   @kh_speaking, '2025-10-15', 'ACTIVE', '2025-10-15 00:00:00'),
(@hv_tri,   @kh_writing, '2025-10-15', 'ACTIVE', '2025-10-15 00:00:00');

COMMIT;