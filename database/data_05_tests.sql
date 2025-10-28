USE Online_Certificate_Classroom;

START TRANSACTION;

SET @R2_BASE_PUBLIC := 'https://pub-9b3a3b8712d849d7b4e15e85e6beca8d.r2.dev';
-- =========================================================
-- 10)  MINI-TEST THEO CHƯƠNG (3 bài/chương)
-- =========================================================

-- =========================
-- Band 405-600
-- =========================
-- =========================
-- 1) KHÓA SPEAKING (@kh_speaking_405_600)
-- =========================
-- Chương 1: Read a Text Aloud (@ch_sp_405_600_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_405_600, @ch_sp_405_600_1, 'Mini-test 1 – Read a Text Aloud', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_1, 'Mini-test 2 – Read a Text Aloud', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_1, 'Mini-test 3 – Read a Text Aloud', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_405_600_1_1 := LAST_INSERT_ID(); SET @mt_sp_405_600_1_2 := @mt_sp_405_600_1_1 + 1; SET @mt_sp_405_600_1_3 := @mt_sp_405_600_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_405_600_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Describe a Picture (@ch_sp_405_600_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_405_600, @ch_sp_405_600_2, 'Mini-test 1 – Describe a Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_2, 'Mini-test 2 – Describe a Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_2, 'Mini-test 3 – Describe a Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_405_600_2_1 := LAST_INSERT_ID(); SET @mt_sp_405_600_2_2 := @mt_sp_405_600_2_1 + 1; SET @mt_sp_405_600_2_3 := @mt_sp_405_600_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_405_600_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to Questions (@ch_sp_405_600_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_405_600, @ch_sp_405_600_3, 'Mini-test 1 – Respond to Questions', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_3, 'Mini-test 2 – Respond to Questions', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_3, 'Mini-test 3 – Respond to Questions', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_405_600_3_1 := LAST_INSERT_ID(); SET @mt_sp_405_600_3_2 := @mt_sp_405_600_3_1 + 1; SET @mt_sp_405_600_3_3 := @mt_sp_405_600_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_405_600_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: Respond to Questions Using Information Provided (@ch_sp_405_600_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_405_600, @ch_sp_405_600_4, 'Mini-test 1 – Respond to Questions Using Information', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_4, 'Mini-test 2 – Respond to Questions Using Information', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_405_600, @ch_sp_405_600_4, 'Mini-test 3 – Respond to Questions Using Information', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_405_600_4_1 := LAST_INSERT_ID(); SET @mt_sp_405_600_4_2 := @mt_sp_405_600_4_1 + 1; SET @mt_sp_405_600_4_3 := @mt_sp_405_600_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_405_600_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_405_600_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 2) KHÓA WRITING (@kh_writing_405_600)
-- =========================
-- Chương 1: Express an Opinion (@ch_wr_405_600_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_405_600, @ch_wr_405_600_1, 'Mini-test 1 – Express an Opinion', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_1, 'Mini-test 2 – Express an Opinion', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_1, 'Mini-test 3 – Express an Opinion', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_405_600_1_1 := LAST_INSERT_ID(); SET @mt_wr_405_600_1_2 := @mt_wr_405_600_1_1 + 1; SET @mt_wr_405_600_1_3 := @mt_wr_405_600_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_405_600_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Write a Sentence Based on a Picture (@ch_wr_405_600_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_405_600, @ch_wr_405_600_2, 'Mini-test 1 – Write a Sentence Based on a Picture', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_2, 'Mini-test 2 – Write a Sentence Based on a Picture', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_2, 'Mini-test 3 – Write a Sentence Based on a Picture', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_405_600_2_1 := LAST_INSERT_ID(); SET @mt_wr_405_600_2_2 := @mt_wr_405_600_2_1 + 1; SET @mt_wr_405_600_2_3 := @mt_wr_405_600_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_405_600_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to a Written Request (@ch_wr_405_600_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_405_600, @ch_wr_405_600_3, 'Mini-test 1 – Respond to a Written Request', 1, 10.00, 0.00, 20, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_3, 'Mini-test 2 – Respond to a Written Request', 2, 10.00, 0.00, 20, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_3, 'Mini-test 3 – Respond to a Written Request', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_wr_405_600_3_1 := LAST_INSERT_ID(); SET @mt_wr_405_600_3_2 := @mt_wr_405_600_3_1 + 1; SET @mt_wr_405_600_3_3 := @mt_wr_405_600_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_405_600_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: Write an Opinion Essay (@ch_wr_405_600_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_405_600, @ch_wr_405_600_4, 'Mini-test 1 – Write an Opinion Essay', 1, 10.00, 0.00, 30, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_4, 'Mini-test 2 – Write an Opinion Essay', 2, 10.00, 0.00, 30, 1, 1),
(@kh_writing_405_600, @ch_wr_405_600_4, 'Mini-test 3 – Write an Opinion Essay', 3, 10.00, 0.00, 30, 1, 1);
SET @mt_wr_405_600_4_1 := LAST_INSERT_ID(); SET @mt_wr_405_600_4_2 := @mt_wr_405_600_4_1 + 1; SET @mt_wr_405_600_4_3 := @mt_wr_405_600_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_405_600_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_405_600_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 3) KHÓA LISTENING (@kh_listening_405_600)
-- =========================
-- Chương 1: PART 1: Photographs (@ch_li_405_600_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_405_600, @ch_li_405_600_1, 'Mini-test 1 – Part 1 Photographs', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_1, 'Mini-test 2 – Part 1 Photographs', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_1, 'Mini-test 3 – Part 1 Photographs', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_405_600_1_1 := LAST_INSERT_ID(); SET @mt_li_405_600_1_2 := @mt_li_405_600_1_1 + 1; SET @mt_li_405_600_1_3 := @mt_li_405_600_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_405_600_1_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.1%20P1%20MT1.mp3')),

(@mt_li_405_600_1_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.2%20P1%20MT1.mp3')),

(@mt_li_405_600_1_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.3%20P1%20MT1.mp3')),

(@mt_li_405_600_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_405_600_1_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.1%20P1%20MT2.mp3')),

(@mt_li_405_600_1_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.2%20P1%20MT2.mp3')),

(@mt_li_405_600_1_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.3%20P1%20MT2.mp3')),

(@mt_li_405_600_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_405_600_1_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.1%20P1%20MT3.mp3')),

(@mt_li_405_600_1_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.2%20P1%20MT3.mp3')),

(@mt_li_405_600_1_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.3%20P1%20MT3.mp3')),

(@mt_li_405_600_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 2: Question–Response (@ch_li_405_600_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_405_600, @ch_li_405_600_2, 'Mini-test 1 – Part 2 Question-Response', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_2, 'Mini-test 2 – Part 2 Question-Response', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_2, 'Mini-test 3 – Part 2 Question-Response', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_405_600_2_1 := LAST_INSERT_ID(); SET @mt_li_405_600_2_2 := @mt_li_405_600_2_1 + 1; SET @mt_li_405_600_2_3 := @mt_li_405_600_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_405_600_2_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.1%20P2%20MT1.mp3')),

(@mt_li_405_600_2_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.2%20P2%20MT1.mp3')),

(@mt_li_405_600_2_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.3%20P2%20MT1.mp3')),

(@mt_li_405_600_2_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_2_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_405_600_2_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.1%20P2%20MT2.mp3')),

(@mt_li_405_600_2_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.2%20P2%20MT2.mp3')),

(@mt_li_405_600_2_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.3%20P2%20MT2.mp3')),

(@mt_li_405_600_2_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_2_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_405_600_2_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.1%20P2%20MT3.mp3')),

(@mt_li_405_600_2_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.2%20P2%20MT3.mp3')),

(@mt_li_405_600_2_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.3%20P2%20MT3.mp3')),

(@mt_li_405_600_2_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_2_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: PART 3: Short Conversations (@ch_li_405_600_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_405_600, @ch_li_405_600_3, 'Mini-test 1 – Part 3 Short Conversations', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_3, 'Mini-test 2 – Part 3 Short Conversations', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_3, 'Mini-test 3 – Part 3 Short Conversations', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_405_600_3_1 := LAST_INSERT_ID(); SET @mt_li_405_600_3_2 := @mt_li_405_600_3_1 + 1; SET @mt_li_405_600_3_3 := @mt_li_405_600_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_405_600_3_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.1%20P3%20MT1.mp3')),

(@mt_li_405_600_3_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.2%20P3%20MT1.mp3')),

(@mt_li_405_600_3_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.3%20P3%20MT1.mp3')),

(@mt_li_405_600_3_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_3_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_405_600_3_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.1%20P3%20MT2.mp3')),

(@mt_li_405_600_3_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.2%20P3%20MT2.mp3')),

(@mt_li_405_600_3_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.3%20P3%20MT2.mp3')),

(@mt_li_405_600_3_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_3_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_405_600_3_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.1%20P3%20MT3.mp3')),

(@mt_li_405_600_3_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.2%20P3%20MT3.mp3')),

(@mt_li_405_600_3_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.3%20P3%20MT3.mp3')),

(@mt_li_405_600_3_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_3_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: PART 4: Short Talks (@ch_li_405_600_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_405_600, @ch_li_405_600_4, 'Mini-test 1 – Part 4 Short Talks', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_4, 'Mini-test 2 – Part 4 Short Talks', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_405_600, @ch_li_405_600_4, 'Mini-test 3 – Part 4 Short Talks', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_405_600_4_1 := LAST_INSERT_ID(); SET @mt_li_405_600_4_2 := @mt_li_405_600_4_1 + 1; SET @mt_li_405_600_4_3 := @mt_li_405_600_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_405_600_4_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.1%20P4%20MT1.mp3')),

(@mt_li_405_600_4_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.2%20P4%20MT1.mp3')),

(@mt_li_405_600_4_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.3%20P4%20MT1.mp3')),

(@mt_li_405_600_4_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_4_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_405_600_4_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.1%20P4%20MT2.mp3')),

(@mt_li_405_600_4_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.2%20P4%20MT2.mp3')),

(@mt_li_405_600_4_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.3%20P4%20MT2.mp3')),

(@mt_li_405_600_4_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_4_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_405_600_4_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.1%20P4%20MT3.mp3')),

(@mt_li_405_600_4_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.2%20P4%20MT3.mp3')),

(@mt_li_405_600_4_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.3%20P4%20MT3.mp3')),

(@mt_li_405_600_4_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_405_600_4_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 4) KHÓA READING (@kh_reading_405_600)
-- =========================
-- Chương 1: PART 5–6: Incomplete Sentences (@ch_re_405_600_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_405_600, @ch_re_405_600_1, 'Mini-test 1 – Part 5–6 Incomplete Sentences', 1, 10.00, 0.00, 10, 1, 1),
(@kh_reading_405_600, @ch_re_405_600_1, 'Mini-test 2 – Part 5–6 Incomplete Sentences', 2, 10.00, 0.00, 10, 1, 1),
(@kh_reading_405_600, @ch_re_405_600_1, 'Mini-test 3 – Part 5–6 Incomplete Sentences', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_re_405_600_1_1 := LAST_INSERT_ID(); SET @mt_re_405_600_1_2 := @mt_re_405_600_1_1 + 1; SET @mt_re_405_600_1_3 := @mt_re_405_600_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_405_600_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_405_600_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_405_600_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 7: Vocabulary & Reading Comprehension (@ch_re_405_600_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_405_600, @ch_re_405_600_2, 'Mini-test 1 – Part 7 Vocabulary & Reading Comprehension Practice', 1, 10.00, 0.00, 20, 1, 1),
(@kh_reading_405_600, @ch_re_405_600_2, 'Mini-test 2 – Part 7 Vocabulary & Reading Comprehension Practice', 2, 10.00, 0.00, 20, 1, 1),
(@kh_reading_405_600, @ch_re_405_600_2, 'Mini-test 3 – Part 7 Vocabulary & Reading Comprehension Practice', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_re_405_600_2_1 := LAST_INSERT_ID(); SET @mt_re_405_600_2_2 := @mt_re_405_600_2_1 + 1; SET @mt_re_405_600_2_3 := @mt_re_405_600_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_405_600_2_1, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_2_1, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_405_600_2_2, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_2_2, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_405_600_2_3, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_405_600_2_3, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- Band 605-780
-- =========================
-- =========================
-- 1) KHÓA SPEAKING (@kh_speaking_605_780)
-- =========================
-- Chương 1: Read a Text Aloud (@ch_sp_605_780_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_605_780, @ch_sp_605_780_1, 'Mini-test 1 – Read a Text Aloud', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_1, 'Mini-test 2 – Read a Text Aloud', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_1, 'Mini-test 3 – Read a Text Aloud', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_605_780_1_1 := LAST_INSERT_ID(); SET @mt_sp_605_780_1_2 := @mt_sp_605_780_1_1 + 1; SET @mt_sp_605_780_1_3 := @mt_sp_605_780_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_605_780_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Describe a Picture (@ch_sp_605_780_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_605_780, @ch_sp_605_780_2, 'Mini-test 1 – Describe a Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_2, 'Mini-test 2 – Describe a Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_2, 'Mini-test 3 – Describe a Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_605_780_2_1 := LAST_INSERT_ID(); SET @mt_sp_605_780_2_2 := @mt_sp_605_780_2_1 + 1; SET @mt_sp_605_780_2_3 := @mt_sp_605_780_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_605_780_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to Questions (@ch_sp_605_780_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_605_780, @ch_sp_605_780_3, 'Mini-test 1 – Respond to Questions', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_3, 'Mini-test 2 – Respond to Questions', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_3, 'Mini-test 3 – Respond to Questions', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_605_780_3_1 := LAST_INSERT_ID(); SET @mt_sp_605_780_3_2 := @mt_sp_605_780_3_1 + 1; SET @mt_sp_605_780_3_3 := @mt_sp_605_780_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_605_780_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest1_N3.%20Respond%20to%20Questions.pdf')),

(@mt_sp_605_780_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest2_N3.%20Respond%20to%20Questions.pdf')),

(@mt_sp_605_780_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest3_N3.%20Respond%20to%20Questions.pdf'));

-- Chương 4: Respond to Questions Using Information Provided (@ch_sp_605_780_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_605_780, @ch_sp_605_780_4, 'Mini-test 1 – Respond to Questions Using Information', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_4, 'Mini-test 2 – Respond to Questions Using Information', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_605_780, @ch_sp_605_780_4, 'Mini-test 3 – Respond to Questions Using Information', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_605_780_4_1 := LAST_INSERT_ID(); SET @mt_sp_605_780_4_2 := @mt_sp_605_780_4_1 + 1; SET @mt_sp_605_780_4_3 := @mt_sp_605_780_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_605_780_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_605_780_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 2) KHÓA WRITING (@kh_writing_605_780)
-- =========================
-- Chương 1: Express an Opinion (@ch_wr_605_780_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_605_780, @ch_wr_605_780_1, 'Mini-test 1 – Express an Opinion', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_1, 'Mini-test 2 – Express an Opinion', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_1, 'Mini-test 3 – Express an Opinion', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_605_780_1_1 := LAST_INSERT_ID(); SET @mt_wr_605_780_1_2 := @mt_wr_605_780_1_1 + 1; SET @mt_wr_605_780_1_3 := @mt_wr_605_780_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_605_780_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Write a Sentence Based on a Picture (@ch_wr_605_780_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_605_780, @ch_wr_605_780_2, 'Mini-test 1 – Write a Sentence Based on a Picture', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_2, 'Mini-test 2 – Write a Sentence Based on a Picture', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_2, 'Mini-test 3 – Write a Sentence Based on a Picture', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_605_780_2_1 := LAST_INSERT_ID(); SET @mt_wr_605_780_2_2 := @mt_wr_605_780_2_1 + 1; SET @mt_wr_605_780_2_3 := @mt_wr_605_780_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_605_780_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to a Written Request (@ch_wr_605_780_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_605_780, @ch_wr_605_780_3, 'Mini-test 1 – Respond to a Written Request', 1, 10.00, 0.00, 20, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_3, 'Mini-test 2 – Respond to a Written Request', 2, 10.00, 0.00, 20, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_3, 'Mini-test 3 – Respond to a Written Request', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_wr_605_780_3_1 := LAST_INSERT_ID(); SET @mt_wr_605_780_3_2 := @mt_wr_605_780_3_1 + 1; SET @mt_wr_605_780_3_3 := @mt_wr_605_780_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_605_780_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: Write an Opinion Essay (@ch_wr_605_780_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_605_780, @ch_wr_605_780_4, 'Mini-test 1 – Write an Opinion Essay', 1, 10.00, 0.00, 30, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_4, 'Mini-test 2 – Write an Opinion Essay', 2, 10.00, 0.00, 30, 1, 1),
(@kh_writing_605_780, @ch_wr_605_780_4, 'Mini-test 3 – Write an Opinion Essay', 3, 10.00, 0.00, 30, 1, 1);
SET @mt_wr_605_780_4_1 := LAST_INSERT_ID(); SET @mt_wr_605_780_4_2 := @mt_wr_605_780_4_1 + 1; SET @mt_wr_605_780_4_3 := @mt_wr_605_780_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_605_780_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_605_780_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 3) KHÓA LISTENING (@kh_listening_605_780)
-- =========================
-- Chương 1: PART 1: Photographs (@ch_li_605_780_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_605_780, @ch_li_605_780_1, 'Mini-test 1 – Part 1 Photographs', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_1, 'Mini-test 2 – Part 1 Photographs', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_1, 'Mini-test 3 – Part 1 Photographs', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_605_780_1_1 := LAST_INSERT_ID(); SET @mt_li_605_780_1_2 := @mt_li_605_780_1_1 + 1; SET @mt_li_605_780_1_3 := @mt_li_605_780_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_605_780_1_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.1%20P1%20MT1.mp3')),

(@mt_li_605_780_1_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.2%20P1%20MT1.mp3')),

(@mt_li_605_780_1_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.3%20P1%20MT1.mp3')),

(@mt_li_605_780_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_605_780_1_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.1%20P1%20MT2.mp3')),

(@mt_li_605_780_1_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.2%20P1%20MT2.mp3')),

(@mt_li_605_780_1_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.3%20P1%20MT2.mp3')),

(@mt_li_605_780_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_605_780_1_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.1%20P1%20MT3.mp3')),

(@mt_li_605_780_1_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.2%20P1%20MT3.mp3')),

(@mt_li_605_780_1_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.3%20P1%20MT3.mp3')),

(@mt_li_605_780_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 2: Question–Response (@ch_li_605_780_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_605_780, @ch_li_605_780_2, 'Mini-test 1 – Part 2 Question-Response', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_2, 'Mini-test 2 – Part 2 Question-Response', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_2, 'Mini-test 3 – Part 2 Question-Response', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_605_780_2_1 := LAST_INSERT_ID(); SET @mt_li_605_780_2_2 := @mt_li_605_780_2_1 + 1; SET @mt_li_605_780_2_3 := @mt_li_605_780_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_605_780_2_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.1%20P2%20MT1.mp3')),

(@mt_li_605_780_2_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.2%20P2%20MT1.mp3')),

(@mt_li_605_780_2_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.3%20P2%20MT1.mp3')),

(@mt_li_605_780_2_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_2_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_605_780_2_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.1%20P2%20MT2.mp3')),

(@mt_li_605_780_2_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.2%20P2%20MT2.mp3')),

(@mt_li_605_780_2_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.3%20P2%20MT2.mp3')),

(@mt_li_605_780_2_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_2_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_605_780_2_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.1%20P2%20MT3.mp3')),

(@mt_li_605_780_2_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.2%20P2%20MT3.mp3')),

(@mt_li_605_780_2_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.3%20P2%20MT3.mp3')),

(@mt_li_605_780_2_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_2_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: PART 3: Short Conversations (@ch_li_605_780_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_605_780, @ch_li_605_780_3, 'Mini-test 1 – Part 3 Short Conversations', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_3, 'Mini-test 2 – Part 3 Short Conversations', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_3, 'Mini-test 3 – Part 3 Short Conversations', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_605_780_3_1 := LAST_INSERT_ID(); SET @mt_li_605_780_3_2 := @mt_li_605_780_3_1 + 1; SET @mt_li_605_780_3_3 := @mt_li_605_780_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_605_780_3_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.1%20P3%20MT1.mp3')),

(@mt_li_605_780_3_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.2%20P3%20MT1.mp3')),

(@mt_li_605_780_3_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.3%20P3%20MT1.mp3')),

(@mt_li_605_780_3_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_3_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_605_780_3_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.1%20P3%20MT2.mp3')),

(@mt_li_605_780_3_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.2%20P3%20MT2.mp3')),

(@mt_li_605_780_3_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.3%20P3%20MT2.mp3')),

(@mt_li_605_780_3_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_3_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_605_780_3_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.1%20P3%20MT3.mp3')),

(@mt_li_605_780_3_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.2%20P3%20MT3.mp3')),

(@mt_li_605_780_3_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.3%20P3%20MT3.mp3')),

(@mt_li_605_780_3_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_3_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: PART 4: Short Talks (@ch_li_605_780_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_605_780, @ch_li_605_780_4, 'Mini-test 1 – Part 4 Short Talks', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_4, 'Mini-test 2 – Part 4 Short Talks', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_605_780, @ch_li_605_780_4, 'Mini-test 3 – Part 4 Short Talks', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_605_780_4_1 := LAST_INSERT_ID(); SET @mt_li_605_780_4_2 := @mt_li_605_780_4_1 + 1; SET @mt_li_605_780_4_3 := @mt_li_605_780_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_605_780_4_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.1%20P4%20MT1.mp3')),

(@mt_li_605_780_4_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.2%20P4%20MT1.mp3')),

(@mt_li_605_780_4_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.3%20P4%20MT1.mp3')),

(@mt_li_605_780_4_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_4_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_605_780_4_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.1%20P4%20MT2.mp3')),

(@mt_li_605_780_4_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.2%20P4%20MT2.mp3')),

(@mt_li_605_780_4_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.3%20P4%20MT2.mp3')),

(@mt_li_605_780_4_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_4_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_605_780_4_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.1%20P4%20MT3.mp3')),

(@mt_li_605_780_4_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.2%20P4%20MT3.mp3')),

(@mt_li_605_780_4_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.3%20P4%20MT3.mp3')),

(@mt_li_605_780_4_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_605_780_4_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 4) KHÓA READING (@kh_reading_605_780)
-- =========================
-- Chương 1: PART 5–6: Incomplete Sentences (@ch_re_605_780_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_605_780, @ch_re_605_780_1, 'Mini-test 1 – Part 5–6 Incomplete Sentences', 1, 10.00, 0.00, 10, 1, 1),
(@kh_reading_605_780, @ch_re_605_780_1, 'Mini-test 2 – Part 5–6 Incomplete Sentences', 2, 10.00, 0.00, 10, 1, 1),
(@kh_reading_605_780, @ch_re_605_780_1, 'Mini-test 3 – Part 5–6 Incomplete Sentences', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_re_605_780_1_1 := LAST_INSERT_ID(); SET @mt_re_605_780_1_2 := @mt_re_605_780_1_1 + 1; SET @mt_re_605_780_1_3 := @mt_re_605_780_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_605_780_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_605_780_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_605_780_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 7: Vocabulary & Reading Comprehension (@ch_re_605_780_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_605_780, @ch_re_605_780_2, 'Mini-test 1 – Part 7 Vocabulary & Reading Comprehension Practice', 1, 10.00, 0.00, 20, 1, 1),
(@kh_reading_605_780, @ch_re_605_780_2, 'Mini-test 2 – Part 7 Vocabulary & Reading Comprehension Practice', 2, 10.00, 0.00, 20, 1, 1),
(@kh_reading_605_780, @ch_re_605_780_2, 'Mini-test 3 – Part 7 Vocabulary & Reading Comprehension Practice', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_re_605_780_2_1 := LAST_INSERT_ID(); SET @mt_re_605_780_2_2 := @mt_re_605_780_2_1 + 1; SET @mt_re_605_780_2_3 := @mt_re_605_780_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_605_780_2_1, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_2_1, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_605_780_2_2, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_2_2, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_605_780_2_3, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_605_780_2_3, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- Band 785-990
-- =========================
-- =========================
-- 1) KHÓA SPEAKING (@kh_speaking_785_990)
-- =========================
-- Chương 1: Read a Text Aloud (@ch_sp_785_990_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_785_990, @ch_sp_785_990_1, 'Mini-test 1 – Read a Text Aloud', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_1, 'Mini-test 2 – Read a Text Aloud', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_1, 'Mini-test 3 – Read a Text Aloud', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_785_990_1_1 := LAST_INSERT_ID(); SET @mt_sp_785_990_1_2 := @mt_sp_785_990_1_1 + 1; SET @mt_sp_785_990_1_3 := @mt_sp_785_990_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_785_990_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Describe a Picture (@ch_sp_785_990_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_785_990, @ch_sp_785_990_2, 'Mini-test 1 – Describe a Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_2, 'Mini-test 2 – Describe a Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_2, 'Mini-test 3 – Describe a Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_785_990_2_1 := LAST_INSERT_ID(); SET @mt_sp_785_990_2_2 := @mt_sp_785_990_2_1 + 1; SET @mt_sp_785_990_2_3 := @mt_sp_785_990_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_785_990_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to Questions (@ch_sp_785_990_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_785_990, @ch_sp_785_990_3, 'Mini-test 1 – Respond to Questions', 1, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_3, 'Mini-test 2 – Respond to Questions', 2, 10.00, 0.00, 10, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_3, 'Mini-test 3 – Respond to Questions', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_sp_785_990_3_1 := LAST_INSERT_ID(); SET @mt_sp_785_990_3_2 := @mt_sp_785_990_3_1 + 1; SET @mt_sp_785_990_3_3 := @mt_sp_785_990_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_785_990_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: Respond to Questions Using Information Provided (@ch_sp_785_990_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_speaking_785_990, @ch_sp_785_990_4, 'Mini-test 1 – Respond to Questions Using Information', 1, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_4, 'Mini-test 2 – Respond to Questions Using Information', 2, 10.00, 0.00, 12, 1, 1),
(@kh_speaking_785_990, @ch_sp_785_990_4, 'Mini-test 3 – Respond to Questions Using Information', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_sp_785_990_4_1 := LAST_INSERT_ID(); SET @mt_sp_785_990_4_2 := @mt_sp_785_990_4_1 + 1; SET @mt_sp_785_990_4_3 := @mt_sp_785_990_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_sp_785_990_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_sp_785_990_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 2) KHÓA WRITING (@kh_writing_785_990)
-- =========================
-- Chương 1: Express an Opinion (@ch_wr_785_990_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_785_990, @ch_wr_785_990_1, 'Mini-test 1 – Express an Opinion', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_1, 'Mini-test 2 – Express an Opinion', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_1, 'Mini-test 3 – Express an Opinion', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_785_990_1_1 := LAST_INSERT_ID(); SET @mt_wr_785_990_1_2 := @mt_wr_785_990_1_1 + 1; SET @mt_wr_785_990_1_3 := @mt_wr_785_990_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_785_990_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: Write a Sentence Based on a Picture (@ch_wr_785_990_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_785_990, @ch_wr_785_990_2, 'Mini-test 1 – Write a Sentence Based on a Picture', 1, 10.00, 0.00, 15, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_2, 'Mini-test 2 – Write a Sentence Based on a Picture', 2, 10.00, 0.00, 15, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_2, 'Mini-test 3 – Write a Sentence Based on a Picture', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_wr_785_990_2_1 := LAST_INSERT_ID(); SET @mt_wr_785_990_2_2 := @mt_wr_785_990_2_1 + 1; SET @mt_wr_785_990_2_3 := @mt_wr_785_990_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_785_990_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: Respond to a Written Request (@ch_wr_785_990_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_785_990, @ch_wr_785_990_3, 'Mini-test 1 – Respond to a Written Request', 1, 10.00, 0.00, 20, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_3, 'Mini-test 2 – Respond to a Written Request', 2, 10.00, 0.00, 20, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_3, 'Mini-test 3 – Respond to a Written Request', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_wr_785_990_3_1 := LAST_INSERT_ID(); SET @mt_wr_785_990_3_2 := @mt_wr_785_990_3_1 + 1; SET @mt_wr_785_990_3_3 := @mt_wr_785_990_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_785_990_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: Write an Opinion Essay (@ch_wr_785_990_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_writing_785_990, @ch_wr_785_990_4, 'Mini-test 1 – Write an Opinion Essay', 1, 10.00, 0.00, 30, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_4, 'Mini-test 2 – Write an Opinion Essay', 2, 10.00, 0.00, 30, 1, 1),
(@kh_writing_785_990, @ch_wr_785_990_4, 'Mini-test 3 – Write an Opinion Essay', 3, 10.00, 0.00, 30, 1, 1);
SET @mt_wr_785_990_4_1 := LAST_INSERT_ID(); SET @mt_wr_785_990_4_2 := @mt_wr_785_990_4_1 + 1; SET @mt_wr_785_990_4_3 := @mt_wr_785_990_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_wr_785_990_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_wr_785_990_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 3) KHÓA LISTENING (@kh_listening_785_990)
-- =========================
-- Chương 1: PART 1: Photographs (@ch_li_785_990_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_785_990, @ch_li_785_990_1, 'Mini-test 1 – Part 1 Photographs', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_1, 'Mini-test 2 – Part 1 Photographs', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_1, 'Mini-test 3 – Part 1 Photographs', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_785_990_1_1 := LAST_INSERT_ID(); SET @mt_li_785_990_1_2 := @mt_li_785_990_1_1 + 1; SET @mt_li_785_990_1_3 := @mt_li_785_990_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_785_990_1_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.1%20P1%20MT1.mp3')),

(@mt_li_785_990_1_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.2%20P1%20MT1.mp3')),

(@mt_li_785_990_1_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/mp3.3%20P1%20MT1.mp3')),

(@mt_li_785_990_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_785_990_1_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.1%20P1%20MT2.mp3')),

(@mt_li_785_990_1_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.2%20P1%20MT2.mp3')),

(@mt_li_785_990_1_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/mp3.3%20P1%20MT2.mp3')),

(@mt_li_785_990_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_785_990_1_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.1%20P1%20MT3.mp3')),

(@mt_li_785_990_1_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.2%20P1%20MT3.mp3')),

(@mt_li_785_990_1_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/mp3.3%20P1%20MT3.mp3')),

(@mt_li_785_990_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 2: Question–Response (@ch_li_785_990_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_785_990, @ch_li_785_990_2, 'Mini-test 1 – Part 2 Question-Response', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_2, 'Mini-test 2 – Part 2 Question-Response', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_2, 'Mini-test 3 – Part 2 Question-Response', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_785_990_2_1 := LAST_INSERT_ID(); SET @mt_li_785_990_2_2 := @mt_li_785_990_2_1 + 1; SET @mt_li_785_990_2_3 := @mt_li_785_990_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_785_990_2_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.1%20P2%20MT1.mp3')),

(@mt_li_785_990_2_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.2%20P2%20MT1.mp3')),

(@mt_li_785_990_2_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test1/mp3.3%20P2%20MT1.mp3')),

(@mt_li_785_990_2_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_2_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_785_990_2_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.1%20P2%20MT2.mp3')),

(@mt_li_785_990_2_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.2%20P2%20MT2.mp3')),

(@mt_li_785_990_2_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test2/mp3.3%20P2%20MT2.mp3')),

(@mt_li_785_990_2_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_2_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_785_990_2_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.1%20P2%20MT3.mp3')),

(@mt_li_785_990_2_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.2%20P2%20MT3.mp3')),

(@mt_li_785_990_2_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/MiniTest/Test3/mp3.3%20P2%20MT3.mp3')),

(@mt_li_785_990_2_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_2_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 3: PART 3: Short Conversations (@ch_li_785_990_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_785_990, @ch_li_785_990_3, 'Mini-test 1 – Part 3 Short Conversations', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_3, 'Mini-test 2 – Part 3 Short Conversations', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_3, 'Mini-test 3 – Part 3 Short Conversations', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_785_990_3_1 := LAST_INSERT_ID(); SET @mt_li_785_990_3_2 := @mt_li_785_990_3_1 + 1; SET @mt_li_785_990_3_3 := @mt_li_785_990_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_785_990_3_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.1%20P3%20MT1.mp3')),

(@mt_li_785_990_3_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.2%20P3%20MT1.mp3')),

(@mt_li_785_990_3_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/mp3.3%20P3%20MT1.mp3')),

(@mt_li_785_990_3_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_3_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_785_990_3_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.1%20P3%20MT2.mp3')),

(@mt_li_785_990_3_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.2%20P3%20MT2.mp3')),

(@mt_li_785_990_3_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/mp3.3%20P3%20MT2.mp3')),

(@mt_li_785_990_3_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_3_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_785_990_3_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.1%20P3%20MT3.mp3')),

(@mt_li_785_990_3_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.2%20P3%20MT3.mp3')),

(@mt_li_785_990_3_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/mp3.3%20P3%20MT3.mp3')),

(@mt_li_785_990_3_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_3_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 4: PART 4: Short Talks (@ch_li_785_990_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_listening_785_990, @ch_li_785_990_4, 'Mini-test 1 – Part 4 Short Talks', 1, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_4, 'Mini-test 2 – Part 4 Short Talks', 2, 10.00, 0.00, 10, 1, 1),
(@kh_listening_785_990, @ch_li_785_990_4, 'Mini-test 3 – Part 4 Short Talks', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_li_785_990_4_1 := LAST_INSERT_ID(); SET @mt_li_785_990_4_2 := @mt_li_785_990_4_1 + 1; SET @mt_li_785_990_4_3 := @mt_li_785_990_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_li_785_990_4_1, 'MiniTest 1 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.1%20P4%20MT1.mp3')),

(@mt_li_785_990_4_1, 'MiniTest 1 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.2%20P4%20MT1.mp3')),

(@mt_li_785_990_4_1, 'MiniTest 1 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/mp3.3%20P4%20MT1.mp3')),

(@mt_li_785_990_4_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_4_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_li_785_990_4_2, 'MiniTest 2 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.1%20P4%20MT2.mp3')),

(@mt_li_785_990_4_2, 'MiniTest 2 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.2%20P4%20MT2.mp3')),

(@mt_li_785_990_4_2, 'MiniTest 2 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/mp3.3%20P4%20MT2.mp3')),

(@mt_li_785_990_4_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_4_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_li_785_990_4_3, 'MiniTest 3 - Audio 1', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.1%20P4%20MT3.mp3')),

(@mt_li_785_990_4_3, 'MiniTest 3 - Audio 2', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.2%20P4%20MT3.mp3')),

(@mt_li_785_990_4_3, 'MiniTest 3 - Audio 3', 'MP3', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/mp3.3%20P4%20MT3.mp3')),

(@mt_li_785_990_4_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_li_785_990_4_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- =========================
-- 4) KHÓA READING (@kh_reading_785_990)
-- =========================
-- Chương 1: PART 5–6: Incomplete Sentences (@ch_re_785_990_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_785_990, @ch_re_785_990_1, 'Mini-test 1 – Part 5–6 Incomplete Sentences', 1, 10.00, 0.00, 10, 1, 1),
(@kh_reading_785_990, @ch_re_785_990_1, 'Mini-test 2 – Part 5–6 Incomplete Sentences', 2, 10.00, 0.00, 10, 1, 1),
(@kh_reading_785_990, @ch_re_785_990_1, 'Mini-test 3 – Part 5–6 Incomplete Sentences', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_re_785_990_1_1 := LAST_INSERT_ID(); SET @mt_re_785_990_1_2 := @mt_re_785_990_1_1 + 1; SET @mt_re_785_990_1_3 := @mt_re_785_990_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_785_990_1_1, 'MiniTest 1 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_1_1, 'MiniTest 1 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_785_990_1_2, 'MiniTest 2 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_1_2, 'MiniTest 2 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_785_990_1_3, 'MiniTest 3 - Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_1_3, 'MiniTest 3 - Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 2: PART 7: Vocabulary & Reading Comprehension (@ch_re_785_990_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_reading_785_990, @ch_re_785_990_2, 'Mini-test 1 – Part 7 Vocabulary & Reading Comprehension Practice', 1, 10.00, 0.00, 20, 1, 1),
(@kh_reading_785_990, @ch_re_785_990_2, 'Mini-test 2 – Part 7 Vocabulary & Reading Comprehension Practice', 2, 10.00, 0.00, 20, 1, 1),
(@kh_reading_785_990, @ch_re_785_990_2, 'Mini-test 3 – Part 7 Vocabulary & Reading Comprehension Practice', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_re_785_990_2_1 := LAST_INSERT_ID(); SET @mt_re_785_990_2_2 := @mt_re_785_990_2_1 + 1; SET @mt_re_785_990_2_3 := @mt_re_785_990_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_re_785_990_2_1, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_2_1, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_re_785_990_2_2, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_2_2, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_re_785_990_2_3, 'Đề',    'PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_re_785_990_2_3, 'Đáp án','PDF', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

COMMIT;