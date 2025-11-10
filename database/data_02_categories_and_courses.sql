USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================
-- 5) DANH MỤC THEO BAND
-- =========================
INSERT INTO danhmuc (tenDanhMuc, slug, icon) VALUES
('TOEIC Foundation (405-600)', 'toeic-405-600', NULL),
('TOEIC Intermediate (605-780)', 'toeic-605-780', NULL),
('TOEIC Advanced (785-990)', 'toeic-785-990', NULL)
ON DUPLICATE KEY UPDATE tenDanhMuc=VALUES(tenDanhMuc);

SELECT maDanhMuc INTO @dm_405_600 FROM danhmuc WHERE slug='toeic-405-600';
SELECT maDanhMuc INTO @dm_605_780 FROM danhmuc WHERE slug='toeic-605-780';
SELECT maDanhMuc INTO @dm_785_990 FROM danhmuc WHERE slug='toeic-785-990';

-- ====================================================
-- 6) KHÓA HỌC THEO BAND
-- ====================================================

-- =========================
-- Band 405-600
-- =========================
-- 1. SPEAKING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_405_600, 3,
    'Luyện TOEIC Speaking 405-600',
    'luyen-thi-toeic-speaking-405-600',
    950000,
    'Khóa luyện Speaking TOEIC: tập trung phát âm chuẩn, kỹ năng trả lời các dạng câu hỏi Speaking, có bài tập ôn luyện theo chương.',
    '2025-01-01','2025-12-31','toeic-speaking-405-600.png',365,'PUBLISHED');
SET @kh_speaking_405_600 := LAST_INSERT_ID();

-- 2. WRITING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_405_600, 2,
    'Luyện TOEIC Writing 405-600',
    'luyen-thi-toeic-writing-405-600',
    900000,
    'Khóa luyện Writing TOEIC: kỹ năng viết email, bài luận, triển khai ý mạch lạc, có bài tập ôn luyện theo chương.',
    '2025-01-01','2025-12-31','toeic-writing-405-600.png',365,'PUBLISHED');
SET @kh_writing_405_600 := LAST_INSERT_ID();

-- 3. LISTENING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_405_600, 3,
    'Luyện TOEIC Listening 405-600',
    'luyen-thi-toeic-listening-405-600',
    1200000,
    'Khóa cấp tốc Listening TOEIC: chiến lược Part 1–4, bài luyện nghe cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-listening-405-600.png',365,'PUBLISHED');
SET @kh_listening_405_600 := LAST_INSERT_ID();

-- 4. READING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_405_600, 2,
    'Luyện TOEIC Reading 405-600',
    'luyen-thi-toeic-reading-405-600',
    1150000,
    'Khóa cấp tốc Reading TOEIC: chiến lược Part 5–7, bài luyện đọc cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-reading-405-600.png',365,'PUBLISHED');
SET @kh_reading_405_600 := LAST_INSERT_ID();

-- =========================
-- Band 605-780
-- =========================
-- 1. SPEAKING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_605_780, 3,
    'Luyện TOEIC Speaking 605-780',
    'luyen-thi-toeic-speaking-605-780',
    1150000,
    'Khóa luyện Speaking TOEIC: tập trung phát âm chuẩn, kỹ năng trả lời các dạng câu hỏi Speaking, có bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-speaking-605-780.png',365,'PUBLISHED');
SET @kh_speaking_605_780 := LAST_INSERT_ID();

-- 2. WRITING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_605_780, 2,
    'Luyện TOEIC Writing 605-780',
    'luyen-thi-toeic-writing-605-780',
    1100000,
    'Khóa luyện Writing TOEIC: kỹ năng viết email, bài luận, triển khai ý mạch lạc, có bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-writing-605-780.png',365,'PUBLISHED');
SET @kh_writing_605_780 := LAST_INSERT_ID();

-- 3. LISTENING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_605_780, 3,
    'Luyện TOEIC Listening 605-780',
    'luyen-thi-toeic-listening-605-780',
    1400000,
    'Khóa cấp tốc Listening TOEIC: chiến lược Part 1–4, bài luyện nghe cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-listening-605-780.png',365,'PUBLISHED');
SET @kh_listening_605_780 := LAST_INSERT_ID();

-- 4. READING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_605_780, 2,
    'Luyện TOEIC Reading 605-780',
    'luyen-thi-toeic-reading-605-780',
    1350000,
    'Khóa cấp tốc Reading TOEIC: chiến lược Part 5–7, bài luyện đọc cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-reading-605-780.png',365,'PUBLISHED');
SET @kh_reading_605_780 := LAST_INSERT_ID();

-- =========================
-- Band 785-990
-- =========================
-- 1. SPEAKING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_785_990, 3,
    'Luyện TOEIC Speaking 785-990',
    'luyen-thi-toeic-speaking-785-990',
    1350000,
    'Khóa luyện Speaking TOEIC: tập trung phát âm chuẩn, kỹ năng trả lời các dạng câu hỏi Speaking, có bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-speaking-785-990.png',365,'PUBLISHED');
SET @kh_speaking_785_990 := LAST_INSERT_ID();

-- 2. WRITING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_785_990, 2,
    'Luyện TOEIC Writing 785-990',
    'luyen-thi-toeic-writing-785-990',
    1300000,
    'Khóa luyện Writing TOEIC: kỹ năng viết email, bài luận, triển khai ý mạch lạc, có bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-writing-785-990.png',365,'PUBLISHED');
SET @kh_writing_785_990 := LAST_INSERT_ID();

-- 3. LISTENING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_785_990, 3,
    'Luyện TOEIC Listening 785-990',
    'luyen-thi-toeic-listening-785-990',
    1600000,
    'Khóa cấp tốc Listening TOEIC: chiến lược Part 1–4, bài luyện nghe cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-listening-785-990.png',365,'PUBLISHED');
SET @kh_listening_785_990 := LAST_INSERT_ID();

-- 4. READING
INSERT INTO khoahoc
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai) VALUES
(@dm_785_990, 2,
    'Luyện TOEIC Reading 785-990',
    'luyen-thi-toeic-reading-785-990',
    1550000,
    'Khóa cấp tốc Reading TOEIC: chiến lược Part 5–7, bài luyện đọc cường độ cao, bài tập ôn luyện theo format chuẩn.',
    '2025-01-01','2025-12-31','toeic-reading-785-990.png',365,'PUBLISHED');
    SET @kh_reading_785_990 := LAST_INSERT_ID();

-- Lấy ID của các khóa học từ band 405-600 (giả định dữ liệu đã tồn tại từ script trước)
SELECT maKH INTO @kh_speaking_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-speaking-405-600';
SELECT maKH INTO @kh_writing_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-writing-405-600';
SELECT maKH INTO @kh_listening_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-listening-405-600';
SELECT maKH INTO @kh_reading_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-reading-405-600';

-- Tạo combo cho band Foundation (405-600)
INSERT INTO goi_khoa_hoc (tenGoi, slug, moTa, gia, giaGoc, hinhanh, ngayBatDau, ngayKetThuc, trangThai, created_by)
VALUES (
    'TOEIC Foundation Full Pack (405-600)',
    'toeic-foundation-full-pack-405-600',
    'Gói combo đầy đủ 4 kỹ năng TOEIC cho band 405-600: Speaking, Writing, Listening, Reading. Giá ưu đãi đặc biệt!',
    3000000.00,  -- Giá bán ưu đãi
    4200000.00,  -- Giá gốc (tổng học phí các khóa)
    'toeic-foundation-pack.png',
    '2025-11-01',
    '2025-12-31',
    'PUBLISHED',
    1  -- Giả sử maND của Admin là 1
);

SET @maGoi = LAST_INSERT_ID();

-- Liên kết các khóa học vào combo
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_speaking_405_600, 1);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_writing_405_600, 2);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_listening_405_600, 3);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_reading_405_600, 4);

-- =========================
-- Tạo combo cho band Intermediate (605-780)
-- =========================
-- Lấy ID của các khóa học từ band 605-780
SELECT maKH INTO @kh_speaking_605_780 FROM khoahoc WHERE slug = 'luyen-thi-toeic-speaking-605-780';
SELECT maKH INTO @kh_writing_605_780 FROM khoahoc WHERE slug = 'luyen-thi-toeic-writing-605-780';
SELECT maKH INTO @kh_listening_605_780 FROM khoahoc WHERE slug = 'luyen-thi-toeic-listening-605-780';
SELECT maKH INTO @kh_reading_605_780 FROM khoahoc WHERE slug = 'luyen-thi-toeic-reading-605-780';

INSERT INTO goi_khoa_hoc (tenGoi, slug, moTa, gia, giaGoc, hinhanh, ngayBatDau, ngayKetThuc, trangThai, created_by)
VALUES (
    'TOEIC Intermediate Full Pack (605-780)',
    'toeic-intermediate-full-pack-605-780',
    'Gói combo đầy đủ 4 kỹ năng TOEIC cho band 605-780: Speaking, Writing, Listening, Reading. Nâng cao trình độ trung cấp!',
    3600000.00,  -- Giá bán ưu đãi (giảm ~14% so với mua lẻ)
    5000000.00,  -- Giá gốc: 1,150,000 + 1,100,000 + 1,400,000 + 1,350,000 = 5,000,000
    'toeic-intermediate-pack.png',
    '2025-11-01',
    '2025-12-31',
    'PUBLISHED',
    1
);

SET @maGoi = LAST_INSERT_ID();

-- Liên kết các khóa học vào combo Intermediate
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_speaking_605_780, 1);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_writing_605_780, 2);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_listening_605_780, 3);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_reading_605_780, 4);

-- =========================
-- Tạo combo cho band Advanced (785-990)
-- =========================
-- Lấy ID của các khóa học từ band 785-990
SELECT maKH INTO @kh_speaking_785_990 FROM khoahoc WHERE slug = 'luyen-thi-toeic-speaking-785-990';
SELECT maKH INTO @kh_writing_785_990 FROM khoahoc WHERE slug = 'luyen-thi-toeic-writing-785-990';
SELECT maKH INTO @kh_listening_785_990 FROM khoahoc WHERE slug = 'luyen-thi-toeic-listening-785-990';
SELECT maKH INTO @kh_reading_785_990 FROM khoahoc WHERE slug = 'luyen-thi-toeic-reading-785-990';

INSERT INTO goi_khoa_hoc (tenGoi, slug, moTa, gia, giaGoc, hinhanh, ngayBatDau, ngayKetThuc, trangThai, created_by)
VALUES (
    'TOEIC Advanced Full Pack (785-990)',
    'toeic-advanced-full-pack-785-990',
    'Gói combo đầy đủ 4 kỹ năng TOEIC cho band 785-990: Speaking, Writing, Listening, Reading. Chinh phục điểm cao tuyệt đối!',
    4200000.00,  -- Giá bán ưu đãi (giảm ~17% so với mua lẻ)
    5800000.00,  -- Giá gốc: 1,350,000 + 1,300,000 + 1,600,000 + 1,550,000 = 5,800,000
    'toeic-advanced-pack.png',
    '2025-11-01',
    '2025-12-31',
    'PUBLISHED',
    1
);

SET @maGoi = LAST_INSERT_ID();

-- Liên kết các khóa học vào combo Advanced
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_speaking_785_990, 1);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_writing_785_990, 2);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_listening_785_990, 3);
INSERT INTO goi_khoa_hoc_chitiet (maGoi, maKH, thuTu) VALUES (@maGoi, @kh_reading_785_990, 4);

COMMIT;
