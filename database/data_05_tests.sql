USE Online_Certificate_Classroom;

START TRANSACTION;

SET @R2_BASE_PUBLIC := 'https://pub-9b3a3b8712d849d7b4e15e85e6beca8d.r2.dev';

-- =========================================================
-- 0) Lấy maKH theo slug cho Band 405–600 × 4 kỹ năng
-- =========================================================
SELECT maKH INTO @kh_li_405 FROM KHOAHOC WHERE slug='luyen-thi-toeic-listening-405-600' LIMIT 1;
SELECT maKH INTO @kh_sp_405 FROM KHOAHOC WHERE slug='luyen-thi-toeic-speaking-405-600' LIMIT 1;
SELECT maKH INTO @kh_re_405 FROM KHOAHOC WHERE slug='luyen-thi-toeic-reading-405-600' LIMIT 1;
SELECT maKH INTO @kh_wr_405 FROM KHOAHOC WHERE slug='luyen-thi-toeic-writing-405-600' LIMIT 1;

-- =========================================================
-- 1) Lấy maChuong cho tất cả chương trong từng khóa
-- =========================================================
-- Listening (4 chương)
SELECT maChuong INTO @ch_li_405_1 FROM CHUONG WHERE maKH=@kh_li_405 AND thuTu=1 LIMIT 1;
SELECT maChuong INTO @ch_li_405_2 FROM CHUONG WHERE maKH=@kh_li_405 AND thuTu=2 LIMIT 1;
SELECT maChuong INTO @ch_li_405_3 FROM CHUONG WHERE maKH=@kh_li_405 AND thuTu=3 LIMIT 1;
SELECT maChuong INTO @ch_li_405_4 FROM CHUONG WHERE maKH=@kh_li_405 AND thuTu=4 LIMIT 1;

-- Speaking (4 chương)
SELECT maChuong INTO @ch_sp_405_1 FROM CHUONG WHERE maKH=@kh_sp_405 AND thuTu=1 LIMIT 1;
SELECT maChuong INTO @ch_sp_405_2 FROM CHUONG WHERE maKH=@kh_sp_405 AND thuTu=2 LIMIT 1;
SELECT maChuong INTO @ch_sp_405_3 FROM CHUONG WHERE maKH=@kh_sp_405 AND thuTu=3 LIMIT 1;
SELECT maChuong INTO @ch_sp_405_4 FROM CHUONG WHERE maKH=@kh_sp_405 AND thuTu=4 LIMIT 1;

-- Reading (2 chương)
SELECT maChuong INTO @ch_re_405_1 FROM CHUONG WHERE maKH=@kh_re_405 AND thuTu=1 LIMIT 1;
SELECT maChuong INTO @ch_re_405_2 FROM CHUONG WHERE maKH=@kh_re_405 AND thuTu=2 LIMIT 1;

-- Writing (4 chương)
SELECT maChuong INTO @ch_wr_405_1 FROM CHUONG WHERE maKH=@kh_wr_405 AND thuTu=1 LIMIT 1;
SELECT maChuong INTO @ch_wr_405_2 FROM CHUONG WHERE maKH=@kh_wr_405 AND thuTu=2 LIMIT 1;
SELECT maChuong INTO @ch_wr_405_3 FROM CHUONG WHERE maKH=@kh_wr_405 AND thuTu=3 LIMIT 1;
SELECT maChuong INTO @ch_wr_405_4 FROM CHUONG WHERE maKH=@kh_wr_405 AND thuTu=4 LIMIT 1;

-- =========================================================
-- 2) CHÈN DỮ LIỆU MINI-TESTS CHO BAND 405–600
--    Mỗi chương: 3 mini-tests + tài liệu PDF đề thi + 1 câu hỏi mẫu
-- =========================================================

-- ---------- LISTENING ----------
-- Chương 1: PART 1: Photographs → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_li_405,@ch_li_405_1,'Mini-test 1 – Listening Photographs','LISTENING',1,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_1,'Mini-test 2 – Listening Photographs','LISTENING',2,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_1,'Mini-test 3 – Listening Photographs','LISTENING',3,10.00,0.00,10,1,1,0);
SET @mt_li_405_1_1 := LAST_INSERT_ID(); SET @mt_li_405_1_2 := @mt_li_405_1_1+1; SET @mt_li_405_1_3 := @mt_li_405_1_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_li_405_1_1,'Đề Listening PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/minitest1.pdf')),

(@mt_li_405_1_2,'Đề Listening PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/minitest2.pdf')),

(@mt_li_405_1_3,'Đề Listening PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem,audio_url) VALUES
(@mt_li_405_1_1,1,'single_choice','Nghe và chọn mô tả đúng.','A. A man is holding a camera.','B. A woman is closing a window.','C. People are crossing the street.','D. A car is parked on the bridge.','A','Từ khóa trong audio.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/audio/q1.mp3')),

(@mt_li_405_1_2,1,'single_choice','Nghe và chọn đáp án đúng.','A. The chairs are folded.','B. The tables are covered.','C. The lights are turned off.','D. The shelves are empty.','C','Nhận biết “turned off”.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/audio/q2.mp3')),

(@mt_li_405_1_3,1,'single_choice','Nghe và chọn đáp án đúng.','A. He is boarding the bus.','B. He is fixing a tire.','C. He is paying the bill.','D. He is opening a drawer.','B','Từ khóa “fixing a tire”.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch1/audio/q3.mp3'));

-- Chương 2: PART 2: Question–Response → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_li_405,@ch_li_405_2,'Mini-test 1 – Question–Response','LISTENING',1,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_2,'Mini-test 2 – Question–Response','LISTENING',2,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_2,'Mini-test 3 – Question–Response','LISTENING',3,10.00,0.00,10,1,1,0);
SET @mt_li_405_2_1 := LAST_INSERT_ID(); SET @mt_li_405_2_2 := @mt_li_405_2_1+1; SET @mt_li_405_2_3 := @mt_li_405_2_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_li_405_2_1,'Đề Listening PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/minitest1.pdf')),

(@mt_li_405_2_2,'Đề Listening PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/minitest2.pdf')),

(@mt_li_405_2_3,'Đề Listening PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem,audio_url) VALUES
(@mt_li_405_2_1,1,'single_choice','Nghe câu hỏi và chọn câu trả lời phù hợp.','A. Yes, I did.','B. At the office.','C. Tomorrow morning.','D. No, thank you.','A','Phù hợp với câu hỏi Yes/No.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/audio/q1.mp3')),

(@mt_li_405_2_2,1,'single_choice','Nghe và chọn đáp án đúng.','A. On the desk.','B. Its raining.','C. By car.','D. Next week.','C','Phù hợp với How',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/audio/q2.mp3')),

(@mt_li_405_2_3,1,'single_choice','Nghe và chọn đáp án đúng.','A. She is busy.','B. In the meeting room.','C. Two hours ago.','D. I don\t know.','D','Phù hợp với câu hỏi mở.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch2/audio/q3.mp3'));

-- Chương 3: PART 3: Short Conversations → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_li_405,@ch_li_405_3,'Mini-test 1 – Short Conversations','LISTENING',1,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_3,'Mini-test 2 – Short Conversations','LISTENING',2,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_3,'Mini-test 3 – Short Conversations','LISTENING',3,10.00,0.00,10,1,1,0);
SET @mt_li_405_3_1 := LAST_INSERT_ID(); SET @mt_li_405_3_2 := @mt_li_405_3_1+1; SET @mt_li_405_3_3 := @mt_li_405_3_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_li_405_3_1,'Đề Listening PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/minitest1.pdf')),

(@mt_li_405_3_2,'Đề Listening PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/minitest2.pdf')),

(@mt_li_405_3_3,'Đề Listening PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem,audio_url) VALUES
(@mt_li_405_3_1,1,'single_choice','What are they discussing?','A. A new project.','B. Lunch plans.','C. Weather.','D. Traffic.','A','Ý chính hội thoại.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/audio/q1.mp3')),

(@mt_li_405_3_2,1,'single_choice','Where is the conversation taking place?','A. In a store.','B. At a restaurant.','C. In an office.','D. On the phone.','C','Ngữ cảnh.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/audio/q2.mp3')),

(@mt_li_405_3_3,1,'single_choice','What will the man do next?','A. Send an email.','B. Make a call.','C. Attend a meeting.','D. Leave early.','B','Hành động tiếp theo.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch3/audio/q3.mp3'));

-- Chương 4: PART 4: Short Talks → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_li_405,@ch_li_405_4,'Mini-test 1 – Short Talks','LISTENING',1,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_4,'Mini-test 2 – Short Talks','LISTENING',2,10.00,0.00,10,1,1,0),
(@kh_li_405,@ch_li_405_4,'Mini-test 3 – Short Talks','LISTENING',3,10.00,0.00,10,1,1,0);
SET @mt_li_405_4_1 := LAST_INSERT_ID(); SET @mt_li_405_4_2 := @mt_li_405_4_1+1; SET @mt_li_405_4_3 := @mt_li_405_4_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_li_405_4_1,'Đề Listening PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/minitest1.pdf')),

(@mt_li_405_4_2,'Đề Listening PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/minitest2.pdf')),

(@mt_li_405_4_3,'Đề Listening PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem,audio_url) VALUES
(@mt_li_405_4_1,1,'single_choice','What is the announcement about?','A. A sale.','B. Weather update.','C. Event cancellation.','D. New product.','C','Chủ đề chính.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/audio/q1.mp3')),

(@mt_li_405_4_2,1,'single_choice','Who is the speaker?','A. A manager.','B. A news reporter.','C. A tour guide.','D. A customer.','B','Vai trò người nói.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/audio/q2.mp3')),

(@mt_li_405_4_3,1,'single_choice','When will the event start?','A. At 9 AM.','B. Tomorrow.','C. Next week.','D. In two hours.','A','Thời gian cụ thể.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/listening/ch4/audio/q3.mp3'));

-- ---------- SPEAKING ----------
-- Chương 1: Read a Text Aloud → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_sp_405,@ch_sp_405_1,'Mini-test 1 – Read a Text Aloud','SPEAKING',1,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_1,'Mini-test 2 – Read a Text Aloud','SPEAKING',2,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_1,'Mini-test 3 – Read a Text Aloud','SPEAKING',3,10.00,0.00,5,1,1,0);
SET @mt_sp_405_1_1 := LAST_INSERT_ID(); SET @mt_sp_405_1_2 := @mt_sp_405_1_1+1; SET @mt_sp_405_1_3 := @mt_sp_405_1_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_sp_405_1_1,'Đề Speaking PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/minitest1.pdf')),

(@mt_sp_405_1_2,'Đề Speaking PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/minitest2.pdf')),

(@mt_sp_405_1_3,'Đề Speaking PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_sp_405_1_1,1,'essay','Read the provided text aloud and record your response as an audio file (45-60 seconds). Submit the audio for teacher grading.','Focus on pronunciation, intonation, and fluency. Teacher will provide feedback.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/text1.pdf')),

(@mt_sp_405_1_2,1,'essay','Record yourself reading the given passage aloud (45-60 seconds). Upload the audio file for evaluation by the teacher.','Emphasize natural rhythm and clarity. Graded manually on accuracy.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/text2.pdf')),

(@mt_sp_405_1_3,1,'essay','Read the text provided and submit an audio recording of your reading (45-60 seconds). The teacher will grade it.','Pay attention to stress and pacing. Feedback via teacher review.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch1/text3.pdf'));

-- Chương 2: Describe a Picture → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_sp_405,@ch_sp_405_2,'Mini-test 1 – Describe a Picture','SPEAKING',1,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_2,'Mini-test 2 – Describe a Picture','SPEAKING',2,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_2,'Mini-test 3 – Describe a Picture','SPEAKING',3,10.00,0.00,5,1,1,0);
SET @mt_sp_405_2_1 := LAST_INSERT_ID(); SET @mt_sp_405_2_2 := @mt_sp_405_2_1+1; SET @mt_sp_405_2_3 := @mt_sp_405_2_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_sp_405_2_1,'Đề Speaking PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/minitest1.pdf')),

(@mt_sp_405_2_2,'Đề Speaking PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/minitest2.pdf')),

(@mt_sp_405_2_3,'Đề Speaking PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_sp_405_2_1,1,'essay','Describe the picture in detail and record your spoken response as an audio file (45-60 seconds). Submit for teacher grading.','Use descriptive language and structure your response. Teacher feedback on vocabulary and coherence.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture1.pdf')),

(@mt_sp_405_2_2,1,'essay','Record an audio description of the provided image (45-60 seconds). Upload the file for manual grading by the teacher.','Focus on key details and organization. Graded on fluency and accuracy.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture2.pdf')),

(@mt_sp_405_2_3,1,'essay','Submit an audio recording describing the photo (45-60 seconds). The teacher will evaluate and provide feedback.','Be vivid; include positions and actions. Manual review for pronunciation.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture3.pdf'));

-- Chương 3: Respond to Questions → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_sp_405,@ch_sp_405_3,'Mini-test 1 – Respond to Questions','SPEAKING',1,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_3,'Mini-test 2 – Respond to Questions','SPEAKING',2,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_3,'Mini-test 3 – Respond to Questions','SPEAKING',3,10.00,0.00,5,1,1,0);
SET @mt_sp_405_3_1 := LAST_INSERT_ID(); SET @mt_sp_405_3_2 := @mt_sp_405_3_1+1; SET @mt_sp_405_3_3 := @mt_sp_405_3_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_sp_405_3_1,'Đề Speaking PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch3/minitest1.pdf')),

(@mt_sp_405_3_2,'Đề Speaking PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch3/minitest2.pdf')),

(@mt_sp_405_3_3,'Đề Speaking PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch3/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_sp_405_3_1,1,'essay','Describe the picture in detail and record your spoken response as an audio file (45-60 seconds). Submit for teacher grading.','Use descriptive language and structure your response. Teacher feedback on vocabulary and coherence.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture1.pdf')),

(@mt_sp_405_3_2,1,'essay','Record an audio description of the provided image (45-60 seconds). Upload the file for manual grading by the teacher.','Focus on key details and organization. Graded on fluency and accuracy.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture2.pdf')),

(@mt_sp_405_3_3,1,'essay','Submit an audio recording describing the photo (45-60 seconds). The teacher will evaluate and provide feedback.','Be vivid; include positions and actions. Manual review for pronunciation.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture3.pdf'));

-- Chương 4: Respond to Questions Using Information Provided → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_sp_405,@ch_sp_405_4,'Mini-test 1 – Respond Using Info','SPEAKING',1,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_4,'Mini-test 2 – Respond Using Info','SPEAKING',2,10.00,0.00,5,1,1,0),
(@kh_sp_405,@ch_sp_405_4,'Mini-test 3 – Respond Using Info','SPEAKING',3,10.00,0.00,5,1,1,0);
SET @mt_sp_405_4_1 := LAST_INSERT_ID(); SET @mt_sp_405_4_2 := @mt_sp_405_4_1+1; SET @mt_sp_405_4_3 := @mt_sp_405_4_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_sp_405_4_1,'Đề Speaking PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch4/minitest1.pdf')),

(@mt_sp_405_4_2,'Đề Speaking PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch4/minitest2.pdf')),

(@mt_sp_405_4_3,'Đề Speaking PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch4/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_sp_405_4_1,1,'essay','Describe the picture in detail and record your spoken response as an audio file (45-60 seconds). Submit for teacher grading.','Use descriptive language and structure your response. Teacher feedback on vocabulary and coherence.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture1.pdf')),

(@mt_sp_405_4_2,1,'essay','Record an audio description of the provided image (45-60 seconds). Upload the file for manual grading by the teacher.','Focus on key details and organization. Graded on fluency and accuracy.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture2.pdf')),

(@mt_sp_405_4_3,1,'essay','Submit an audio recording describing the photo (45-60 seconds). The teacher will evaluate and provide feedback.','Be vivid; include positions and actions. Manual review for pronunciation.','10.00',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/speaking/ch2/picture3.pdf'));

-- ---------- READING ----------
-- Chương 1: PART 5–6: Incomplete Sentences → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_re_405,@ch_re_405_1,'Mini-test 1 – Incomplete Sentences','READING',1,10.00,0.00,10,1,1,0),
(@kh_re_405,@ch_re_405_1,'Mini-test 2 – Incomplete Sentences','READING',2,10.00,0.00,10,1,1,0),
(@kh_re_405,@ch_re_405_1,'Mini-test 3 – Incomplete Sentences','READING',3,10.00,0.00,10,1,1,0);
SET @mt_re_405_1_1 := LAST_INSERT_ID(); SET @mt_re_405_1_2 := @mt_re_405_1_1+1; SET @mt_re_405_1_3 := @mt_re_405_1_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_re_405_1_1,'Đề Reading PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch1/minitest1.pdf')),

(@mt_re_405_1_2,'Đề Reading PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch1/minitest2.pdf')),

(@mt_re_405_1_3,'Đề Reading PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch1/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem) VALUES
(@mt_re_405_1_1,1,'single_choice','The report was _____ yesterday.','A. submit','B. submitted','C. submitting','D. submits','B','Passive voice.',10.00),
(@mt_re_405_1_2,1,'single_choice','She is the _____ employee.','A. good','B. better','C. best','D. well','C','Superlative.',10.00),
(@mt_re_405_1_3,1,'single_choice','We need to _____ the budget.','A. approve','B. approved','C. approving','D. approval','A','Verb form.',10.00);

-- Chương 2: PART 7: Vocabulary & Reading Comprehension → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_re_405,@ch_re_405_2,'Mini-test 1 – Reading Comprehension','READING',1,10.00,0.00,10,1,1,0),
(@kh_re_405,@ch_re_405_2,'Mini-test 2 – Reading Comprehension','READING',2,10.00,0.00,10,1,1,0),
(@kh_re_405,@ch_re_405_2,'Mini-test 3 – Reading Comprehension','READING',3,10.00,0.00,10,1,1,0);
SET @mt_re_405_2_1 := LAST_INSERT_ID(); SET @mt_re_405_2_2 := @mt_re_405_2_1+1; SET @mt_re_405_2_3 := @mt_re_405_2_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_re_405_2_1,'Đề Reading PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch2/minitest1.pdf')),

(@mt_re_405_2_2,'Đề Reading PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch2/minitest2.pdf')),

(@mt_re_405_2_3,'Đề Reading PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/reading/ch2/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,phuongAnA,phuongAnB,phuongAnC,phuongAnD,dapAnDung,giaiThich,diem) VALUES
(@mt_re_405_2_1,1,'single_choice','What is the purpose of the email?','A. To complain.','B. To inform.','C. To request.','D. To advertise.','B','Main purpose.',10.00),
(@mt_re_405_2_2,1,'single_choice','Where is the event?','A. In the park.','B. At the office.','C. Online.','D. At the hotel.','D','Location detail.',10.00),
(@mt_re_405_2_3,1,'single_choice','What does the word mean?','A. Definition A.','B. Definition B.','C. Definition C.','D. Definition D.','C','Vocabulary context.',10.00);

-- ---------- WRITING ----------
-- Chương 1: Express an Opinion → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_wr_405,@ch_wr_405_1,'Mini-test 1 – Express an Opinion','WRITING',1,10.00,0.00,20,1,1,0),
(@kh_wr_405,@ch_wr_405_1,'Mini-test 2 – Express an Opinion','WRITING',2,10.00,0.00,20,1,1,0),
(@kh_wr_405,@ch_wr_405_1,'Mini-test 3 – Express an Opinion','WRITING',3,10.00,0.00,20,1,1,0);
SET @mt_wr_405_1_1 := LAST_INSERT_ID(); SET @mt_wr_405_1_2 := @mt_wr_405_1_1+1; SET @mt_wr_405_1_3 := @mt_wr_405_1_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_wr_405_1_1,'Đề Writing PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/minitest1.pdf')),

(@mt_wr_405_1_2,'Đề Writing PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/minitest2.pdf')),

(@mt_wr_405_1_3,'Đề Writing PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_wr_405_1_1,1,'essay','Express your opinion on remote work (100-120 words).','Support with reasons.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/guideline1.pdf')),

(@mt_wr_405_1_2,1,'essay','Give your view on social media.','Balanced argument.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/guideline2.pdf')),

(@mt_wr_405_1_3,1,'essay','Opinion on environmental issues.','Use examples.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch1/guideline3.pdf'));

-- Chương 2: Write a Sentence Based on a Picture → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_wr_405,@ch_wr_405_2,'Mini-test 1 – Sentence Based on Picture','WRITING',1,10.00,0.00,10,1,1,0),
(@kh_wr_405,@ch_wr_405_2,'Mini-test 2 – Sentence Based on Picture','WRITING',2,10.00,0.00,10,1,1,0),
(@kh_wr_405,@ch_wr_405_2,'Mini-test 3 – Sentence Based on Picture','WRITING',3,10.00,0.00,10,1,1,0);
SET @mt_wr_405_2_1 := LAST_INSERT_ID(); SET @mt_wr_405_2_2 := @mt_wr_405_2_1+1; SET @mt_wr_405_2_3 := @mt_wr_405_2_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_wr_405_2_1,'Đề Writing PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/minitest1.pdf')),

(@mt_wr_405_2_2,'Đề Writing PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/minitest2.pdf')),

(@mt_wr_405_2_3,'Đề Writing PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_wr_405_2_1,1,'essay','Write a sentence based on the picture.','Accurate description.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/picture1.pdf')),

(@mt_wr_405_2_2,1,'essay','Describe the image in one sentence.','Use correct grammar.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/picture2.pdf')),

(@mt_wr_405_2_3,1,'essay','Form a sentence from the photo.','Be descriptive.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch2/picture3.pdf'));

-- Chương 3: Respond to a Written Request → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_wr_405,@ch_wr_405_3,'Mini-test 1 – Respond to Request','WRITING',1,10.00,0.00,20,1,1,0),
(@kh_wr_405,@ch_wr_405_3,'Mini-test 2 – Respond to Request','WRITING',2,10.00,0.00,20,1,1,0),
(@kh_wr_405,@ch_wr_405_3,'Mini-test 3 – Respond to Request','WRITING',3,10.00,0.00,20,1,1,0);
SET @mt_wr_405_3_1 := LAST_INSERT_ID(); SET @mt_wr_405_3_2 := @mt_wr_405_3_1+1; SET @mt_wr_405_3_3 := @mt_wr_405_3_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_wr_405_3_1,'Đề Writing PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/minitest1.pdf')),

(@mt_wr_405_3_2,'Đề Writing PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/minitest2.pdf')),

(@mt_wr_405_3_3,'Đề Writing PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_wr_405_3_1,1,'essay','Respond to the email request (100-120 words).','Polite and complete.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/request1.pdf')),

(@mt_wr_405_3_2,1,'essay','Write a response to the written request.','Address all points.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/request2.pdf')),

(@mt_wr_405_3_3,1,'essay','Reply to the given request.','Formal tone.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch3/request3.pdf'));

-- Chương 4: Write an Opinion Essay → 3 mini-tests
INSERT INTO CHUONG_MINITEST
(maKH,maChuong,title,skill_type,thuTu,max_score,trongSo,time_limit_min,attempts_allowed,is_active,is_published) VALUES
(@kh_wr_405,@ch_wr_405_4,'Mini-test 1 – Opinion Essay','WRITING',1,10.00,0.00,30,1,1,0),
(@kh_wr_405,@ch_wr_405_4,'Mini-test 2 – Opinion Essay','WRITING',2,10.00,0.00,30,1,1,0),
(@kh_wr_405,@ch_wr_405_4,'Mini-test 3 – Opinion Essay','WRITING',3,10.00,0.00,30,1,1,0);
SET @mt_wr_405_4_1 := LAST_INSERT_ID(); SET @mt_wr_405_4_2 := @mt_wr_405_4_1+1; SET @mt_wr_405_4_3 := @mt_wr_405_4_1+2;

INSERT INTO MINITEST_TAILIEU (maMT,tenTL,loai,mime_type,visibility,public_url) VALUES
(@mt_wr_405_4_1,'Đề Writing PDF - 1','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/minitest1.pdf')),

(@mt_wr_405_4_2,'Đề Writing PDF - 2','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/minitest2.pdf')),

(@mt_wr_405_4_3,'Đề Writing PDF - 3','PDF','application/pdf','public',
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/minitest3.pdf'));

INSERT INTO MINITEST_QUESTIONS
(maMT,thuTu,loai,noiDungCauHoi,giaiThich,diem,pdf_url) VALUES
(@mt_wr_405_4_1,1,'essay','Write an opinion essay on technology (200 words).','Introduction, body, conclusion.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/topic1.pdf')),

(@mt_wr_405_4_2,1,'essay','Essay on education reform.','Support with examples.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/topic2.pdf')),

(@mt_wr_405_4_3,1,'essay','Opinion essay on globalization.','Coherent structure.',10.00,
CONCAT(@R2_BASE_PUBLIC,'/mini-tests/405-600/writing/ch4/topic3.pdf'));

COMMIT;
