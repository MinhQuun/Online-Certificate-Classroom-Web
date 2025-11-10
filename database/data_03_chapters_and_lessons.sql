USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 7) CHƯƠNG
-- 7.1 Nói - Band 405-600
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_405_600, 'Read a Text Aloud', 1, 'Hướng dẫn kỹ thuật phát âm, nhấn trọng âm và ngữ điệu để đọc một văn bản tiếng Anh một cách trôi chảy và tự nhiên.');
SET @ch_sp_405_600_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_405_600, 'Describe a Picture', 2, 'Phát triển kỹ năng quan sát và sử dụng từ vựng phong phú để miêu tả sinh động một bức ảnh trong thời gian giới hạn.');
SET @ch_sp_405_600_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_405_600, 'Respond to Questions', 3, 'Nắm vững kỹ thuật trả lời các câu hỏi ngắn một cách mạch lạc, tự nhiên và đúng trọng tâm.');
SET @ch_sp_405_600_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_405_600, 'Respond to Questions Using Information Provided', 4, 'Rèn luyện kỹ năng đọc hiểu và tổng hợp thông tin từ các tài liệu cho trước để trả lời câu hỏi một cách chính xác.');
SET @ch_sp_405_600_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.2 Viết - Band 405-600
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_405_600, 'Express an Opinion', 1, 'Học cách xây dựng lập luận, sắp xếp ý tưởng và trình bày quan điểm cá nhân về một chủ đề một cách rõ ràng, logic và thuyết phục.');
SET @ch_wr_405_600_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_405_600, 'Write a Sentence Based on a Picture', 2, 'Thực hành kỹ năng viết câu hoàn chỉnh và đúng ngữ pháp dựa trên một bức ảnh, sử dụng từ vựng phù hợp với ngữ cảnh.');
SET @ch_wr_405_600_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_405_600, 'Respond to a Written Request', 3, 'Nâng cao kỹ năng viết email chuyên nghiệp, học cách phản hồi các yêu cầu công việc một cách hiệu quả, rõ ràng và lịch sự.');
SET @ch_wr_405_600_3 := LAST_INSERT_ID();

-- =========================================================
-- 7.3 Nghe - Band 405-600
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_405_600, 'PART 1: Photographs', 1, 'Luyện kỹ năng nghe-hiểu các mô tả ngắn về hình ảnh và chọn ra đáp án chính xác nhất, tránh các bẫy thường gặp.');
SET @ch_li_405_600_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_405_600, 'PART 2: Question–Response', 2, 'Tăng cường khả năng phản xạ nghe-hiểu các dạng câu hỏi và lựa chọn câu trả lời phù hợp nhất về ngữ cảnh và ý nghĩa.');
SET @ch_li_405_600_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_405_600, 'PART 3: Short Conversations', 3, 'Rèn luyện kỹ năng nghe-hiểu các đoạn hội thoại ngắn, tập trung vào việc xác định ý chính, chi tiết và suy luận.');
SET @ch_li_405_600_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_405_600, 'PART 4: Short Talks', 4, 'Nắm bắt thông tin then chốt từ các bài nói ngắn như thông báo, tin nhắn thoại, bản tin và trả lời các câu hỏi liên quan.');
SET @ch_li_405_600_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.4 Đọc - Band 405-600
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_405_600, 'PART 5–6: Incomplete Sentences', 1, 'Củng cố kiến thức ngữ pháp và từ vựng trọng tâm để hoàn thành câu và đoạn văn một cách chính xác.');
SET @ch_re_405_600_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_405_600, 'PART 7: Vocabulary & Reading Comprehension', 2, 'Phát triển kỹ năng đọc-hiểu chuyên sâu qua các dạng văn bản (email, quảng cáo, bài báo) và xử lý nhiều đoạn văn cùng lúc.');
SET @ch_re_405_600_2 := LAST_INSERT_ID();

-- =========================================================
-- 7.5 Nói - Band 605-780
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_605_780, 'Read a Text Aloud', 1, 'Hướng dẫn kỹ thuật phát âm, nhấn trọng âm và ngữ điệu để đọc một văn bản tiếng Anh một cách trôi chảy và tự nhiên.');
SET @ch_sp_605_780_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_605_780, 'Describe a Picture', 2, 'Phát triển kỹ năng quan sát và sử dụng từ vựng phong phú để miêu tả sinh động một bức ảnh trong thời gian giới hạn.');
SET @ch_sp_605_780_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_605_780, 'Respond to Questions', 3, 'Nắm vững kỹ thuật trả lời các câu hỏi ngắn một cách mạch lạc, tự nhiên và đúng trọng tâm.');
SET @ch_sp_605_780_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_605_780, 'Respond to Questions Using Information Provided', 4, 'Rèn luyện kỹ năng đọc hiểu và tổng hợp thông tin từ các tài liệu cho trước để trả lời câu hỏi một cách chính xác.');
SET @ch_sp_605_780_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.6 Viết - Band 605-780
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_605_780, 'Express an Opinion', 1, 'Học cách xây dựng lập luận, sắp xếp ý tưởng và trình bày quan điểm cá nhân về một chủ đề một cách rõ ràng, logic và thuyết phục.');
SET @ch_wr_605_780_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_605_780, 'Write a Sentence Based on a Picture', 2, 'Thực hành kỹ năng viết câu hoàn chỉnh và đúng ngữ pháp dựa trên một bức ảnh, sử dụng từ vựng phù hợp với ngữ cảnh.');
SET @ch_wr_605_780_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_605_780, 'Respond to a Written Request', 3, 'Nâng cao kỹ năng viết email chuyên nghiệp, học cách phản hồi các yêu cầu công việc một cách hiệu quả, rõ ràng và lịch sự.');
SET @ch_wr_605_780_3 := LAST_INSERT_ID();

-- =========================================================
-- 7.7 Nghe - Band 605-780
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_605_780, 'PART 1: Photographs', 1, 'Luyện kỹ năng nghe-hiểu các mô tả ngắn về hình ảnh và chọn ra đáp án chính xác nhất, tránh các bẫy thường gặp.');
SET @ch_li_605_780_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_605_780, 'PART 2: Question–Response', 2, 'Tăng cường khả năng phản xạ nghe-hiểu các dạng câu hỏi và lựa chọn câu trả lời phù hợp nhất về ngữ cảnh và ý nghĩa.');
SET @ch_li_605_780_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_605_780, 'PART 3: Short Conversations', 3, 'Rèn luyện kỹ năng nghe-hiểu các đoạn hội thoại ngắn, tập trung vào việc xác định ý chính, chi tiết và suy luận.');
SET @ch_li_605_780_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_605_780, 'PART 4: Short Talks', 4, 'Nắm bắt thông tin then chốt từ các bài nói ngắn như thông báo, tin nhắn thoại, bản tin và trả lời các câu hỏi liên quan.');
SET @ch_li_605_780_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.8 Đọc - Band 605-780
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_605_780, 'PART 5–6: Incomplete Sentences', 1, 'Củng cố kiến thức ngữ pháp và từ vựng trọng tâm để hoàn thành câu và đoạn văn một cách chính xác.');
SET @ch_re_605_780_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_605_780, 'PART 7: Vocabulary & Reading Comprehension', 2, 'Phát triển kỹ năng đọc-hiểu chuyên sâu qua các dạng văn bản (email, quảng cáo, bài báo) và xử lý nhiều đoạn văn cùng lúc.');
SET @ch_re_605_780_2 := LAST_INSERT_ID();

-- =========================================================
-- 7.9 Nói - Band 785-990
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_785_990, 'Read a Text Aloud', 1, 'Hướng dẫn kỹ thuật phát âm, nhấn trọng âm và ngữ điệu để đọc một văn bản tiếng Anh một cách trôi chảy và tự nhiên.');
SET @ch_sp_785_990_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_785_990, 'Describe a Picture', 2, 'Phát triển kỹ năng quan sát và sử dụng từ vựng phong phú để miêu tả sinh động một bức ảnh trong thời gian giới hạn.');
SET @ch_sp_785_990_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_785_990, 'Respond to Questions', 3, 'Nắm vững kỹ thuật trả lời các câu hỏi ngắn một cách mạch lạc, tự nhiên và đúng trọng tâm.');
SET @ch_sp_785_990_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_speaking_785_990, 'Respond to Questions Using Information Provided', 4, 'Rèn luyện kỹ năng đọc hiểu và tổng hợp thông tin từ các tài liệu cho trước để trả lời câu hỏi một cách chính xác.');
SET @ch_sp_785_990_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.10 Viết - Band 785-990
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_785_990, 'Express an Opinion', 1, 'Học cách xây dựng lập luận, sắp xếp ý tưởng và trình bày quan điểm cá nhân về một chủ đề một cách rõ ràng, logic và thuyết phục.');
SET @ch_wr_785_990_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_785_990, 'Write a Sentence Based on a Picture', 2, 'Thực hành kỹ năng viết câu hoàn chỉnh và đúng ngữ pháp dựa trên một bức ảnh, sử dụng từ vựng phù hợp với ngữ cảnh.');
SET @ch_wr_785_990_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_writing_785_990, 'Respond to a Written Request', 3, 'Nâng cao kỹ năng viết email chuyên nghiệp, học cách phản hồi các yêu cầu công việc một cách hiệu quả, rõ ràng và lịch sự.');
SET @ch_wr_785_990_3 := LAST_INSERT_ID();

-- =========================================================
-- 7.11 Nghe - Band 785-990
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_785_990, 'PART 1: Photographs', 1, 'Luyện kỹ năng nghe-hiểu các mô tả ngắn về hình ảnh và chọn ra đáp án chính xác nhất, tránh các bẫy thường gặp.');
SET @ch_li_785_990_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_785_990, 'PART 2: Question–Response', 2, 'Tăng cường khả năng phản xạ nghe-hiểu các dạng câu hỏi và lựa chọn câu trả lời phù hợp nhất về ngữ cảnh và ý nghĩa.');
SET @ch_li_785_990_2 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_785_990, 'PART 3: Short Conversations', 3, 'Rèn luyện kỹ năng nghe-hiểu các đoạn hội thoại ngắn, tập trung vào việc xác định ý chính, chi tiết và suy luận.');
SET @ch_li_785_990_3 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_listening_785_990, 'PART 4: Short Talks', 4, 'Nắm bắt thông tin then chốt từ các bài nói ngắn như thông báo, tin nhắn thoại, bản tin và trả lời các câu hỏi liên quan.');
SET @ch_li_785_990_4 := LAST_INSERT_ID();

-- =========================================================
-- 7.12 Đọc - Band 785-990
-- =========================================================
INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_785_990, 'PART 5–6: Incomplete Sentences', 1, 'Củng cố kiến thức ngữ pháp và từ vựng trọng tâm để hoàn thành câu và đoạn văn một cách chính xác.');
SET @ch_re_785_990_1 := LAST_INSERT_ID();

INSERT INTO chuong (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_reading_785_990, 'PART 7: Vocabulary & Reading Comprehension', 2, 'Phát triển kỹ năng đọc-hiểu chuyên sâu qua các dạng văn bản (email, quảng cáo, bài báo) và xử lý nhiều đoạn văn cùng lúc.');
SET @ch_re_785_990_2 := LAST_INSERT_ID();

-- =========================================================
-- 8) BÀI HỌC
-- 8.1 Speaking - Band 405-600
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_1, 'Read a Text Aloud 1', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 1, 'video');
SET @bh_sp_405_600_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_1, 'Read a Text Aloud 2', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 2, 'video');
SET @bh_sp_405_600_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_1, 'Read a Text Aloud 3', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 3, 'video');
SET @bh_sp_405_600_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_2, 'Describe a Picture 1', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 1, 'video');
SET @bh_sp_405_600_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_2, 'Describe a Picture 2', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 2, 'video');
SET @bh_sp_405_600_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_2, 'Describe a Picture 3', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 3, 'video');
SET @bh_sp_405_600_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_3, 'Respond to Questions 1', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 1, 'video');
SET @bh_sp_405_600_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_3, 'Respond to Questions 2', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 2, 'video');
SET @bh_sp_405_600_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_3, 'Respond to Questions 3', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 3, 'video');
SET @bh_sp_405_600_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_4, 'Respond to Questions Using Information 1', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 1, 'video');
SET @bh_sp_405_600_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_4, 'Respond to Questions Using Information 2', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 2, 'video');
SET @bh_sp_405_600_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_405_600_4, 'Respond to Questions Using Information 3', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 3, 'video');
SET @bh_sp_405_600_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.2 Writing - Band 405-600
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_1, 'Express an Opinion 1', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 1, 'video');
SET @bh_wr_405_600_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_1, 'Express an Opinion 2', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 2, 'video');
SET @bh_wr_405_600_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_1, 'Express an Opinion 3', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 3, 'video');
SET @bh_wr_405_600_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_2, 'Write a Sentence Based on a Picture 1', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 1, 'video');
SET @bh_wr_405_600_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_2, 'Write a Sentence Based on a Picture 2', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 2, 'video');
SET @bh_wr_405_600_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_2, 'Write a Sentence Based on a Picture 3', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 3, 'video');
SET @bh_wr_405_600_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_3, 'Respond to Written Request 1', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 1, 'video');
SET @bh_wr_405_600_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_3, 'Respond to Written Request 2', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 2, 'video');
SET @bh_wr_405_600_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_405_600_3, 'Respond to Written Request 3', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 3, 'video');
SET @bh_wr_405_600_3_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.3 Listening - Band 405-600
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_1, 'Photographs 1', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 1, 'video');
SET @bh_li_405_600_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_1, 'Photographs 2', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 2, 'video');
SET @bh_li_405_600_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_1, 'Photographs 3', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 3, 'video');
SET @bh_li_405_600_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_2, 'Question – Response 1', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 1, 'video');
SET @bh_li_405_600_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_2, 'Question – Response 2', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 2, 'video');
SET @bh_li_405_600_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_2, 'Question – Response 3', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 3, 'video');
SET @bh_li_405_600_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_3, 'Short Conversations 1', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 1, 'video');
SET @bh_li_405_600_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_3, 'Short Conversations 2', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 2, 'video');
SET @bh_li_405_600_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_3, 'Short Conversations 3', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 3, 'video');
SET @bh_li_405_600_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_4, 'Short Talks 1', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 1, 'video');
SET @bh_li_405_600_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_4, 'Short Talks 2', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 2, 'video');
SET @bh_li_405_600_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_405_600_4, 'Short Talks 3', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 3, 'video');
SET @bh_li_405_600_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.4 Reading - Band 405-600
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_1, 'Incomplete Sentences 1', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 1, 'video');
SET @bh_re_405_600_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_1, 'Incomplete Sentences 2', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 2, 'video');
SET @bh_re_405_600_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_1, 'Incomplete Sentences 3', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 3, 'video');
SET @bh_re_405_600_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_2, 'Vocabulary & Reading Comprehension Practice 1', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 1, 'video');
SET @bh_re_405_600_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_2, 'Vocabulary & Reading Comprehension Practice 2', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 2, 'video');
SET @bh_re_405_600_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_405_600_2, 'Vocabulary & Reading Comprehension Practice 3', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 3, 'video');
SET @bh_re_405_600_2_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.5 Speaking - Band 605-780
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_1, 'Read a Text Aloud 1', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 1, 'video');
SET @bh_sp_605_780_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_1, 'Read a Text Aloud 2', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 2, 'video');
SET @bh_sp_605_780_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_1, 'Read a Text Aloud 3', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 3, 'video');
SET @bh_sp_605_780_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_2, 'Describe a Picture 1', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 1, 'video');
SET @bh_sp_605_780_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_2, 'Describe a Picture 2', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 2, 'video');
SET @bh_sp_605_780_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_2, 'Describe a Picture 3', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 3, 'video');
SET @bh_sp_605_780_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_3, 'Respond to Questions 1', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 1, 'video');
SET @bh_sp_605_780_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_3, 'Respond to Questions 2', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 2, 'video');
SET @bh_sp_605_780_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_3, 'Respond to Questions 3', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 3, 'video');
SET @bh_sp_605_780_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_4, 'Respond to Questions Using Information 1', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 1, 'video');
SET @bh_sp_605_780_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_4, 'Respond to Questions Using Information 2', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 2, 'video');
SET @bh_sp_605_780_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_605_780_4, 'Respond to Questions Using Information 3', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 3, 'video');
SET @bh_sp_605_780_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.6 Writing - Band 605-780
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_1, 'Express an Opinion 1', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 1, 'video');
SET @bh_wr_605_780_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_1, 'Express an Opinion 2', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 2, 'video');
SET @bh_wr_605_780_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_1, 'Express an Opinion 3', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 3, 'video');
SET @bh_wr_605_780_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_2, 'Write a Sentence Based on a Picture 1', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 1, 'video');
SET @bh_wr_605_780_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_2, 'Write a Sentence Based on a Picture 2', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 2, 'video');
SET @bh_wr_605_780_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_2, 'Write a Sentence Based on a Picture 3', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 3, 'video');
SET @bh_wr_605_780_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_3, 'Respond to Written Request 1', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 1, 'video');
SET @bh_wr_605_780_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_3, 'Respond to Written Request 2', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 2, 'video');
SET @bh_wr_605_780_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_605_780_3, 'Respond to Written Request 3', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 3, 'video');
SET @bh_wr_605_780_3_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.7 Listening - Band 605-780
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_1, 'Photographs 1', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 1, 'video');
SET @bh_li_605_780_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_1, 'Photographs 2', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 2, 'video');
SET @bh_li_605_780_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_1, 'Photographs 3', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 3, 'video');
SET @bh_li_605_780_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_2, 'Question – Response 1', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 1, 'video');
SET @bh_li_605_780_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_2, 'Question – Response 2', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 2, 'video');
SET @bh_li_605_780_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_2, 'Question – Response 3', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 3, 'video');
SET @bh_li_605_780_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_3, 'Short Conversations 1', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 1, 'video');
SET @bh_li_605_780_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_3, 'Short Conversations 2', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 2, 'video');
SET @bh_li_605_780_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_3, 'Short Conversations 3', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 3, 'video');
SET @bh_li_605_780_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_4, 'Short Talks 1', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 1, 'video');
SET @bh_li_605_780_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_4, 'Short Talks 2', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 2, 'video');
SET @bh_li_605_780_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_605_780_4, 'Short Talks 3', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 3, 'video');
SET @bh_li_605_780_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.8 Reading - Band 605-780 (Tương tự band 405-600)
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_1, 'Incomplete Sentences 1', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 1, 'video');
SET @bh_re_605_780_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_1, 'Incomplete Sentences 2', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 2, 'video');
SET @bh_re_605_780_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_1, 'Incomplete Sentences 3', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 3, 'video');
SET @bh_re_605_780_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_2, 'Vocabulary & Reading Comprehension Practice 1', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 1, 'video');
SET @bh_re_605_780_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_2, 'Vocabulary & Reading Comprehension Practice 2', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 2, 'video');
SET @bh_re_605_780_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_605_780_2, 'Vocabulary & Reading Comprehension Practice 3', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 3, 'video');
SET @bh_re_605_780_2_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.9 Speaking - Band 785-990
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_1, 'Read a Text Aloud 1', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 1, 'video');
SET @bh_sp_785_990_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_1, 'Read a Text Aloud 2', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 2, 'video');
SET @bh_sp_785_990_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_1, 'Read a Text Aloud 3', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 3, 'video');
SET @bh_sp_785_990_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_2, 'Describe a Picture 1', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 1, 'video');
SET @bh_sp_785_990_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_2, 'Describe a Picture 2', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 2, 'video');
SET @bh_sp_785_990_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_2, 'Describe a Picture 3', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 3, 'video');
SET @bh_sp_785_990_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_3, 'Respond to Questions 1', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 1, 'video');
SET @bh_sp_785_990_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_3, 'Respond to Questions 2', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 2, 'video');
SET @bh_sp_785_990_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_3, 'Respond to Questions 3', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 3, 'video');
SET @bh_sp_785_990_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_4, 'Respond to Questions Using Information 1', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 1, 'video');
SET @bh_sp_785_990_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_4, 'Respond to Questions Using Information 2', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 2, 'video');
SET @bh_sp_785_990_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_sp_785_990_4, 'Respond to Questions Using Information 3', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 3, 'video');
SET @bh_sp_785_990_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.10 Writing - Band 785-990
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_1, 'Express an Opinion 1', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 1, 'video');
SET @bh_wr_785_990_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_1, 'Express an Opinion 2', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 2, 'video');
SET @bh_wr_785_990_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_1, 'Express an Opinion 3', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 3, 'video');
SET @bh_wr_785_990_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_2, 'Write a Sentence Based on a Picture 1', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 1, 'video');
SET @bh_wr_785_990_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_2, 'Write a Sentence Based on a Picture 2', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 2, 'video');
SET @bh_wr_785_990_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_2, 'Write a Sentence Based on a Picture 3', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 3, 'video');
SET @bh_wr_785_990_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_3, 'Respond to Written Request 1', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 1, 'video');
SET @bh_wr_785_990_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_3, 'Respond to Written Request 2', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 2, 'video');
SET @bh_wr_785_990_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_wr_785_990_3, 'Respond to Written Request 3', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 3, 'video');
SET @bh_wr_785_990_3_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.11 Listening - Band 785-990
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_1, 'Photographs 1', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 1, 'video');
SET @bh_li_785_990_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_1, 'Photographs 2', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 2, 'video');
SET @bh_li_785_990_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_1, 'Photographs 3', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 3, 'video');
SET @bh_li_785_990_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_2, 'Question – Response 1', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 1, 'video');
SET @bh_li_785_990_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_2, 'Question – Response 2', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 2, 'video');
SET @bh_li_785_990_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_2, 'Question – Response 3', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 3, 'video');
SET @bh_li_785_990_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_3, 'Short Conversations 1', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 1, 'video');
SET @bh_li_785_990_3_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_3, 'Short Conversations 2', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 2, 'video');
SET @bh_li_785_990_3_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_3, 'Short Conversations 3', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 3, 'video');
SET @bh_li_785_990_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_4, 'Short Talks 1', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 1, 'video');
SET @bh_li_785_990_4_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_4, 'Short Talks 2', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 2, 'video');
SET @bh_li_785_990_4_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_li_785_990_4, 'Short Talks 3', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 3, 'video');
SET @bh_li_785_990_4_3 := LAST_INSERT_ID();

-- =========================================================
-- 8.12 Reading - Band 785-990
-- =========================================================
-- Chương 1
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_1, 'Incomplete Sentences 1', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 1, 'video');
SET @bh_re_785_990_1_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_1, 'Incomplete Sentences 2', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 2, 'video');
SET @bh_re_785_990_1_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_1, 'Incomplete Sentences 3', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 3, 'video');
SET @bh_re_785_990_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_2, 'Vocabulary & Reading Comprehension Practice 1', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 1, 'video');
SET @bh_re_785_990_2_1 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_2, 'Vocabulary & Reading Comprehension Practice 2', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 2, 'video');
SET @bh_re_785_990_2_2 := LAST_INSERT_ID();

INSERT INTO baihoc (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_re_785_990_2, 'Vocabulary & Reading Comprehension Practice 3', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 3, 'video');
SET @bh_re_785_990_2_3 := LAST_INSERT_ID();

COMMIT;
