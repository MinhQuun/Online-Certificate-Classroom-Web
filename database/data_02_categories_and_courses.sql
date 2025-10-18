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
-- 6) 2 KHÓA HỌC TOEIC
-- =========================================================
-- 6.1 Nói - Viết
INSERT INTO KHOAHOC
(maDanhMuc, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,
    'Luyện thi TOEIC (Nói - Viết)',
    'luyen-thi-toeic-noi-viet',
    1850000,
    'Khóa luyện Speaking & Writing TOEIC: phát âm, triển khai ý, email & bài luận; có mini-test theo chương và thi cuối khóa.',
    '2025-01-01', '2025-12-31', 'toeic_noi-viet.png', 365, 'PUBLISHED');
SET @kh_noiviet := LAST_INSERT_ID();

-- 6.2 Nghe - Đọc 700–850+
INSERT INTO KHOAHOC
(maDanhMuc, tenKH, slug, hocPhi, moTa, ngayBatDau, ngayKetThuc, hinhanh, thoiHanNgay, trangThai)
VALUES
(@dm_toeic,
    'Luyện thi TOEIC (Nghe - Đọc) cấp tốc mục tiêu 700-850+',
    'luyen-thi-toeic-nghe-doc-cap-toc-700-850',
    2350000,
    'Khóa cấp tốc Listening & Reading: chiến lược Part 1–7, bài luyện cường độ cao, mini-test & mock test.',
    '2025-01-01', '2025-12-31', 'toeic_nghe-doc.png', 365, 'PUBLISHED');
SET @kh_nghedoc := LAST_INSERT_ID();

COMMIT;