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
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong2/MiniTest1.pdf')),

(@mt_nv_2_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong2/MiniTest2.pdf')),

(@mt_nv_2_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong2/MiniTest3.pdf'));

-- Chương 3: Respond to Questions (@ch_nv_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_3, 'Mini-test 1 – Respond to Questions', 1, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_3, 'Mini-test 2 – Respond to Questions', 2, 10.00, 0.00, 10, 1, 1),
(@kh_noiviet, @ch_nv_3, 'Mini-test 3 – Respond to Questions', 3, 10.00, 0.00, 10, 1, 1);
SET @mt_nv_3_1 := LAST_INSERT_ID(); SET @mt_nv_3_2 := @mt_nv_3_1 + 1; SET @mt_nv_3_3 := @mt_nv_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_3_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong3/MiniTest1.pdf')),

(@mt_nv_3_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong3/MiniTest2.pdf')),

(@mt_nv_3_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong3/MiniTest3.pdf'));

-- Chương 4: Respond to Questions Using Information Provided (@ch_nv_4)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_4, 'Mini-test 1 – Respond to Questions Using Information', 1, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_4, 'Mini-test 2 – Respond to Questions Using Information', 2, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_4, 'Mini-test 3 – Respond to Questions Using Information', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nv_4_1 := LAST_INSERT_ID(); SET @mt_nv_4_2 := @mt_nv_4_1 + 1; SET @mt_nv_4_3 := @mt_nv_4_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_4_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong4/MiniTest1.pdf')),

(@mt_nv_4_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong4/MiniTest2.pdf')),

(@mt_nv_4_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong4/MiniTest3.pdf'));

-- Chương 5: Express an Opinion (@ch_nv_5)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_5, 'Mini-test 1 – Express an Opinion', 1, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_5, 'Mini-test 2 – Express an Opinion', 2, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_5, 'Mini-test 3 – Express an Opinion', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_nv_5_1 := LAST_INSERT_ID(); SET @mt_nv_5_2 := @mt_nv_5_1 + 1; SET @mt_nv_5_3 := @mt_nv_5_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_5_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong5/MiniTest1.pdf')),

(@mt_nv_5_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong5/MiniTest2.pdf')),

(@mt_nv_5_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong5/MiniTest3.pdf'));

-- Chương 6: Write a Sentence Based on a Picture (@ch_nv_6)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_6, 'Mini-test 1 – Sentence from Picture', 1, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_6, 'Mini-test 2 – Sentence from Picture', 2, 10.00, 0.00, 12, 1, 1),
(@kh_noiviet, @ch_nv_6, 'Mini-test 3 – Sentence from Picture', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nv_6_1 := LAST_INSERT_ID(); SET @mt_nv_6_2 := @mt_nv_6_1 + 1; SET @mt_nv_6_3 := @mt_nv_6_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_6_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong6/MiniTest1.pdf')),

(@mt_nv_6_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong6/MiniTest2.pdf')),

(@mt_nv_6_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong6/MiniTest3.pdf'));

-- Chương 7: Respond to a Written Request (@ch_nv_7)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_7, 'Mini-test 1 – Written Request', 1, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_7, 'Mini-test 2 – Written Request', 2, 10.00, 0.00, 15, 1, 1),
(@kh_noiviet, @ch_nv_7, 'Mini-test 3 – Written Request', 3, 10.00, 0.00, 15, 1, 1);
SET @mt_nv_7_1 := LAST_INSERT_ID(); SET @mt_nv_7_2 := @mt_nv_7_1 + 1; SET @mt_nv_7_3 := @mt_nv_7_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_7_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong7/MiniTest1.pdf')),

(@mt_nv_7_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong7/MiniTest2.pdf')),

(@mt_nv_7_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong7/MiniTest3.pdf'));

-- Chương 8: Write an Opinion Essay (@ch_nv_8)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_8, 'Mini-test 1 – Opinion Essay', 1, 10.00, 0.00, 20, 1, 1),
(@kh_noiviet, @ch_nv_8, 'Mini-test 2 – Opinion Essay', 2, 10.00, 0.00, 20, 1, 1),
(@kh_noiviet, @ch_nv_8, 'Mini-test 3 – Opinion Essay', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_nv_8_1 := LAST_INSERT_ID(); SET @mt_nv_8_2 := @mt_nv_8_1 + 1; SET @mt_nv_8_3 := @mt_nv_8_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
(@mt_nv_8_1, 'MiniTest 1 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong8/MiniTest1.pdf')),

(@mt_nv_8_2, 'MiniTest 2 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong8/MiniTest2.pdf')),

(@mt_nv_8_3, 'MiniTest 3 - PDF', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Chuong8/MiniTest3.pdf'));

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
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest1/audio.mp3')),

(@mt_nd_1_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest1/de.pdf')),

(@mt_nd_1_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_1_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest2/audio.mp3')),

(@mt_nd_1_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest2/de.pdf')),

(@mt_nd_1_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_1_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest3/audio.mp3')),

(@mt_nd_1_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest3/de.pdf')),

(@mt_nd_1_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong1/MiniTest3/dapan.pdf'));

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
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest1/audio.mp3')),

(@mt_nd_2_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest1/de.pdf')),

(@mt_nd_2_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_2_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest2/audio.mp3')),

(@mt_nd_2_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest2/de.pdf')),

(@mt_nd_2_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_2_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest3/audio.mp3')),

(@mt_nd_2_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest3/de.pdf')),

(@mt_nd_2_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong2/MiniTest3/dapan.pdf'));

-- Chương 3: PART 3 – Short Conversations (@ch_nd_3)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_3, 'Mini-test 1 – Part 3 Conversations', 1, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_3, 'Mini-test 2 – Part 3 Conversations', 2, 10.00, 0.00, 12, 1, 1),
(@kh_nghedoc, @ch_nd_3, 'Mini-test 3 – Part 3 Conversations', 3, 10.00, 0.00, 12, 1, 1);
SET @mt_nd_3_1 := LAST_INSERT_ID(); SET @mt_nd_3_2 := @mt_nd_3_1 + 1; SET @mt_nd_3_3 := @mt_nd_3_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_3_1, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest1/audio.mp3')),

(@mt_nd_3_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest1/de.pdf')),

(@mt_nd_3_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_3_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest2/audio.mp3')),

(@mt_nd_3_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest2/de.pdf')),

(@mt_nd_3_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_3_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest3/audio.mp3')),

(@mt_nd_3_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest3/de.pdf')),

(@mt_nd_3_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong3/MiniTest3/dapan.pdf'));

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
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest1/audio.mp3')),

(@mt_nd_4_1, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest1/de.pdf')),

(@mt_nd_4_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_4_2, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest2/audio.mp3')),

(@mt_nd_4_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest2/de.pdf')),

(@mt_nd_4_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_4_3, 'Audio', 'MP3', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest3/audio.mp3')),

(@mt_nd_4_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest3/de.pdf')),

(@mt_nd_4_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong4/MiniTest3/dapan.pdf'));

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
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest1/de.pdf')),

(@mt_nd_5_1, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_5_2, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest2/de.pdf')),

(@mt_nd_5_2, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_5_3, 'Đề',    'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest3/de.pdf')),

(@mt_nd_5_3, 'Đáp án','PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong5/MiniTest3/dapan.pdf'));

-- Chương 6: PART 7 – Vocabulary & Reading Comprhension Practice (@ch_nd_6)
INSERT INTO CHUONG_MINITEST
(maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_6, 'Mini-test 1 – Part 7 Vocabulary & Reading Comprhension Practice', 1, 10.00, 0.00, 20, 1, 1),
(@kh_nghedoc, @ch_nd_6, 'Mini-test 2 – Part 7 Vocabulary & Reading Comprhension Practice', 2, 10.00, 0.00, 20, 1, 1),
(@kh_nghedoc, @ch_nd_6, 'Mini-test 3 – Part 7 Vocabulary & Reading Comprhension Practice', 3, 10.00, 0.00, 20, 1, 1);
SET @mt_nd_6_1 := LAST_INSERT_ID(); SET @mt_nd_6_2 := @mt_nd_6_1 + 1; SET @mt_nd_6_3 := @mt_nd_6_1 + 2;

INSERT INTO MINITEST_TAILIEU (maMT, tenTL, loai, mime_type, visibility, public_url) VALUES
-- MiniTest 1
(@mt_nd_6_1, 'Đề',    'PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest1/de.pdf')),
(@mt_nd_6_1, 'Đáp án','PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest1/dapan.pdf')),

-- MiniTest 2
(@mt_nd_6_2, 'Đề',    'PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest2/de.pdf')),
(@mt_nd_6_2, 'Đáp án','PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest2/dapan.pdf')),

-- MiniTest 3
(@mt_nd_6_3, 'Đề',    'PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest3/de.pdf')),
(@mt_nd_6_3, 'Đáp án','PDF', 'application/pdf', 'public', CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Chuong6/MiniTest3/dapan.pdf'));

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
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Final/Speaking/De1.pdf')),

(@test_nv, 'Final - Speaking - Đề 2', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Final/Speaking/De2.pdf')),

(@test_nv, 'Final - Writing  - Đề 1', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Final/Writing/De1.pdf')),

(@test_nv, 'Final - Writing  - Đề 2', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Noi-Viet/Final/Writing/De2.pdf'));

-- =========================
-- 2) KHÓA NGHE - ĐỌC (@kh_nghedoc)
-- =========================
INSERT INTO TEST (maKH, dotTest, title, time_limit_min, total_questions)
VALUES (@kh_nghedoc, 'Final', 'Final Test - Nghe/Đọc', 120, NULL);
SET @test_nd := LAST_INSERT_ID();

INSERT INTO TEST_TAILIEU (maTest, tenTL, loai, mime_type, visibility, public_url) VALUES
(@test_nd, 'Final - Audio',  'MP3', 'audio/mpeg',      'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Final/audio.mp3')),

(@test_nd, 'Final - Đề',     'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Final/de.pdf')),

(@test_nd, 'Final - Đáp án', 'PDF', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Nghe-Doc/Final/dapan.pdf'));

COMMIT;