USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 7) CHƯƠNG (Đã cập nhật mô tả)
-- 7.1 Nói - Viết
-- =========================================================
INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Read a Text Aloud', 1, 'Hướng dẫn kỹ thuật phát âm, nhấn trọng âm và ngữ điệu để đọc một văn bản tiếng Anh một cách trôi chảy và tự nhiên.');
SET @ch_nv_1 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Describe a Picture', 2, 'Phát triển kỹ năng quan sát và sử dụng từ vựng phong phú để miêu tả sinh động một bức ảnh trong thời gian giới hạn.');
SET @ch_nv_2 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Respond to Questions', 3, 'Nắm vững kỹ thuật trả lời các câu hỏi ngắn một cách mạch lạc, tự nhiên và đúng trọng tâm.');
SET @ch_nv_3 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Respond to Questions Using Information Provided', 4, 'Rèn luyện kỹ năng đọc hiểu và tổng hợp thông tin từ các tài liệu cho trước để trả lời câu hỏi một cách chính xác.');
SET @ch_nv_4 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Express an Opinion', 5, 'Học cách xây dựng lập luận, sắp xếp ý tưởng và trình bày quan điểm cá nhân về một chủ đề một cách rõ ràng, logic và thuyết phục.');
SET @ch_nv_5 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Write a Sentence Based on a Picture', 6, 'Thực hành kỹ năng viết câu hoàn chỉnh và đúng ngữ pháp dựa trên một bức ảnh, sử dụng từ vựng phù hợp với ngữ cảnh.');
SET @ch_nv_6 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Respond to a Written Request', 7, 'Nâng cao kỹ năng viết email chuyên nghiệp, học cách phản hồi các yêu cầu công việc một cách hiệu quả, rõ ràng và lịch sự.');
SET @ch_nv_7 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_noiviet, 'Write an Opinion Essay', 8, 'Hướng dẫn chi tiết cách viết một bài luận trình bày quan điểm, từ việc xây dựng dàn ý, phát triển luận điểm đến việc đưa ra dẫn chứng.');
SET @ch_nv_8 := LAST_INSERT_ID();

-- =========================================================
-- 7) CHƯƠNG (Đã cập nhật mô tả)
-- 7.2 Nghe - Đọc 
-- =========================================================
INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 1: Photographs', 1, 'Luyện kỹ năng nghe-hiểu các mô tả ngắn về hình ảnh và chọn ra đáp án chính xác nhất, tránh các bẫy thường gặp.');
SET @ch_nd_1 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 2: Question–Response', 2, 'Tăng cường khả năng phản xạ nghe-hiểu các dạng câu hỏi và lựa chọn câu trả lời phù hợp nhất về ngữ cảnh và ý nghĩa.');
SET @ch_nd_2 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 3: Short Conversations', 3, 'Rèn luyện kỹ năng nghe-hiểu các đoạn hội thoại ngắn, tập trung vào việc xác định ý chính, chi tiết và suy luận.');
SET @ch_nd_3 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 4: Short Talks', 4, 'Nắm bắt thông tin then chốt từ các bài nói ngắn như thông báo, tin nhắn thoại, bản tin và trả lời các câu hỏi liên quan.');
SET @ch_nd_4 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 5–6: Incomplete Sentences', 5, 'Củng cố kiến thức ngữ pháp và từ vựng trọng tâm để hoàn thành câu và đoạn văn một cách chính xác.');
SET @ch_nd_5 := LAST_INSERT_ID();

INSERT INTO CHUONG (maKH, tenChuong, thuTu, moTa) VALUES
(@kh_nghedoc, 'PART 7: Vocabulary & Reading Comprehension', 6, 'Phát triển kỹ năng đọc-hiểu chuyên sâu qua các dạng văn bản (email, quảng cáo, bài báo) và xử lý nhiều đoạn văn cùng lúc.');
SET @ch_nd_6 := LAST_INSERT_ID();

-- =========================================================
-- 8) BÀI HỌC (Đã cập nhật mô tả)
-- 8.1 Nói - Viết
-- =========================================================
-- Chương 1
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_1, 'Read a Text Aloud 1', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 1, 'video');
SET @bh_nv_1_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_1, 'Read a Text Aloud 2', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 2, 'video');
SET @bh_nv_1_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_1, 'Read a Text Aloud 3', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 3, 'video');
SET @bh_nv_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_2, 'Describe a Picture 1', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 1, 'video');
SET @bh_nv_2_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_2, 'Describe a Picture 2', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 2, 'video');
SET @bh_nv_2_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_2, 'Describe a Picture 3', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 3, 'video');
SET @bh_nv_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_3, 'Respond to Questions 1', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 1, 'video');
SET @bh_nv_3_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_3, 'Respond to Questions 2', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 2, 'video');
SET @bh_nv_3_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_3, 'Respond to Questions 3', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 3, 'video');
SET @bh_nv_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_4, 'Respond to Questions Using Information 1', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 1, 'video');
SET @bh_nv_4_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_4, 'Respond to Questions Using Information 2', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 2, 'video');
SET @bh_nv_4_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_4, 'Respond to Questions Using Information 3', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 3, 'video');
SET @bh_nv_4_3 := LAST_INSERT_ID();

-- Chương 5
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_5, 'Express an Opinion 1', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 1, 'video');
SET @bh_nv_5_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_5, 'Express an Opinion 2', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 2, 'video');
SET @bh_nv_5_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_5, 'Express an Opinion 3', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 3, 'video');
SET @bh_nv_5_3 := LAST_INSERT_ID();

-- Chương 6
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_6, 'Write a Sentence Based on a Picture 1', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 1, 'video');
SET @bh_nv_6_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_6, 'Write a Sentence Based on a Picture 2', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 2, 'video');
SET @bh_nv_6_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_6, 'Write a Sentence Based on a Picture 3', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 3, 'video');
SET @bh_nv_6_3 := LAST_INSERT_ID();

-- Chương 7
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_7, 'Respond to Written Resquest 1', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 1, 'video');
SET @bh_nv_7_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_7, 'Respond to Written Resquest 2', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 2, 'video');
SET @bh_nv_7_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_7, 'Respond to Written Resquest 3', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 3, 'video');
SET @bh_nv_7_3 := LAST_INSERT_ID();

-- Chương 8
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_8, 'Write an Opinion Essay 1', 'Hướng dẫn cách lên dàn ý chi tiết cho một bài luận trình bày quan điểm.', 1, 'video');
SET @bh_nv_8_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_8, 'Write an Opinion Essay 2', 'Phát triển luận điểm, đưa ra ví dụ và dẫn chứng để bài viết có sức thuyết phục.', 2, 'video');
SET @bh_nv_8_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nv_8, 'Write an Opinion Essay 3', 'Thực hành viết một bài luận hoàn chỉnh và các tiêu chí tự đánh giá.', 3, 'video');
SET @bh_nv_8_3 := LAST_INSERT_ID();

-- =========================================================
-- 8) BÀI HỌC (Đã cập nhật mô tả)
-- 8.2 Nghe - Đọc
-- =========================================================
-- Chương 1
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_1, 'Photographs 1', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 1, 'video');
SET @bh_nd_1_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_1, 'Photographs 2', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 2, 'video');
SET @bh_nd_1_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_1, 'Photographs 3', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 3, 'video');
SET @bh_nd_1_3 := LAST_INSERT_ID();

-- Chương 2
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_2, 'Question – Response 1', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 1, 'video');
SET @bh_nd_2_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_2, 'Question – Response 2', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 2, 'quiz');
SET @bh_nd_2_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_2, 'Question – Response 3', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 3, 'video');
SET @bh_nd_2_3 := LAST_INSERT_ID();

-- Chương 3
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_3, 'Short Conversations 1', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 1, 'video');
SET @bh_nd_3_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_3, 'Short Conversations 2', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 2, 'video');
SET @bh_nd_3_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_3, 'Short Conversations 3', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 3, 'video');
SET @bh_nd_3_3 := LAST_INSERT_ID();

-- Chương 4
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_4, 'Short Talks 1', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 1, 'video');
SET @bh_nd_4_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_4, 'Short Talks 2', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 2, 'video');
SET @bh_nd_4_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_4, 'Short Talks 3', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 3, 'video');
SET @bh_nd_4_3 := LAST_INSERT_ID();

-- Chương 5 - 6
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_5, 'Incomplete Sentences 1', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 1, 'video');
SET @bh_nd_5_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_5, 'Incomplete Sentences 2', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 2, 'video');
SET @bh_nd_5_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_5, 'Incomplete Sentences 3', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 3, 'video');
SET @bh_nd_5_3 := LAST_INSERT_ID();

-- Chương 7
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_6, 'Vocabulary & Reading Comprhension Practice 1', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 1, 'video');
SET @bh_nd_6_1 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_6, 'Vocabulary & Reading Comprhension Practice 2', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 2, 'video');
SET @bh_nd_6_2 := LAST_INSERT_ID();
INSERT INTO BAIHOC (maChuong, tieuDe, moTa, thuTu, loai) VALUES
(@ch_nd_6, 'Vocabulary & Reading Comprhension Practice 3', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 3, 'video');
SET @bh_nd_6_3 := LAST_INSERT_ID();

COMMIT;