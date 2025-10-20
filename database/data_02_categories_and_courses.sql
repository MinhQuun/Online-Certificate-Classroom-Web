USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 5) DANH MỤC
-- =========================================================
INSERT INTO DANHMUC (tenDanhMuc, slug, icon) VALUES
('Chứng chỉ TOEIC', 'chung-chi-toeic', NULL)
ON DUPLICATE KEY UPDATE tenDanhMuc=VALUES(tenDanhMuc);

SELECT maDanhMuc INTO @dm_toeic FROM DANHMUC WHERE slug='chung-chi-toeic';

-- =========================================================
-- 6) 4 KHÓA HỌC TOEIC
-- =========================================================
-- 6.1 SPEAKING
INSERT INTO KHOAHOC
(maDanhMuc, maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,3,
    'Luyện thi TOEIC Speaking',
    'luyen-thi-toeic-speaking',
    950000,
    'Khóa luyện Speaking TOEIC: tập trung phát âm chuẩn, kỹ năng trả lời các dạng câu hỏi Speaking, có mini-test theo chương và thi cuối khóa.',
    '2025-01-01', '2025-12-31', 'toeic_speaking.png', 365, 'PUBLISHED');
SET @kh_speaking := LAST_INSERT_ID();

-- 6.2 WRITING
INSERT INTO KHOAHOC
(maDanhMuc,maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,2,
    'Luyện thi TOEIC Writing',
    'luyen-thi-toeic-writing',
    900000,
    'Khóa luyện Writing TOEIC: kỹ năng viết email, bài luận, triển khai ý mạch lạc, có mini-test theo chương và thi cuối khóa.',
    '2025-01-01', '2025-12-31', 'toeic_writing.png', 365, 'PUBLISHED');
SET @kh_writing := LAST_INSERT_ID();

-- 6.3 LISTENING
INSERT INTO KHOAHOC
(maDanhMuc,maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,3,
    'Luyện thi TOEIC Listening cấp tốc mục tiêu 700-850+',
    'luyen-thi-toeic-listening-cap-toc-700-850',
    1200000,
    'Khóa cấp tốc Listening TOEIC: chiến lược Part 1–4, bài luyện nghe cường độ cao, mini-test & mock test.',
    '2025-01-01', '2025-12-31', 'toeic_listening.png', 365, 'PUBLISHED');
SET @kh_listening := LAST_INSERT_ID();

-- 6.4 READING
INSERT INTO KHOAHOC
(maDanhMuc,maND, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,2,
    'Luyện thi TOEIC Reading cấp tốc mục tiêu 700-850+',
    'luyen-thi-toeic-reading-cap-toc-700-850',
    1150000,
    'Khóa cấp tốc Reading TOEIC: chiến lược Part 5–7, bài luyện đọc cường độ cao, mini-test & mock test.',
    '2025-01-01', '2025-12-31', 'toeic_reading.png', 365, 'PUBLISHED');
SET @kh_reading := LAST_INSERT_ID();

COMMIT;