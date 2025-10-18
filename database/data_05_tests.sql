USE Online_Certificate_Classroom;

START TRANSACTION;

SET @R2_BASE_PUBLIC := 'https://pub-9b3a3b8712d849d7b4e15e85e6beca8d.r2.dev';
-- =========================================================
-- 10)  MINI-TEST THEO CHƯƠNG (3 bài/chương)
-- =========================================================

-- =========================
-- 1) KHÓA NÓI - VIẾT (@kh_noiviet)
-- =========================
-- Chương 1: Read a Text Aloud (@ch_nv_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_1, 'Mini-test 1 – Read a Text Aloud', 1, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_1, 'Mini-test 2 – Read a Text Aloud', 2, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_1, 'Mini-test 3 – Read a Text Aloud', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_nv_1_1 := LAST_INSERT_ID(); SET @mt_nv_1_2 := @mt_nv_1_1 + 1; SET @mt_nv_1_3 := @mt_nv_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_1_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/MiniTest/MiniTest1_N1.%20Read%20a%20Text%20Aloud.pdf')),

(@mt_nv_1_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/MiniTest/MiniTest2_N1.%20Read%20a%20Text%20Aloud.pdf')),

(@mt_nv_1_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/MiniTest/MiniTest3_N1.%20Read%20a%20Text%20Aloud.pdf'));

-- Chương 2: Describe a Picture (@ch_nv_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_2, 'Mini-test 1 – Describe a Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_2, 'Mini-test 2 – Describe a Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_2, 'Mini-test 3 – Describe a Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nv_2_1 := LAST_INSERT_ID(); SET @mt_nv_2_2 := @mt_nv_2_1 + 1; SET @mt_nv_2_3 := @mt_nv_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_2_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/MiniTest/MiniTest1_N2.%20Describe%20a%20Picture.pdf')),

(@mt_nv_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/MiniTest/MiniTest2_N2.%20Describe%20a%20Picture.pdf')),

(@mt_nv_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/MiniTest/MiniTest3_N2.%20Describe%20a%20Picture.pdf'));

-- Chương 3: Respond to Questions (@ch_nv_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_3, 'Mini-test 1 – Respond to Questions', 1, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_3, 'Mini-test 2 – Respond to Questions', 2, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_3, 'Mini-test 3 – Respond to Questions', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_nv_3_1 := LAST_INSERT_ID(); SET @mt_nv_3_2 := @mt_nv_3_1 + 1; SET @mt_nv_3_3 := @mt_nv_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest1_N3.%20Respond%20to%20Questions.pdf')),

(@mt_nv_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest2_N3.%20Respond%20to%20Questions.pdf')),

(@mt_nv_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/MiniTest/MiniTest3_N3.%20Respond%20to%20Questions.pdf'));

-- Chương 4: Respond to Questions Using Information Provided (@ch_nv_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_4, 'Mini-test 1 – Respond to Questions Using Information', 1, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_4, 'Mini-test 2 – Respond to Questions Using Information', 2, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_4, 'Mini-test 3 – Respond to Questions Using Information', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nv_4_1 := LAST_INSERT_ID(); SET @mt_nv_4_2 := @mt_nv_4_1 + 1; SET @mt_nv_4_3 := @mt_nv_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/MiniTest/MiniTest1_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@mt_nv_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/MiniTest/MiniTest2_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@mt_nv_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/MiniTest/MiniTest3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- Chương 5: Express an Opinion (@ch_nv_5)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_5, 'Mini-test 1 – Express an Opinion', 1, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_5, 'Mini-test 2 – Express an Opinion', 2, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_5, 'Mini-test 3 – Express an Opinion', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_nv_5_1 := LAST_INSERT_ID(); SET @mt_nv_5_2 := @mt_nv_5_1 + 1; SET @mt_nv_5_3 := @mt_nv_5_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_5_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/MiniTest/MiniTest1_N5.%20Express%20an%20Opinion.pdf')),

(@mt_nv_5_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/MiniTest/MiniTest2_N5.%20Express%20an%20Opinion.pdf')),

(@mt_nv_5_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/MiniTest/MiniTest3_N5.%20Express%20an%20Opinion.pdf'));

-- Chương 6: Write a Sentence Based on a Picture (@ch_nv_6)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_6, 'Mini-test 1 – Sentence from Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_6, 'Mini-test 2 – Sentence from Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_6, 'Mini-test 3 – Sentence from Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nv_6_1 := LAST_INSERT_ID(); SET @mt_nv_6_2 := @mt_nv_6_1 + 1; SET @mt_nv_6_3 := @mt_nv_6_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_6_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/MiniTest/MiniTest1_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf')),

(@mt_nv_6_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/MiniTest/MiniTest2_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf')),

(@mt_nv_6_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/MiniTest/MiniTest3_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf'));

-- Chương 7: Respond to a Written Request (@ch_nv_7)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_7, 'Mini-test 1 – Written Request', 1, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_7, 'Mini-test 2 – Written Request', 2, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_7, 'Mini-test 3 – Written Request', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_nv_7_1 := LAST_INSERT_ID(); SET @mt_nv_7_2 := @mt_nv_7_1 + 1; SET @mt_nv_7_3 := @mt_nv_7_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_7_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/MiniTest/MiniTest1_V2.%20Respond%20to%20a%20Written%20Resquest.pdf')),

(@mt_nv_7_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/MiniTest/MiniTest2_V2.%20Respond%20to%20a%20Written%20Resquest.pdf')),

(@mt_nv_7_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/MiniTest/MiniTest3_V2.%20Respond%20to%20a%20Written%20Resquest.pdf'));

-- Chương 8: Write an Opinion Essay (@ch_nv_8)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_8, 'Mini-test 1 – Opinion Essay', 1, 10.00, 0.00, 20, 1, 1),
(@kh_noiviet, @ch_nv_8, 'Mini-test 2 – Opinion Essay', 2, 10.00, 0.00, 20, 1, 1),
(@kh_noiviet, @ch_nv_8, 'Mini-test 3 – Opinion Essay', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_nv_8_1 := LAST_INSERT_ID(); SET @mt_nv_8_2 := @mt_nv_8_1 + 1; SET @mt_nv_8_3 := @mt_nv_8_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_8_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/MiniTest/MiniTest1_V3.%20Write%20an%20Opinion%20Essay.pdf')),

(@mt_nv_8_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/MiniTest/MiniTest2_V3.%20Write%20an%20Opinion%20Essay.pdf')),

(@mt_nv_8_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/MiniTest/MiniTest3_V3.%20Write%20an%20Opinion%20Essay.pdf'));

-- =========================
-- 2) KHÓA NGHE - ĐỌC (@kh_nghedoc)
-- =========================
-- Chương 1: PART 1 – Photographs (@ch_nd_1)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_1, 'Mini-test 1 – Part 1 Photographs', 1, 10.00, 0.00, 10, 1, 1),
(@kh_nghedoc, @ch_nd_1, 'Mini-test 2 – Part 1 Photographs', 2, 10.00, 0.00, 10, 1, 1),
(@kh_nghedoc, @ch_nd_1, 'Mini-test 3 – Part 1 Photographs', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_nd_1_1 := LAST_INSERT_ID(); SET @mt_nd_1_2 := @mt_nd_1_1 + 1; SET @mt_nd_1_3 := @mt_nd_1_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_1_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/Audio/Thi%20Online-%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).mp3')),

(@mt_nd_1_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_nd_1_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/%C4%91%C3%A1p%20%C3%A1n.pdf')),

-- MiniTest 2
(@mt_nd_1_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/Audio/Thi%20Online-%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2002).mp3')),

(@mt_nd_1_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2002).pdf')),

(@mt_nd_1_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/%C4%91%C3%A1p%20%C3%A1n.pdf')),

-- MiniTest 3
(@mt_nd_1_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/Audio/Thi%20Online-%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).mp3')),

(@mt_nd_1_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/%C4%90%E1%BB%81/Thi%20Online_%20Luy%E1%BB%87n%20t%E1%BA%ADp%20b%E1%BB%99%20c%C3%A2u%20h%E1%BB%8Fi%20tr%E1%BB%8Dng%20%C4%91i%E1%BB%83m%20Part%201%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

(@mt_nd_1_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/%C4%91%C3%A1p%20%C3%A1n.pdf'));

-- Chương 2: PART 2 – Question–Response (@ch_nd_2)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_2, 'Mini-test 1 – Part 2 Question–Response', 1, 10.00, 0.00, 10, 1, 1),
(@kh_nghedoc, @ch_nd_2, 'Mini-test 2 – Part 2 Question–Response', 2, 10.00, 0.00, 10, 1, 1),
(@kh_nghedoc, @ch_nd_2, 'Mini-test 3 – Part 2 Question–Response', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_nd_2_1 := LAST_INSERT_ID(); SET @mt_nd_2_2 := @mt_nd_2_1 + 1; SET @mt_nd_2_3 := @mt_nd_2_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_2_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test1/Audio/01.mp3')),

(@mt_nd_2_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test1/%C4%90%E1%BB%81/DE%201-1.pdf')),

(@mt_nd_2_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/DA%201-1.pdf')),

-- MiniTest 2
(@mt_nd_2_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test2/Audio/02.mp3')),

(@mt_nd_2_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test2/%C4%90%E1%BB%81/DE%202-1.pdf')),

(@mt_nd_2_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/DA%202-1.pdf')),

-- MiniTest 3
(@mt_nd_2_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test3/Audio/File%20nghe%201.mp3')),

(@mt_nd_2_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test3/%C4%90%E1%BB%81/DE%201.pdf')),

(@mt_nd_2_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/DA%201.pdf'));

-- Chương 3: PART 3 – Short Conversations (@ch_nd_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_3, 'Mini-test 1 – Part 3 Conversations', 1, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_3, 'Mini-test 2 – Part 3 Conversations', 2, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_3, 'Mini-test 3 – Part 3 Conversations', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nd_3_1 := LAST_INSERT_ID(); SET @mt_nd_3_2 := @mt_nd_3_1 + 1; SET @mt_nd_3_3 := @mt_nd_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_3_1, 'Audio 1', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/Audio/01.mp3')),

(@mt_nd_3_1, 'Audio 2', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/Audio/02.mp3')),

(@mt_nd_3_1, 'Audio 3', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/Audio/03.mp3')),

(@mt_nd_3_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/%C4%90%E1%BB%81/DE%201.pdf')),

(@mt_nd_3_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/DA%201.pdf')),

-- MiniTest 2
(@mt_nd_3_2, 'Audio 1', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/Audio/Part%203%20-%2041-43.mp3')),

(@mt_nd_3_2, 'Audio 2', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/Audio/Part%203-44-46.mp3')),

(@mt_nd_3_2, 'Audio 3', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/Audio/Part%203-47-49.mp3')),

(@mt_nd_3_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/%C4%90%E1%BB%81/DE%202.pdf')),

(@mt_nd_3_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/DA%202.pdf')),

-- MiniTest 3
(@mt_nd_3_3, 'Audio 1', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/Audio/Part%203.1.mp3')),

(@mt_nd_3_3, 'Audio 2', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/Audio/Part%203.10.mp3')),

(@mt_nd_3_3, 'Audio 3', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/Audio/Part%203.2.mp3')),

(@mt_nd_3_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/%C4%90%E1%BB%81/DE%201.pdf')),

(@mt_nd_3_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/DA%201.pdf'));


-- Chương 4: PART 4 – Short Talks (@ch_nd_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_4, 'Mini-test 1 – Part 4 Short Talks', 1, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_4, 'Mini-test 2 – Part 4 Short Talks', 2, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_4, 'Mini-test 3 – Part 4 Short Talks', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nd_4_1 := LAST_INSERT_ID(); SET @mt_nd_4_2 := @mt_nd_4_1 + 1; SET @mt_nd_4_3 := @mt_nd_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_4_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/Audio/mp3.1.mp3')),

(@mt_nd_4_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/Audio/mp3.2.mp3')),

(@mt_nd_4_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/Audio/mp3.3.mp3')),

(@mt_nd_4_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/%C4%90%E1%BB%81/Part%204%20-%20Thi%20Online_%20OFFICE%20(%C4%90%E1%BB%81%20S%E1%BB%91%2001).pdf')),

(@mt_nd_4_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Office%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_nd_4_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/Audio/mp3.1.mp3')),

(@mt_nd_4_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/Audio/mp3.2.mp3')),

(@mt_nd_4_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/Audio/mp3.3.mp3')),

(@mt_nd_4_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/%C4%90%E1%BB%81/Part%204%20-%20Thi%20Online_%20SHOPPING%20AND%20ENTERTAINMENT%20(%C4%90%E1%BB%81%20S%E1%BB%91%2001).pdf')),

(@mt_nd_4_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Shopping%20and%20Entertainment%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_nd_4_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/Audio/mp3.1.mp3')),

(@mt_nd_4_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/Audio/mp3.2.mp3')),

(@mt_nd_4_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/Audio/mp3.3.mp3')),

(@mt_nd_4_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/%C4%90%E1%BB%81/Part%204%20-%20Thi%20Online_%20ADVERTISEMENTS%20(%C4%90%E1%BB%81%20S%E1%BB%91%2001).pdf')),

(@mt_nd_4_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Advertisement%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf'));

-- Chương 5: PART 5–6 – Incomplete Sentences (@ch_nd_5)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_5, 'Mini-test 1 – Part 5–6 Incomplete Sentences', 1, 10.00, 0.00, 15, 1, 1),
(@kh_nghedoc, @ch_nd_5, 'Mini-test 2 – Part 5–6 Incomplete Sentences', 2, 10.00, 0.00, 15, 1, 1),
(@kh_nghedoc, @ch_nd_5, 'Mini-test 3 – Part 5–6 Incomplete Sentences', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_nd_5_1 := LAST_INSERT_ID(); SET @mt_nd_5_2 := @mt_nd_5_1 + 1; SET @mt_nd_5_3 := @mt_nd_5_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_5_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20online_%20C%C3%A1c%20Th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_nd_5_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20online_%20C%C3%A1c%20Th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 2
(@mt_nd_5_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test2/%C4%90%E1%BB%81/Thi%20Online_%20%C4%90%E1%BA%A1i%20t%E1%BB%AB%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

(@mt_nd_5_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20%C4%90%E1%BA%A1i%20t%E1%BB%AB%20(%C4%90%E1%BB%81%20s%E1%BB%91%2001).pdf')),

-- MiniTest 3
(@mt_nd_5_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test3/%C4%90%E1%BB%81/Thi%20Online_%20Ch%E1%BB%A9c%20n%C4%83ng%20c%E1%BB%A7a%20danh%2C%20%C4%91%E1%BB%99ng%20t%E1%BB%AB%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

(@mt_nd_5_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A9c%20n%C4%83ng%20c%E1%BB%A7a%20danh%2C%20%C4%91%E1%BB%99ng%20t%E1%BB%AB%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf'));

-- Chương 6: PART 7 – Vocabulary & Reading Comprhension Practice (@ch_nd_6)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_6, 'Mini-test 1 – Part 7 Vocabulary & Reading Comprhension Practice', 1, 10.00, 0.00, 20, 1, 1),
(@kh_nghedoc, @ch_nd_6, 'Mini-test 2 – Part 7 Vocabulary & Reading Comprhension Practice', 2, 10.00, 0.00, 20, 1, 1),
(@kh_nghedoc, @ch_nd_6, 'Mini-test 3 – Part 7 Vocabulary & Reading Comprhension Practice', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_nd_6_1 := LAST_INSERT_ID(); SET @mt_nd_6_2 := @mt_nd_6_1 + 1; SET @mt_nd_6_3 := @mt_nd_6_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_6_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test1/%C4%90%E1%BB%81/Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20E-%20mail_Letter_Fax%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

(@mt_nd_6_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test1/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20E-%20mail_Letter_Fax%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

-- MiniTest 2
(@mt_nd_6_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test2/%C4%90%E1%BB%81/Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Memo%20_Notice%20_Announcement%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

(@mt_nd_6_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test2/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Memo%20_Notice%20_Announcement%20(%C4%90%E1%BB%81%20s%E1%BB%91%2003).pdf')),

-- MiniTest 3
(@mt_nd_6_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test3/%C4%90%E1%BB%81/Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Article%20(%C4%90%E1%BB%81%20s%E1%BB%91%2002).pdf')),

(@mt_nd_6_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/MiniTest/Test3/%C4%90%C3%A1p%20%C3%A1n/%C4%90A%20Thi%20Online_%20Ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Article%20(%C4%90%E1%BB%81%20s%E1%BB%91%2002).pdf'));

-- =========================================================
-- 11) FINAL TEST — KHÓA NÓI - VIẾT 
-- =========================================================
-- =========================
-- 1) KHÓA NÓI - VIẾT (@kh_noiviet)
-- =========================
INSERT INTO TEST (maKH, dotTest, title, time_limit_min, total_questions)
VALUES (@kh_noiviet, 'Final', 'Final Test - Nói/Viết', 90, NULL);
SET @test_nv := LAST_INSERT_ID();

INSERT INTO TEST_TAILIEU (maTest, tenTL, loai, mime_type, visibility, public_url) VALUES
(@test_nv, 'Final - Speaking - Đề 1', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/FinalTest/DeSpeaking1.pdf')),

(@test_nv, 'Final - Speaking - Đề 2', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/FinalTest/DeSpeaking2.pdf')),

(@test_nv, 'Final - Writing  - Đề 1', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/FinalTest/DeWritting1.pdf')),

(@test_nv, 'Final - Writing  - Đề 2', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/FinalTest/DeWritting2.pdf'));

-- =========================
-- 2) KHÓA NGHE - ĐỌC (@kh_nghedoc)
-- =========================
INSERT INTO TEST (maKH, dotTest, title, time_limit_min, total_questions)
VALUES (@kh_nghedoc, 'Final', 'Final Test - Nghe/Đọc', 120, NULL);
SET @test_nd := LAST_INSERT_ID();

INSERT INTO TEST_TAILIEU (maTest, tenTL, loai, mime_type, visibility, public_url) VALUES
(@test_nd, 'Final - Audio 1',  'MP3', 'audio/mpeg',      'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/FINAL%20TEST/Audio/Part%201.mp3')),

(@test_nd, 'Final - Audio 2',  'MP3', 'audio/mpeg',      'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/FINAL%20TEST/Audio/Part%202.mp3')),

(@test_nd, 'Final - Audio 3',  'MP3', 'audio/mpeg',      'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/FINAL%20TEST/Audio/Part%203.mp3')),

(@test_nd, 'Final - Đề',     'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/FINAL%20TEST/De/DE19.pdf')),

(@test_nd, 'Final - Đáp án', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/FINAL%20TEST/DapAn/DA19.pdf'));

COMMIT;