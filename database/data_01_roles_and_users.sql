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

-- Thêm học viên mới
INSERT INTO nguoidung (hoTen, email, sdt, matKhau, chuyenMon, vaiTro) VALUES
('Lưu Hoàng Anh',   'anh.lhu@example.com',   '0912000111', '123456', NULL, 'HOC_VIEN'),
('Phan Trà My',     'my.phan@example.com',   '0912000222', '123456', NULL, 'HOC_VIEN'),
('Vũ Minh Châu',    'chau.vu@example.com',   '0912000333', '123456', NULL, 'HOC_VIEN'),
('Nguyễn Gia Huy',  'huy.nguyen@example.com','0912000444', '123456', NULL, 'HOC_VIEN'),
('Trần Quốc Bảo',   'bao.tran@example.com',  '0912000555', '123456', NULL, 'HOC_VIEN')
ON DUPLICATE KEY UPDATE hoTen=VALUES(hoTen), sdt=VALUES(sdt), vaiTro=VALUES(vaiTro), updated_at=CURRENT_TIMESTAMP;

SELECT maND INTO @nd_student4 FROM nguoidung WHERE email='anh.lhu@example.com';
SELECT maND INTO @nd_student5 FROM nguoidung WHERE email='my.phan@example.com';
SELECT maND INTO @nd_student6 FROM nguoidung WHERE email='chau.vu@example.com';
SELECT maND INTO @nd_student7 FROM nguoidung WHERE email='huy.nguyen@example.com';
SELECT maND INTO @nd_student8 FROM nguoidung WHERE email='bao.tran@example.com';

INSERT IGNORE INTO quyen_nguoidung (maND, maQuyen) VALUES
(@nd_student4, 'Q003'),
(@nd_student5, 'Q003'),
(@nd_student6, 'Q003'),
(@nd_student7, 'Q003'),
(@nd_student8, 'Q003');

INSERT INTO hocvien (maND, hoTen, ngaySinh, ngayNhapHoc) VALUES
(@nd_student4, 'Lưu Hoàng Anh',  '2004-03-18', '2025-02-01'),
(@nd_student5, 'Phan Trà My',    '2004-07-22', '2025-02-01'),
(@nd_student6, 'Vũ Minh Châu',   '2004-11-05', '2025-02-05'),
(@nd_student7, 'Nguyễn Gia Huy', '2004-09-12', '2025-02-05'),
(@nd_student8, 'Trần Quốc Bảo',  '2004-12-30', '2025-02-10')
ON DUPLICATE KEY UPDATE hoTen=VALUES(hoTen), ngaySinh=VALUES(ngaySinh), ngayNhapHoc=VALUES(ngayNhapHoc), updated_at=CURRENT_TIMESTAMP;

SELECT maHV INTO @hv_anh  FROM hocvien WHERE maND=@nd_student4;
SELECT maHV INTO @hv_my   FROM hocvien WHERE maND=@nd_student5;
SELECT maHV INTO @hv_chau FROM hocvien WHERE maND=@nd_student6;
SELECT maHV INTO @hv_huy  FROM hocvien WHERE maND=@nd_student7;
SELECT maHV INTO @hv_bao  FROM hocvien WHERE maND=@nd_student8;

-- Bo sung giang vien moi
INSERT INTO nguoidung (hoTen, email, sdt, matKhau, chuyenMon, vaiTro) VALUES
('Le Hoang Phuc', 'phuc.le@example.com', '0912000666', '123456', 'IELTS/TOEIC', 'GIANG_VIEN')
ON DUPLICATE KEY UPDATE hoTen=VALUES(hoTen), sdt=VALUES(sdt), chuyenMon=VALUES(chuyenMon), vaiTro=VALUES(vaiTro), updated_at=CURRENT_TIMESTAMP;

SELECT maND INTO @nd_teacher3 FROM nguoidung WHERE email='phuc.le@example.com';
INSERT IGNORE INTO quyen_nguoidung (maND, maQuyen) VALUES (@nd_teacher3, 'Q002');

COMMIT;
