USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 9) TÀI LIỆU HỌC TẬP (Cloudflare R2)
-- =========================================================
SET @R2_BASE_PUBLIC := 'https://pub-9b3a3b8712d849d7b4e15e85e6beca8d.r2.dev';

-- =========================================================
-- NÓI - VIẾT
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, storage_key, r2_bucket, mime_type, size_bytes, duration_sec, visibility, public_url) VALUES
-- Chương 1 - Bài 1
(@bh_nv_1_1, 'Read a Text Aloud 1 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'video/mp4', 314572800, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')
),

(@bh_nv_1_1, 'Read a Text Aloud 1 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'application/pdf', 10485760, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')
),

-- Chương 1 - Bài 2
(@bh_nv_1_3, 'Read a Text Aloud 2 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'video/mp4', 314572800, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')
),

(@bh_nv_1_4, 'Read a Text Aloud 2 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'application/pdf', 10485760, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')
),

-- Chương 1 - Bài 3
(@bh_nv_1_5, 'Read a Text Aloud 3 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'video/mp4', 314572800, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')
),

(@bh_nv_1_6, 'Read a Text Aloud 3 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.',
NULL, NULL, 'application/pdf', 10485760, NULL, 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')
),

-- Chương 2 - Bài 1
(@bh_nv_2_1, 'Part 1 Q&A video', 'Video', '400MB', 'Lesson video',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_2_1, '-part1.mp4'), NULL, 'video/mp4', 419430400, NULL, 'public', NULL),
(@bh_nv_2_2, 'Describe picture samples', 'ZIP', '100MB', 'Images + audio',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_2_2, '-pictures.zip'), NULL, 'application/zip', 104857600, NULL, 'public', NULL),

-- Chương 2 - Bài 2
(@bh_nv_2_3, 'Part 2 response video', 'Video', '350MB', 'Q/R strategies',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_2_3, '-part2.mp4'), NULL, 'video/mp4', 367001600, NULL, 'public', NULL),
(@bh_nv_2_4, 'Common questions quiz', 'ZIP', '80MB', 'Drills',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_2_4, '-questions.zip'), NULL, 'application/zip', 83886080, NULL, 'public', NULL),

-- Chương 3 - Bài 1
(@bh_nv_3_1, 'Email template PDF', 'PDF', '4MB', 'Layouts',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_3_1, '-email.pdf'), NULL, 'application/pdf', 4194304, NULL, 'public', NULL),
(@bh_nv_3_2, 'Notice writing video', 'Video', '250MB', 'Demo video',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_3_2, '-notice.mp4'), NULL, 'video/mp4', 262144000, NULL, 'public', NULL),

-- Chương 3 - Bài 2
(@bh_nv_3_3, 'Sample exercises ZIP', 'ZIP', '15MB', 'Tasks',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_3_3, '-exercises.zip'), NULL, 'application/zip', 15728640, NULL, 'public', NULL),
(@bh_nv_3_4, 'Errors quiz', 'ZIP', '25MB', 'Quiz pack',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_3_4, '-errors.zip'), NULL, 'application/zip', 26214400, NULL, 'public', NULL),

-- Chương 4 - Bài 1
(@bh_nv_4_1, 'Argument structure PDF', 'PDF', '6MB', 'Guide',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_4_1, '-structure.pdf'), NULL, 'application/pdf', 6291456, NULL, 'public', NULL),
(@bh_nv_4_2, 'Opinion samples video', 'Video', '350MB', 'Examples',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_4_2, '-samples.mp4'), NULL, 'video/mp4', 367001600, NULL, 'public', NULL),

-- Chương 4 - Bài 2
(@bh_nv_4_3, 'Prompt practice assignment', 'DOC', '2MB', 'Templates',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_4_3, '-prompt.doc'), NULL, 'application/msword', 2097152, NULL, 'public', NULL),
(@bh_nv_4_4, 'Cohesion tips PDF', 'PDF', '3MB', 'Tips',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_4_4, '-cohesion.pdf'), NULL, 'application/pdf', 3145728, NULL, 'public', NULL),

-- Chương 4 - Bài 3
(@bh_nv_4_5, 'Task 2 graded quiz', 'ZIP', '40MB', 'Mock graded',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_4_5, '-task2.zip'), NULL, 'application/zip', 41943040, NULL, 'public', NULL),

-- Chương 5 - Bài 1
(@bh_nv_5_1, 'Integrated task video', 'Video', '450MB', 'Demo',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_5_1, '-integrated.mp4'), NULL, 'video/mp4', 471859200, NULL, 'public', NULL),
(@bh_nv_5_2, 'Skills drill quiz', 'ZIP', '60MB', 'Drills',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_5_2, '-drill.zip'), NULL, 'application/zip', 62914560, NULL, 'public', NULL),

-- Chương 5 - Bài 2
(@bh_nv_5_3, 'Context practice assignment', 'PDF', '7MB', 'Sheets',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_5_3, '-context.pdf'), NULL, 'application/pdf', 7340032, NULL, 'public', NULL),
(@bh_nv_5_4, 'Errors review doc', 'DOC', '4MB', 'Analysis',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_5_4, '-review.doc'), NULL, 'application/msword', 4194304, NULL, 'public', NULL),

-- Chương 5 - Bài 3
(@bh_nv_5_5, 'Mock integrated ZIP', 'ZIP', '130MB', 'Full test',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_5_5, '-mock-integrated.zip'), NULL, 'application/zip', 136314880, NULL, 'public', NULL),

-- Chương 6 - Bài 1
(@bh_nv_6_1, 'Debate video', 'Video', '500MB', 'Advanced debate',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_6_1, '-debate.mp4'), NULL, 'video/mp4', 524288000, NULL, 'public', NULL),
(@bh_nv_6_2, 'Speed quiz', 'ZIP', '80MB', 'Quick drills',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_6_2, '-speed.zip'), NULL, 'application/zip', 83886080, NULL, 'public', NULL),

-- Chương 6 - Bài 2
(@bh_nv_6_3, 'Advanced pron video', 'Video', '200MB', 'Idioms',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_6_3, '-pron.mp4'), NULL, 'video/mp4', 209715200, NULL, 'public', NULL),
(@bh_nv_6_4, 'Mock Part 3-4 assignment', 'ZIP', '120MB', 'Files',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_6_4, '-mock34.zip'), NULL, 'application/zip', 125829120, NULL, 'public', NULL),

-- Chương 6 - Bài 3
(@bh_nv_6_5, 'Final speaking test PDF', 'PDF', '15MB', 'Full test',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_6_5, '-final-speaking.pdf'), NULL, 'application/pdf', 15728640, NULL, 'public', NULL),

-- Chương 7 - Bài 1
(@bh_nv_7_1, 'Complex sentences PDF', 'PDF', '5MB', 'Grammar',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_7_1, '-sentences.pdf'), NULL, 'application/pdf', 5242880, NULL, 'public', NULL),
(@bh_nv_7_2, 'Vocab video', 'Video', '300MB', 'Specialized',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_7_2, '-vocab.mp4'), NULL, 'video/mp4', 314572800, NULL, 'public', NULL),

-- Chương 7 - Bài 2
(@bh_nv_7_3, 'Advanced structures doc', 'DOC', '6MB', 'Samples',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_7_3, '-structures.doc'), NULL, 'application/msword', 6291456, NULL, 'public', NULL),
(@bh_nv_7_4, 'Peer review quiz', 'ZIP', '50MB', 'Templates',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_7_4, '-peer.zip'), NULL, 'application/zip', 52428800, NULL, 'public', NULL),

-- Chương 7 - Bài 3
(@bh_nv_7_5, 'Final drills assignment', 'PDF', '10MB', 'Full practice',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_7_5, '-drills.pdf'), NULL, 'application/pdf', 10485760, NULL, 'public', NULL),

-- Chương 8 - Bài 1
(@bh_nv_8_1, 'Full speaking mock ZIP', 'ZIP', '150MB', 'Test pack',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_8_1, '-speaking-mock.zip'), NULL, 'application/zip', 157286400, NULL, 'public', NULL),
(@bh_nv_8_2, 'Full writing mock PDF', 'PDF', '12MB', 'Essay mocks',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_8_2, '-writing-mock.pdf'), NULL, 'application/pdf', 12582912, NULL, 'public', NULL),

-- Chương 8 - Bài 2
(@bh_nv_8_3, 'Analysis video', 'Video', '400MB', 'Session',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_8_3, '-analysis.mp4'), NULL, 'video/mp4', 419430400, NULL, 'public', NULL),
(@bh_nv_8_4, 'Final tips doc', 'DOC', '2MB', 'Tips',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_8_4, '-tips.doc'), NULL, 'application/msword', 2097152, NULL, 'public', NULL),

-- Chương 8 - Bài 3
(@bh_nv_8_5, 'Complete resource pack ZIP', 'ZIP', '200MB', 'All materials',
CONCAT('courses/', @kh_noiviet, '/', @bh_nv_8_5, '-complete-pack.zip'), NULL, 'application/zip', 209715200, NULL, 'public', NULL);

-- =========================================================
-- NGHE - ĐỌC
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, storage_key, r2_bucket, mime_type, size_bytes, duration_sec, visibility, public_url) VALUES
-- Chương 1 - Bài 1
(@bh_nd_1_1, 'Overview video', 'Video', '250MB', 'Strategy video',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_1, '-overview.mp4'), NULL, 'video/mp4', 262144000, NULL, 'public', NULL),
(@bh_nd_1_2, 'Part 1 strategy audio', 'MP3', '30MB', 'Photos audio',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_2, '-part1.mp3'), NULL, 'audio/mpeg', 31457280, NULL, 'public', NULL),

-- Chương 1 - Bài 2
(@bh_nd_1_3, 'Note-taking quiz', 'ZIP', '40MB', 'Drills',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_3, '-notes.zip'), NULL, 'application/zip', 41943040, NULL, 'public', NULL),
(@bh_nd_1_4, 'Prediction PDF', 'PDF', '4MB', 'Exercises',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_4, '-prediction.pdf'), NULL, 'application/pdf', 4194304, NULL, 'public', NULL),

-- Chương 1 - Bài 3
(@bh_nd_1_5, 'Part 2 drills video', 'Video', '300MB', 'Practice',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_5, '-part2-drills.mp4'), NULL, 'video/mp4', 314572800, NULL, 'public', NULL),
(@bh_nd_1_6, 'Listening vocab doc', 'DOC', '3MB', 'Word list',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_1_6, '-vocab.doc'), NULL, 'application/msword', 3145728, NULL, 'public', NULL),

-- Chương 2 - Bài 1
(@bh_nd_2_1, 'Part 2 video', 'Video', '350MB', 'Q/R lesson',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_2_1, '-part2.mp4'), NULL, 'video/mp4', 367001600, NULL, 'public', NULL),
(@bh_nd_2_2, 'Audio drills quiz', 'ZIP', '100MB', 'Part 1-2 pack',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_2_2, '-audio-drills.zip'), NULL, 'application/zip', 104857600, NULL, 'public', NULL),

-- Chương 2 - Bài 2
(@bh_nd_2_3, 'Traps analysis doc', 'DOC', '3MB', 'Guide',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_2_3, '-traps.doc'), NULL, 'application/msword', 3145728, NULL, 'public', NULL),
(@bh_nd_2_4, 'Speed practice assignment', 'MP3', '60MB', 'Fast audio',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_2_4, '-speed.mp3'), NULL, 'audio/mpeg', 62914560, NULL, 'public', NULL),

-- Chương 2 - Bài 3
(@bh_nd_2_5, 'Mini drill ZIP', 'ZIP', '70MB', 'Quick set',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_2_5, '-mini.zip'), NULL, 'application/zip', 73400320, NULL, 'public', NULL),

-- Chương 3 - Bài 1
(@bh_nd_3_1, 'Skimming video', 'Video', '200MB', 'Techniques',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_1, '-skimming.mp4'), NULL, 'video/mp4', 209715200, NULL, 'public', NULL),
(@bh_nd_3_2, 'Vocab PDF', 'PDF', '8MB', 'List',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_2, '-vocab.pdf'), NULL, 'application/pdf', 8388608, NULL, 'public', NULL),

-- Chương 3 - Bài 2
(@bh_nd_3_3, 'Skimming drills video', 'Video', '150MB', 'Practice',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_3, '-drills.mp4'), NULL, 'video/mp4', 157286400, NULL, 'public', NULL),
(@bh_nd_3_4, 'Part 5 quiz', 'ZIP', '50MB', 'Sentences',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_4, '-part5.zip'), NULL, 'application/zip', 52428800, NULL, 'public', NULL),

-- Chương 3 - Bài 3
(@bh_nd_3_5, 'Part 6 practice doc', 'DOC', '4MB', 'Completion',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_5, '-part6.doc'), NULL, 'application/msword', 4194304, NULL, 'public', NULL),
(@bh_nd_3_6, 'Reading strategies quiz', 'ZIP', '60MB', 'Techniques',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_3_6, '-strategies.zip'), NULL, 'application/zip', 62914560, NULL, 'public', NULL),

-- Chương 4 - Bài 1
(@bh_nd_4_1, 'Part 5 quiz pack', 'ZIP', '80MB', 'Grammar drills',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_4_1, '-part5-quiz.zip'), NULL, 'application/zip', 83886080, NULL, 'public', NULL),
(@bh_nd_4_2, 'Part 6 doc', 'DOC', '5MB', 'Completion',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_4_2, '-part6.doc'), NULL, 'application/msword', 5242880, NULL, 'public', NULL),

-- Chương 4 - Bài 2
(@bh_nd_4_3, 'Mock 5-6 PDF', 'PDF', '10MB', 'Test',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_4_3, '-mock56.pdf'), NULL, 'application/pdf', 10485760, NULL, 'public', NULL),
(@bh_nd_4_4, 'Context vocab video', 'Video', '300MB', 'Lesson',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_4_4, '-context.mp4'), NULL, 'video/mp4', 314572800, NULL, 'public', NULL),

-- Chương 4 - Bài 3
(@bh_nd_4_5, 'Error spotting assignment', 'ZIP', '20MB', 'Files',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_4_5, '-spotting.zip'), NULL, 'application/zip', 20971520, NULL, 'public', NULL),

-- Chương 5 - 6 - Bài 1
(@bh_nd_5_1, 'Part 3 video', 'Video', '400MB', 'Conversations',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_5_1, '-part3.mp4'), NULL, 'video/mp4', 419430400, NULL, 'public', NULL),
(@bh_nd_5_2, 'Part 4 quiz', 'ZIP', '90MB', 'Talks audio',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_5_2, '-part4.zip'), NULL, 'application/zip', 94371840, NULL, 'public', NULL),

-- Chương 5 - 6 - Bài 2
(@bh_nd_5_3, 'Inference doc', 'DOC', '4MB', 'Skills',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_5_3, '-inference.doc'), NULL, 'application/msword', 4194304, NULL, 'public', NULL),
(@bh_nd_5_4, 'Audio pack MP3', 'MP3', '100MB', 'Part 3-4',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_5_4, '-audio-pack.mp3'), NULL, 'audio/mpeg', 104857600, NULL, 'public', NULL),

-- Chương 5 - 6 - Bài 3
(@bh_nd_5_5, 'Mock Part 3-4 PDF', 'PDF', '20MB', 'Full test',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_5_5, '-mock34.pdf'), NULL, 'application/pdf', 20971520, NULL, 'public', NULL),

-- Chương 7 (biến nd_6_*) - Bài 1
(@bh_nd_6_1, 'Part 7 single video', 'Video', '250MB', 'Passages',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_6_1, '-part7-single.mp4'), NULL, 'video/mp4', 262144000, NULL, 'public', NULL),
(@bh_nd_6_2, 'Double passages quiz', 'ZIP', '120MB', 'Practice',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_6_2, '-double.zip'), NULL, 'application/zip', 125829120, NULL, 'public', NULL),

-- Chương 7 - Bài 2
(@bh_nd_6_3, 'Time management PDF', 'PDF', '3MB', 'Tips',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_6_3, '-time.pdf'), NULL, 'application/pdf', 3145728, NULL, 'public', NULL),
(@bh_nd_6_4, 'Intensive drills assignment', 'DOC', '6MB', 'Sheets',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_6_4, '-drills.doc'), NULL, 'application/msword', 6291456, NULL, 'public', NULL),

-- Chương 7 - Bài 3
(@bh_nd_6_5, 'Mock Part 7 ZIP', 'ZIP', '150MB', 'Full test',
CONCAT('courses/', @kh_nghedoc, '/', @bh_nd_6_5, '-mock7.zip'), NULL, 'application/zip', 157286400, NULL, 'public', NULL);

COMMIT;