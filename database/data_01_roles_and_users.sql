USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 1) QUYỀN
-- =========================================================
INSERT INTO quyen (maQuyen, tenQuyen) VALUES
('Q001', 'Quản trị viên'),
('Q002', 'Giảng viên'),
('Q003', 'Học viên')
ON DUPLICATE KEY UPDATE tenQuyen=VALUES(tenQuyen);

-- =========================================================
-- 2) NGƯỜI DÙNG (1 admin, 2 giảng viên, 3 học viên)
--  LƯU Ý: vaiTro dùng ENUM: ADMIN | GIAO_VU | GIANG_VIEN | HOC_VIEN
--  Mật khẩu là ví dụ placeholder, thay bằng hash thật trong Laravel.
-- =========================================================
INSERT INTO nguoidung (hoTen, email, sdt, matKhau, chuyenMon, vaiTro) VALUES
('Võ Nguyễn Minh Quân',    'vonguyenminhquan20052004@gmail.com',    '0966546750', '123456', 'Quản trị hệ thống', 'ADMIN'),
('Nguyễn Phạm Trường Duy', 'nptduyc920@gmail.com',             '0796177075', '123456', 'Ngôn ngữ Anh',      'GIANG_VIEN'),
('Trương Quang Như Đoan',  'truongdoan76qn@gmail.com',         '0866503201', '123456', 'Ngôn ngữ Anh',      'GIANG_VIEN'),
('Nguyễn Thị Tú Trinh',    'nguyenthitutrinh120504@gmail.com', '0564609210', '123456',  NULL,               'HOC_VIEN'),
('Trần Minh Luân',         'hakachi303@gmail.com',             '0389137204', '123456',  NULL,               'HOC_VIEN'),
('Nghê Minh Trí',          'tringhe2004@gmail.com',            '0856780003', '123456',  NULL,               'HOC_VIEN');

-- Lấy id theo email để map quyền/học viên
SELECT maND INTO @nd_admin1   FROM nguoidung WHERE email='vonguyenminhquan20052004@gmail.com';
SELECT maND INTO @nd_teacher1 FROM nguoidung WHERE email='nptduyc920@gmail.com';
SELECT maND INTO @nd_teacher2 FROM nguoidung WHERE email='truongdoan76qn@gmail.com';
SELECT maND INTO @nd_student1 FROM nguoidung WHERE email='nguyenthitutrinh120504@gmail.com';
SELECT maND INTO @nd_student2 FROM nguoidung WHERE email='hakachi303@gmail.com';
SELECT maND INTO @nd_student3 FROM nguoidung WHERE email='tringhe2004@gmail.com';

-- =========================================================
-- 3) GÁN QUYỀN
-- =========================================================
INSERT IGNORE INTO quyen_nguoidung (maND, maQuyen) VALUES
(@nd_admin1,   'Q001'),
(@nd_teacher1, 'Q002'),
(@nd_teacher2, 'Q002'),
(@nd_student1, 'Q003'),
(@nd_student2, 'Q003'),
(@nd_student3, 'Q003');

-- =========================================================
-- 4) HỒ SƠ HỌC VIÊN (1-1 với nguoidung)
-- =========================================================
INSERT INTO hocvien (maND, hoTen, ngaySinh, ngayNhapHoc) VALUES
(@nd_student1, 'Nguyễn Thị Tú Trinh', '2004-05-12', '2025-01-10'),
(@nd_student2, 'Trần Minh Luân',      '2004-10-22', '2025-01-15'),
(@nd_student3, 'Nghê Minh Trí',       '2004-01-26', '2025-01-15');

SELECT maHV INTO @hv_trinh FROM hocvien WHERE maND=@nd_student1;
SELECT maHV INTO @hv_luan  FROM hocvien WHERE maND=@nd_student2;
SELECT maHV INTO @hv_tri   FROM hocvien WHERE maND=@nd_student3;

COMMIT;