USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 10) MINI-TEST THEO CHƯƠNG (CHUONG_MINITEST) - MỞ RỘNG (8 mỗi khóa, một theo mỗi chương)
-- =========================================================
-- Nói - Viết (một mini-test mỗi chương, thuTu=1, time_limit_min=10, attempts_allowed=1)
INSERT INTO CHUONG_MINITEST (maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_noiviet, @ch_nv_1, 'Quiz – Speaking Fundamentals', 1, 10.0, 0.15, 10, 1, 1),
(@kh_noiviet, @ch_nv_2, 'Mock Speaking Part 1-2', 1, 10.0, 0.20, 10, 1, 1),
(@kh_noiviet, @ch_nv_3, 'Task 1 Email Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_noiviet, @ch_nv_4, 'Task 2 Graded Practice', 1, 10.0, 0.20, 10, 1, 1),
(@kh_noiviet, @ch_nv_5, 'Integrated Skills Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_noiviet, @ch_nv_6, 'Advanced Drills Quiz', 1, 10.0, 0.20, 10, 1, 1),
(@kh_noiviet, @ch_nv_7, 'Writing Advanced Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_noiviet, @ch_nv_8, 'Full Mock Speaking', 1, 10.0, 0.25, 10, 1, 1);

-- Nghe - Đọc (một mini-test mỗi chương, thuTu=1, time_limit_min=10, attempts_allowed=1)
INSERT INTO CHUONG_MINITEST (maKH, maChuong, title, thuTu, max_score, trongSo, time_limit_min, attempts_allowed, is_active) VALUES
(@kh_nghedoc, @ch_nd_1, 'Listening Overview Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_nghedoc, @ch_nd_2, 'Mini Listening Drill 01', 1, 10.0, 0.20, 10, 1, 1),
(@kh_nghedoc, @ch_nd_3, 'Reading Skimming Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_nghedoc, @ch_nd_4, 'Mock Reading Part 5-6', 1, 10.0, 0.20, 10, 1, 1),
(@kh_nghedoc, @ch_nd_5, 'Listening Part 3-4 Quiz', 1, 10.0, 0.15, 10, 1, 1),
(@kh_nghedoc, @ch_nd_6, 'Mock Part 7 Intensive', 1, 10.0, 0.20, 10, 1, 1);

-- =========================================================
-- 11) TEST CUỐI KHÓA - MỞ RỘNG (2 đợt mỗi khóa)
-- =========================================================
INSERT INTO TEST (maKH, dotTest, title, time_limit_min, total_questions) VALUES
(@kh_noiviet, 'Đợt 1', 'Final – TOEIC Speaking & Writing Mock 1', 60, 40),
(@kh_noiviet, 'Đợt 2', 'Final – TOEIC Speaking & Writing Mock 2 (Advanced)', 75, 50),
(@kh_nghedoc, 'Đợt 1', 'Final – TOEIC Listening & Reading Mock 1', 120, 200),
(@kh_nghedoc, 'Đợt 2', 'Final – TOEIC Listening & Reading Mock 2 (850+)', 120, 200);

COMMIT;