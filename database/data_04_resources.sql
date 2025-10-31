USE Online_Certificate_Classroom;

START TRANSACTION;

-- =========================================================
-- 9) TÀI LIỆU HỌC TẬP (Cloudflare R2)
-- =========================================================
SET @R2_BASE_PUBLIC := 'https://pub-9b3a3b8712d849d7b4e15e85e6beca8d.r2.dev';

-- =========================================================
-- Band 405-600 SPEAKING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES
-- Chương 1 - N1. Read a Text Aloud
-- Bài 1
(@bh_sp_405_600_1_1, 'Read a Text Aloud 1 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_405_600_1_1, 'Read a Text Aloud 1 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 2
(@bh_sp_405_600_1_2, 'Read a Text Aloud 2 (Video)', 'Video', '300MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai2/BaiGiang2_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_405_600_1_2, 'Read a Text Aloud 2 (PDF)', 'PDF', '10MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai2/TaiLieuBai2_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 3
(@bh_sp_405_600_1_3, 'Read a Text Aloud 3 (Video)', 'Video', '300MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai3/BaiGiang3_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_405_600_1_3, 'Read a Text Aloud 3 (PDF)', 'PDF', '10MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai3/TaiLieuBai3_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Chương 2 - N2. Describe a Picture
-- Bài 1
(@bh_sp_405_600_2_1, 'Describe a Picture 1 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai1/BaiGiang1_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_405_600_2_1, 'Describe a Picture 1 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai1/TaiLieuBai1_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 2
(@bh_sp_405_600_2_2, 'Describe a Picture 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai2/BaiGiang2_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_405_600_2_2, 'Describe a Picture 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai2/TaiLieuBai2_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 3
(@bh_sp_405_600_2_3, 'Describe a Picture 3 (Video)', 'Video', '350MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai3/BaiGiang3_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_405_600_2_3, 'Describe a Picture 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai3/TaiLieuBai3_N2.%20Describe%20a%20Picture.pdf')),

-- Chương 3 - N3. Respond to Questions
-- Bài 1
(@bh_sp_405_600_3_1, 'Respond to Questions 1 (Video)', 'Video', '250MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai1/BaiGiang1_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_3_1, 'Respond to Questions 1 (PDF)', 'PDF', '10MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai1/TaiLieuBai1_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 2
(@bh_sp_405_600_3_2, 'Respond to Questions 2 (Video)', 'Video', '250MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai2/BaiGiang2_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_3_2, 'Respond to Questions 2 (PDF)', 'PDF', '10MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai2/TaiLieuBai2_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 3
(@bh_sp_405_600_3_3, 'Respond to Questions 3 (Video)', 'Video', '250MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_3_3, 'Respond to Questions 3 (PDF)', 'PDF', '10MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai3/TaiLieuBai3_N3.%20Respond%20to%20Questions.pdf')),

-- Chương 4 - N4. Respond to Questions Using Information Provided
-- Bài 1
(@bh_sp_405_600_4_1, 'Respond to Questions Using Information 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_4_1, 'Respond to Questions Using Information 1 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai1/TaiLieuBai1_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_sp_405_600_4_2, 'Respond to Questions Using Information 2 (Video)', 'Video', '350MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_4_2, 'Respond to Questions Using Information 2 (PDF)', 'PDF', '10MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai2/TaiLieuBai2_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_sp_405_600_4_3, 'Respond to Questions Using Information 3 (Video)', 'Video', '350MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_405_600_4_3, 'Respond to Questions Using Information 3 (PDF)', 'PDF', '10MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 405-600 WRITING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES
-- Chương 1 - V1. Express an Opinion
-- Bài 1
(@bh_wr_405_600_1_1, 'Express an Opinion 1 (Video)', 'Video', '300MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_1_1, 'Express an Opinion 1 (PDF)', 'PDF', '10MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_405_600_1_2, 'Express an Opinion 2 (Video)', 'Video', '300MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_1_2, 'Express an Opinion 2 (PDF)', 'PDF', '10MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_405_600_1_3, 'Express an Opinion 3 (Video)', 'Video', '300MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_1_3, 'Express an Opinion 3 (PDF)', 'PDF', '10MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - V2. Write a Sentence Based on a Picture
-- Bài 1
(@bh_wr_405_600_2_1, 'Write a Sentence Based on a Picture 1 (Video)', 'Video', '300MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_2_1, 'Write a Sentence Based on a Picture 1 (PDF)', 'PDF', '10MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_405_600_2_2, 'Write a Sentence Based on a Picture 2 (Video)', 'Video', '300MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_2_2, 'Write a Sentence Based on a Picture 2 (PDF)', 'PDF', '10MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_405_600_2_3, 'Write a Sentence Based on a Picture 3 (Video)', 'Video', '300MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_2_3, 'Write a Sentence Based on a Picture 3 (PDF)', 'PDF', '10MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 3 - V3. Respond to a Written Request
-- Bài 1
(@bh_wr_405_600_3_1, 'Respond to Written Request 1 (Video)', 'Video', '300MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_3_1, 'Respond to Written Request 1 (PDF)', 'PDF', '10MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_405_600_3_2, 'Respond to Written Request 2 (Video)', 'Video', '300MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_3_2, 'Respond to Written Request 2 (PDF)', 'PDF', '10MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_405_600_3_3, 'Respond to Written Request 3 (Video)', 'Video', '300MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_wr_405_600_3_3, 'Respond to Written Request 3 (PDF)', 'PDF', '10MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 405-600 LISTENING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 1. Photographs
-- Bài 1
(@bh_li_405_600_1_1, 'Photographs 1 (Video)', 'Video', '350MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_1_1, 'Photographs 1 (PDF)', 'PDF', '10MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_1_1, 'Photographs 1 Audio 1', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.1%20P1%20B1.mp3')),

(@bh_li_405_600_1_1, 'Photographs 1 Audio 2', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.2%20P1%20B1.mp3')),

(@bh_li_405_600_1_1, 'Photographs 1 Audio 3', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.3%20P1%20B1.mp3')),

-- Bài 2
(@bh_li_405_600_1_2, 'Photographs 2 (Video)', 'Video', '350MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_1_2, 'Photographs 2 (PDF)', 'PDF', '10MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_1_2, 'Photographs 2 Audio 1', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.1%20P1%20B2.mp3')),

(@bh_li_405_600_1_2, 'Photographs 2 Audio 2', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.2%20P1%20B2.mp3')),

(@bh_li_405_600_1_2, 'Photographs 2 Audio 3', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.3%20P1%20B2.mp3')),

-- Bài 3
(@bh_li_405_600_1_3, 'Photographs 3 (Video)', 'Video', '350MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_1_3, 'Photographs 3 (PDF)', 'PDF', '10MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_1_3, 'Photographs 3 Audio 1', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_405_600_1_3, 'Photographs 3 Audio 2', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.2%20P1%20B3.mp3')),

(@bh_li_405_600_1_3, 'Photographs 3 Audio 3', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.3%20P1%20B3.mp3')),

-- Chương 2 - PART 2. Question–Response
-- Bài 1
(@bh_li_405_600_2_1, 'Question – Response 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_2_1, 'Question – Response 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_2_1, 'Question – Response 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_405_600_2_1, 'Question – Response 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai1/mp3.2%20P2%20B1.mp3')),

(@bh_li_405_600_2_1, 'Question – Response 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

-- Bài 2
(@bh_li_405_600_2_2, 'Question – Response 2 (Video)', 'Video', '350MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_2_2, 'Question – Response 2 (PDF)', 'PDF', '10MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_2_2, 'Question – Response 2 Audio 1', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_405_600_2_2, 'Question – Response 2 Audio 2', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai2/mp3.2%20P2%20B2.mp3')),

(@bh_li_405_600_2_2, 'Question – Response 2 Audio 3', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

-- Bài 3
(@bh_li_405_600_2_3, 'Question – Response 3 (Video)', 'Video', '350MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_2_3, 'Question – Response 3 (PDF)', 'PDF', '10MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_2_3, 'Question – Response 3 Audio 1', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_405_600_2_3, 'Question – Response 3 Audio 2', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai3/mp3.2%20P2%20B3.mp3')),

(@bh_li_405_600_2_3, 'Question – Response 3 Audio 3', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

-- Chương 3 - PART 3. Short Conversations
-- Bài 1
(@bh_li_405_600_3_1, 'Short Conversations 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_3_1, 'Short Conversations 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_3_1, 'Short Conversations 1 Audio 1', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.1%20P3%20B1.mp3')),

(@bh_li_405_600_3_1, 'Short Conversations 1 Audio 2', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.2%20P3%20B1.mp3')),

(@bh_li_405_600_3_1, 'Short Conversations 1 Audio 3', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.3%20P3%20B1.mp3')),

-- Bài 2
(@bh_li_405_600_3_2, 'Short Conversations 2 (Video)', 'Video', '350MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_3_2, 'Short Conversations 2 (PDF)', 'PDF', '10MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_3_2, 'Short Conversations 2 Audio 1', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.1%20P3%20B2.mp3')),

(@bh_li_405_600_3_2, 'Short Conversations 2 Audio 2', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.2%20P3%20B2.mp3')),

(@bh_li_405_600_3_2, 'Short Conversations 2 Audio 3', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.3%20P3%20B2.mp3')),

-- Bài 3
(@bh_li_405_600_3_3, 'Short Conversations 3 (Video)', 'Video', '350MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_3_3, 'Short Conversations 3 (PDF)', 'PDF', '10MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_3_3, 'Short Conversations 3 Audio 1', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.1%20P3%20B3.mp3')),

(@bh_li_405_600_3_3, 'Short Conversations 3 Audio 2', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.2%20P3%20B3.mp3')),

(@bh_li_405_600_3_3, 'Short Conversations 3 Audio 3', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.3%20P3%20B3.mp3')),

-- Chương 4 - PART 4. Short Talks
-- Bài 1
(@bh_li_405_600_4_1, 'Short Talks 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_4_1, 'Short Talks 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_4_1, 'Short Talks 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.1%20P4%20B1.mp3')),

(@bh_li_405_600_4_1, 'Short Talks 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.2%20P4%20B1.mp3')),

(@bh_li_405_600_4_1, 'Short Talks 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.3%20P4%20B1.mp3')),

-- Bài 2
(@bh_li_405_600_4_2, 'Short Talks 2 (Video)', 'Video', '350MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_4_2, 'Short Talks 2 (PDF)', 'PDF', '10MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_4_2, 'Short Talks 2 Audio 1', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.1%20P4%20B2.mp3')),

(@bh_li_405_600_4_2, 'Short Talks 2 Audio 2', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.2%20P4%20B2.mp3')),

(@bh_li_405_600_4_2, 'Short Talks 2 Audio 3', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.3%20P4%20B2.mp3')),

-- Bài 3
(@bh_li_405_600_4_3, 'Short Talks 3 (Video)', 'Video', '350MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_li_405_600_4_3, 'Short Talks 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_405_600_4_3, 'Short Talks 3 Audio 1', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.1%20P4%20B3.mp3')),

(@bh_li_405_600_4_3, 'Short Talks 3 Audio 2', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_405_600_4_3, 'Short Talks 3 Audio 3', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3'));

-- =========================================================
-- Band 405-600 READING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 5 - 6. Incomplete Sentences
-- Bài 1
(@bh_re_405_600_1_1, 'Incomplete Sentences 1 (Video)', 'Video', '350MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_405_600_1_1, 'Incomplete Sentences 1 (PDF)', 'PDF', '10MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_405_600_1_2, 'Incomplete Sentences 2 (Video)', 'Video', '350MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_405_600_1_2, 'Incomplete Sentences 2 (PDF)', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_re_405_600_1_2, 'Incomplete Sentences 2 PDF 1', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_re_405_600_1_2, 'Incomplete Sentences 2 PDF 2', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_405_600_1_3, 'Incomplete Sentences 3 (Video)', 'Video', '350MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai3/C%C3%A2u%20gi%E1%BA%A3%20%C4%91%E1%BB%8Bnh.mp4')),

(@bh_re_405_600_1_3, 'Incomplete Sentences 3 (PDF)', 'PDF', '10MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - PART 7. Vocabulary & Reading Comprehension Practice
-- Bài 1
(@bh_re_405_600_2_1, 'Vocabulary & Reading Comprehension Practice 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20&%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_re_405_600_2_1, 'Vocabulary & Reading Comprehension Practice 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_405_600_2_2, 'Vocabulary & Reading Comprehension Practice 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20&%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_re_405_600_2_2, 'Vocabulary & Reading Comprehension Practice 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_405_600_2_3, 'Vocabulary & Reading Comprehension Practice 3 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20&%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_re_405_600_2_3, 'Vocabulary & Reading Comprehension Practice 3 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 605-780 SPEAKING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - N1. Read a Text Aloud
-- Bài 1
(@bh_sp_605_780_1_1, 'Read a Text Aloud 1 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_1_1, 'Read a Text Aloud 1 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 2
(@bh_sp_605_780_1_2, 'Read a Text Aloud 2 (Video)', 'Video', '300MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai2/BaiGiang2_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_1_2, 'Read a Text Aloud 2 (PDF)', 'PDF', '10MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai2/TaiLieuBai2_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 3
(@bh_sp_605_780_1_3, 'Read a Text Aloud 3 (Video)', 'Video', '300MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai3/BaiGiang3_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_1_3, 'Read a Text Aloud 3 (PDF)', 'PDF', '10MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai3/TaiLieuBai3_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Chương 2 - N2. Describe a Picture
-- Bài 1
(@bh_sp_605_780_2_1, 'Describe a Picture 1 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai1/BaiGiang1_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_605_780_2_1, 'Describe a Picture 1 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai1/TaiLieuBai1_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 2
(@bh_sp_605_780_2_2, 'Describe a Picture 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai2/BaiGiang2_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_605_780_2_2, 'Describe a Picture 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai2/TaiLieuBai2_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 3
(@bh_sp_605_780_2_3, 'Describe a Picture 3 (Video)', 'Video', '350MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai3/BaiGiang3_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_605_780_2_3, 'Describe a Picture 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai3/TaiLieuBai3_N2.%20Describe%20a%20Picture.pdf')),

-- Chương 3 - N3. Respond to Questions
-- Bài 1
(@bh_sp_605_780_3_1, 'Respond to Questions 1 (Video)', 'Video', '250MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai1/BaiGiang1_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_605_780_3_1, 'Respond to Questions 1 (PDF)', 'PDF', '10MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai1/TaiLieuBai1_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 2
(@bh_sp_605_780_3_2, 'Respond to Questions 2 (Video)', 'Video', '250MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai2/BaiGiang2_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_605_780_3_2, 'Respond to Questions 2 (PDF)', 'PDF', '10MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai2/TaiLieuBai2_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 3
(@bh_sp_605_780_3_3, 'Respond to Questions 3 (Video)', 'Video', '250MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_605_780_3_3, 'Respond to Questions 3 (PDF)', 'PDF', '10MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai3/TaiLieuBai3_N3.%20Respond%20to%20Questions.pdf')),

-- Chương 4 - N4. Respond to Questions Using Information Provided
-- Bài 1
(@bh_sp_605_780_4_1, 'Respond to Questions Using Information 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_4_1, 'Respond to Questions Using Information 1 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai1/TaiLieuBai1_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_sp_605_780_4_2, 'Respond to Questions Using Information 2 (Video)', 'Video', '350MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_4_2, 'Respond to Questions Using Information 2 (PDF)', 'PDF', '10MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai2/TaiLieuBai2_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_sp_605_780_4_3, 'Respond to Questions Using Information 3 (Video)', 'Video', '350MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_605_780_4_3, 'Respond to Questions Using Information 3 (PDF)', 'PDF', '10MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 605-780 WRITING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - V1. Express an Opinion
-- Bài 1
(@bh_wr_605_780_1_1, 'Express an Opinion 1 (Video)', 'Video', '300MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_1_1, 'Express an Opinion 1 (PDF)', 'PDF', '10MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_605_780_1_2, 'Express an Opinion 2 (Video)', 'Video', '300MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_1_2, 'Express an Opinion 2 (PDF)', 'PDF', '10MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_605_780_1_3, 'Express an Opinion 3 (Video)', 'Video', '300MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_1_3, 'Express an Opinion 3 (PDF)', 'PDF', '10MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - V2. Write a Sentence Based on a Picture
-- Bài 1
(@bh_wr_605_780_2_1, 'Write a Sentence Based on a Picture 1 (Video)', 'Video', '300MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_2_1, 'Write a Sentence Based on a Picture 1 (PDF)', 'PDF', '10MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_605_780_2_2, 'Write a Sentence Based on a Picture 2 (Video)', 'Video', '300MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_2_2, 'Write a Sentence Based on a Picture 2 (PDF)', 'PDF', '10MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_605_780_2_3, 'Write a Sentence Based on a Picture 3 (Video)', 'Video', '300MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_2_3, 'Write a Sentence Based on a Picture 3 (PDF)', 'PDF', '10MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),
-- Chương 3 - V3. Respond to a Written Request

-- Bài 1
(@bh_wr_605_780_3_1, 'Respond to Written Request 1 (Video)', 'Video', '300MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_3_1, 'Respond to Written Request 1 (PDF)', 'PDF', '10MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_605_780_3_2, 'Respond to Written Request 2 (Video)', 'Video', '300MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_3_2, 'Respond to Written Request 2 (PDF)', 'PDF', '10MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_605_780_3_3, 'Respond to Written Request 3 (Video)', 'Video', '300MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_605_780_3_3, 'Respond to Written Request 3 (PDF)', 'PDF', '10MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 605-780 LISTENING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 1. Photographs
-- Bài 1
(@bh_li_605_780_1_1, 'Photographs 1 (Video)', 'Video', '350MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_1_1, 'Photographs 1 (PDF)', 'PDF', '10MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_1_1, 'Photographs 1 Audio 1', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.1%20P1%20B1.mp3')),

(@bh_li_605_780_1_1, 'Photographs 1 Audio 2', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.2%20P1%20B1.mp3')),

(@bh_li_605_780_1_1, 'Photographs 1 Audio 3', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.3%20P1%20B1.mp3')),

-- Bài 2
(@bh_li_605_780_1_2, 'Photographs 2 (Video)', 'Video', '350MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_1_2, 'Photographs 2 (PDF)', 'PDF', '10MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_1_2, 'Photographs 2 Audio 1', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.1%20P1%20B2.mp3')),

(@bh_li_605_780_1_2, 'Photographs 2 Audio 2', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.2%20P1%20B2.mp3')),

(@bh_li_605_780_1_2, 'Photographs 2 Audio 3', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.3%20P1%20B2.mp3')),

-- Bài 3
(@bh_li_605_780_1_3, 'Photographs 3 (Video)', 'Video', '350MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_1_3, 'Photographs 3 (PDF)', 'PDF', '10MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_1_3, 'Photographs 3 Audio 1', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_605_780_1_3, 'Photographs 3 Audio 2', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.2%20P1%20B3.mp3')),

(@bh_li_605_780_1_3, 'Photographs 3 Audio 3', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.3%20P1%20B3.mp3')),

-- Chương 2 - PART 2. Question–Response
-- Bài 1
(@bh_li_605_780_2_1, 'Question – Response 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_2_1, 'Question – Response 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_2_1, 'Question – Response 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_605_780_2_1, 'Question – Response 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai1/mp3.2%20P2%20B1.mp3')),

(@bh_li_605_780_2_1, 'Question – Response 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

-- Bài 2
(@bh_li_605_780_2_2, 'Question – Response 2 (Video)', 'Video', '350MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_2_2, 'Question – Response 2 (PDF)', 'PDF', '10MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_2_2, 'Question – Response 2 Audio 1', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_605_780_2_2, 'Question – Response 2 Audio 2', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai2/mp3.2%20P2%20B2.mp3')),

(@bh_li_605_780_2_2, 'Question – Response 2 Audio 3', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

-- Bài 3
(@bh_li_605_780_2_3, 'Question – Response 3 (Video)', 'Video', '350MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_2_3, 'Question – Response 3 (PDF)', 'PDF', '10MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_2_3, 'Question – Response 3 Audio 1', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_605_780_2_3, 'Question – Response 3 Audio 2', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai3/mp3.2%20P2%20B3.mp3')),

(@bh_li_605_780_2_3, 'Question – Response 3 Audio 3', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

-- Chương 3 - PART 3. Short Conversations
-- Bài 1
(@bh_li_605_780_3_1, 'Short Conversations 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_3_1, 'Short Conversations 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_3_1, 'Short Conversations 1 Audio 1', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.1%20P3%20B1.mp3')),

(@bh_li_605_780_3_1, 'Short Conversations 1 Audio 2', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.2%20P3%20B1.mp3')),

(@bh_li_605_780_3_1, 'Short Conversations 1 Audio 3', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.3%20P3%20B1.mp3')),

-- Bài 2
(@bh_li_605_780_3_2, 'Short Conversations 2 (Video)', 'Video', '350MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_3_2, 'Short Conversations 2 (PDF)', 'PDF', '10MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_3_2, 'Short Conversations 2 Audio 1', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.1%20P3%20B2.mp3')),

(@bh_li_605_780_3_2, 'Short Conversations 2 Audio 2', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.2%20P3%20B2.mp3')),

(@bh_li_605_780_3_2, 'Short Conversations 2 Audio 3', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.3%20P3%20B2.mp3')),

-- Bài 3
(@bh_li_605_780_3_3, 'Short Conversations 3 (Video)', 'Video', '350MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_3_3, 'Short Conversations 3 (PDF)', 'PDF', '10MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_3_3, 'Short Conversations 3 Audio 1', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.1%20P3%20B3.mp3')),

(@bh_li_605_780_3_3, 'Short Conversations 3 Audio 2', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.2%20P3%20B3.mp3')),

(@bh_li_605_780_3_3, 'Short Conversations 3 Audio 3', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.3%20P3%20B3.mp3')),

-- Chương 4 - PART 4. Short Talks
-- Bài 1
(@bh_li_605_780_4_1, 'Short Talks 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_4_1, 'Short Talks 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_4_1, 'Short Talks 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.1%20P4%20B1.mp3')),

(@bh_li_605_780_4_1, 'Short Talks 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.2%20P4%20B1.mp3')),

(@bh_li_605_780_4_1, 'Short Talks 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.3%20P4%20B1.mp3')),

-- Bài 2
(@bh_li_605_780_4_2, 'Short Talks 2 (Video)', 'Video', '350MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_4_2, 'Short Talks 2 (PDF)', 'PDF', '10MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_4_2, 'Short Talks 2 Audio 1', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.1%20P4%20B2.mp3')),

(@bh_li_605_780_4_2, 'Short Talks 2 Audio 2', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.2%20P4%20B2.mp3')),

(@bh_li_605_780_4_2, 'Short Talks 2 Audio 3', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.3%20P4%20B2.mp3')),
-- Bài 3
(@bh_li_605_780_4_3, 'Short Talks 3 (Video)', 'Video', '350MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_605_780_4_3, 'Short Talks 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_605_780_4_3, 'Short Talks 3 Audio 1', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.1%20P4%20B3.mp3')),

(@bh_li_605_780_4_3, 'Short Talks 3 Audio 2', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_605_780_4_3, 'Short Talks 3 Audio 3', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3'));

-- =========================================================
-- Band 605-780 READING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 5 - 6. Incomplete Sentences
-- Bài 1
(@bh_re_605_780_1_1, 'Incomplete Sentences 1 (Video)', 'Video', '350MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_605_780_1_1, 'Incomplete Sentences 1 (PDF)', 'PDF', '10MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_605_780_1_2, 'Incomplete Sentences 2 (Video)', 'Video', '350MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_605_780_1_2, 'Incomplete Sentences 2 (PDF)', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_re_605_780_1_2, 'Incomplete Sentences 2 PDF 1', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/TaiLieuHocTap/Bai2/TaiLieuBai2.1_PART%205%E2%80%936.%20Incomplete%20Sentences.pdf')),

(@bh_re_605_780_1_2, 'Incomplete Sentences 2 PDF 2', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_605_780_1_3, 'Incomplete Sentences 3 (Video)', 'Video', '350MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai3/C%C3%A2u%20gi%E1%BA%A3%20%C4%91%E1%BB%8Bnh.mp4')),

(@bh_re_605_780_1_3, 'Incomplete Sentences 3 (PDF)', 'PDF', '10MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - PART 7. Vocabulary & Reading Comprehension Practice
-- Bài 1
(@bh_re_605_780_2_1, 'Vocabulary & Reading Comprehension Practice 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20&%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_re_605_780_2_1, 'Vocabulary & Reading Comprehension Practice 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_605_780_2_2, 'Vocabulary & Reading Comprehension Practice 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_605_780_2_2, 'Vocabulary & Reading Comprehension Practice 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_605_780_2_3, 'Vocabulary & Reading Comprehension Practice 3 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_605_780_2_3, 'Vocabulary & Reading Comprehension Practice 3 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));


-- =========================================================
-- Band 785-990 SPEAKING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - N1. Read a Text Aloud
-- Bài 1
(@bh_sp_785_990_1_1, 'Read a Text Aloud 1 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_1_1, 'Read a Text Aloud 1 (PDF)', 'PDF', '10MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 2
(@bh_sp_785_990_1_2, 'Read a Text Aloud 2 (Video)', 'Video', '300MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai2/BaiGiang2_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_1_2, 'Read a Text Aloud 2 (PDF)', 'PDF', '10MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai2/TaiLieuBai2_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 3
(@bh_sp_785_990_1_3, 'Read a Text Aloud 3 (Video)', 'Video', '300MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai3/BaiGiang3_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_1_3, 'Read a Text Aloud 3 (PDF)', 'PDF', '10MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai3/TaiLieuBai3_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Chương 2 - N2. Describe a Picture
-- Bài 1
(@bh_sp_785_990_2_1, 'Describe a Picture 1 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai1/BaiGiang1_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_785_990_2_1, 'Describe a Picture 1 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai1/TaiLieuBai1_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 2
(@bh_sp_785_990_2_2, 'Describe a Picture 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai2/BaiGiang2_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_785_990_2_2, 'Describe a Picture 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai2/TaiLieuBai2_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 3
(@bh_sp_785_990_2_3, 'Describe a Picture 3 (Video)', 'Video', '350MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai3/BaiGiang3_N2.%20Describe%20a%20Picture.mp4')),

(@bh_sp_785_990_2_3, 'Describe a Picture 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai3/TaiLieuBai3_N2.%20Describe%20a%20Picture.pdf')),

-- Chương 3 - N3. Respond to Questions
-- Bài 1
(@bh_sp_785_990_3_1, 'Respond to Questions 1 (Video)', 'Video', '250MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai1/BaiGiang1_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_785_990_3_1, 'Respond to Questions 1 (PDF)', 'PDF', '10MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai1/TaiLieuBai1_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 2
(@bh_sp_785_990_3_2, 'Respond to Questions 2 (Video)', 'Video', '250MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai2/BaiGiang2_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_785_990_3_2, 'Respond to Questions 2 (PDF)', 'PDF', '10MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai2/TaiLieuBai2_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 3
(@bh_sp_785_990_3_3, 'Respond to Questions 3 (Video)', 'Video', '250MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_sp_785_990_3_3, 'Respond to Questions 3 (PDF)', 'PDF', '10MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai3/TaiLieuBai3_N3.%20Respond%20to%20Questions.pdf')),

-- Chương 4 - N4. Respond to Questions Using Information Provided
-- Bài 1
(@bh_sp_785_990_4_1, 'Respond to Questions Using Information 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_4_1, 'Respond to Questions Using Information 1 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai1/TaiLieuBai1_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_sp_785_990_4_2, 'Respond to Questions Using Information 2 (Video)', 'Video', '350MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_4_2, 'Respond to Questions Using Information 2 (PDF)', 'PDF', '10MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai2/TaiLieuBai2_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_sp_785_990_4_3, 'Respond to Questions Using Information 3 (Video)', 'Video', '350MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_sp_785_990_4_3, 'Respond to Questions Using Information 3 (PDF)', 'PDF', '10MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 785-990 WRITING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - V1. Express an Opinion
-- Bài 1
(@bh_wr_785_990_1_1, 'Express an Opinion 1 (Video)', 'Video', '300MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_1_1, 'Express an Opinion 1 (PDF)', 'PDF', '10MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_785_990_1_2, 'Express an Opinion 2 (Video)', 'Video', '300MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_1_2, 'Express an Opinion 2 (PDF)', 'PDF', '10MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_785_990_1_3, 'Express an Opinion 3 (Video)', 'Video', '300MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_1_3, 'Express an Opinion 3 (PDF)', 'PDF', '10MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - V2. Write a Sentence Based on a Picture
-- Bài 1
(@bh_wr_785_990_2_1, 'Write a Sentence Based on a Picture 1 (Video)', 'Video', '300MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_2_1, 'Write a Sentence Based on a Picture 1 (PDF)', 'PDF', '10MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_785_990_2_2, 'Write a Sentence Based on a Picture 2 (Video)', 'Video', '300MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_2_2, 'Write a Sentence Based on a Picture 2 (PDF)', 'PDF', '10MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_785_990_2_3, 'Write a Sentence Based on a Picture 3 (Video)', 'Video', '300MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_2_3, 'Write a Sentence Based on a Picture 3 (PDF)', 'PDF', '10MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 3 - V3. Respond to a Written Request
-- Bài 1
(@bh_wr_785_990_3_1, 'Respond to Written Request 1 (Video)', 'Video', '300MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_3_1, 'Respond to Written Request 1 (PDF)', 'PDF', '10MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_wr_785_990_3_2, 'Respond to Written Request 2 (Video)', 'Video', '300MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_3_2, 'Respond to Written Request 2 (PDF)', 'PDF', '10MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_wr_785_990_3_3, 'Respond to Written Request 3 (Video)', 'Video', '300MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_wr_785_990_3_3, 'Respond to Written Request 3 (PDF)', 'PDF', '10MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

-- =========================================================
-- Band 785-990 LISTENING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 1. Photographs
-- Bài 1
(@bh_li_785_990_1_1, 'Photographs 1 (Video)', 'Video', '350MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_1_1, 'Photographs 1 (PDF)', 'PDF', '10MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_1_1, 'Photographs 1 Audio 1', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.1%20P1%20B1.mp3')),

(@bh_li_785_990_1_1, 'Photographs 1 Audio 2', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.2%20P1%20B1.mp3')),

(@bh_li_785_990_1_1, 'Photographs 1 Audio 3', 'Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.3%20P1%20B1.mp3')),

-- Bài 2
(@bh_li_785_990_1_2, 'Photographs 2 (Video)', 'Video', '350MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_1_2, 'Photographs 2 (PDF)', 'PDF', '10MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_1_2, 'Photographs 2 Audio 1', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.1%20P1%20B2.mp3')),

(@bh_li_785_990_1_2, 'Photographs 2 Audio 2', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.2%20P1%20B2.mp3')),

(@bh_li_785_990_1_2, 'Photographs 2 Audio 3', 'Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.3%20P1%20B2.mp3')),

-- Bài 3
(@bh_li_785_990_1_3, 'Photographs 3 (Video)', 'Video', '350MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_1_3, 'Photographs 3 (PDF)', 'PDF', '10MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_1_3, 'Photographs 3 Audio 1', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_li_785_990_1_3, 'Photographs 3 Audio 2', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.2%20P1%20B3.mp3')),

(@bh_li_785_990_1_3, 'Photographs 3 Audio 3', 'Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.3%20P1%20B3.mp3')),

-- Chương 2 - PART 2. Question–Response
-- Bài 1
(@bh_li_785_990_2_1, 'Question – Response 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_2_1, 'Question – Response 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_2_1, 'Question – Response 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

(@bh_li_785_990_2_1, 'Question – Response 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai1/mp3.2%20P2%20B1.mp3')),

(@bh_li_785_990_2_1, 'Question – Response 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

-- Bài 2
(@bh_li_785_990_2_2, 'Question – Response 2 (Video)', 'Video', '350MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_2_2, 'Question – Response 2 (PDF)', 'PDF', '10MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_2_2, 'Question – Response 2 Audio 1', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

(@bh_li_785_990_2_2, 'Question – Response 2 Audio 2', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai2/mp3.2%20P2%20B2.mp3')),

(@bh_li_785_990_2_2, 'Question – Response 2 Audio 3', 'Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

-- Bài 3
(@bh_li_785_990_2_3, 'Question – Response 3 (Video)', 'Video', '350MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_2_3, 'Question – Response 3 (PDF)', 'PDF', '10MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_2_3, 'Question – Response 3 Audio 1', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

(@bh_li_785_990_2_3, 'Question – Response 3 Audio 2', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question–Response/Audio/Bai3/mp3.2%20P2%20B3.mp3')),

(@bh_li_785_990_2_3, 'Question – Response 3 Audio 3', 'Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

-- Chương 3 - PART 3. Short Conversations
-- Bài 1
(@bh_li_785_990_3_1, 'Short Conversations 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_3_1, 'Short Conversations 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_3_1, 'Short Conversations 1 Audio 1', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.1%20P3%20B1.mp3')),

(@bh_li_785_990_3_1, 'Short Conversations 1 Audio 2', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.2%20P3%20B1.mp3')),

(@bh_li_785_990_3_1, 'Short Conversations 1 Audio 3', 'Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.3%20P3%20B1.mp3')),

-- Bài 2
(@bh_li_785_990_3_2, 'Short Conversations 2 (Video)', 'Video', '350MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_3_2, 'Short Conversations 2 (PDF)', 'PDF', '10MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_3_2, 'Short Conversations 2 Audio 1', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.1%20P3%20B2.mp3')),

(@bh_li_785_990_3_2, 'Short Conversations 2 Audio 2', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.2%20P3%20B2.mp3')),

(@bh_li_785_990_3_2, 'Short Conversations 2 Audio 3', 'Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.3%20P3%20B2.mp3')),

-- Bài 3
(@bh_li_785_990_3_3, 'Short Conversations 3 (Video)', 'Video', '350MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_3_3, 'Short Conversations 3 (PDF)', 'PDF', '10MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_3_3, 'Short Conversations 3 Audio 1', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.1%20P3%20B3.mp3')),

(@bh_li_785_990_3_3, 'Short Conversations 3 Audio 2', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.2%20P3%20B3.mp3')),

(@bh_li_785_990_3_3, 'Short Conversations 3 Audio 3', 'Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.3%20P3%20B3.mp3')),

-- Chương 4 - PART 4. Short Talks
-- Bài 1
(@bh_li_785_990_4_1, 'Short Talks 1 (Video)', 'Video', '350MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_4_1, 'Short Talks 1 (PDF)', 'PDF', '10MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_4_1, 'Short Talks 1 Audio 1', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.1%20P4%20B1.mp3')),

(@bh_li_785_990_4_1, 'Short Talks 1 Audio 2', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.2%20P4%20B1.mp3')),

(@bh_li_785_990_4_1, 'Short Talks 1 Audio 3', 'Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.3%20P4%20B1.mp3')),

-- Bài 2
(@bh_li_785_990_4_2, 'Short Talks 2 (Video)', 'Video', '350MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_4_2, 'Short Talks 2 (PDF)', 'PDF', '10MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_4_2, 'Short Talks 2 Audio 1', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.1%20P4%20B2.mp3')),

(@bh_li_785_990_4_2, 'Short Talks 2 Audio 2', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.2%20P4%20B2.mp3')),

(@bh_li_785_990_4_2, 'Short Talks 2 Audio 3', 'Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.3%20P4%20B2.mp3')),

-- Bài 3
(@bh_li_785_990_4_3, 'Short Talks 3 (Video)', 'Video', '350MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_li_785_990_4_3, 'Short Talks 3 (PDF)', 'PDF', '10MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_li_785_990_4_3, 'Short Talks 3 Audio 1', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.1%20P4%20B3.mp3')),

(@bh_li_785_990_4_3, 'Short Talks 3 Audio 2', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_li_785_990_4_3, 'Short Talks 3 Audio 3', 'Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3'));

-- =========================================================
-- Band 785-990 READING
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES

-- Chương 1 - PART 5 - 6. Incomplete Sentences
-- Bài 1
(@bh_re_785_990_1_1, 'Incomplete Sentences 1 (Video)', 'Video', '350MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_785_990_1_1, 'Incomplete Sentences 1 (PDF)', 'PDF', '10MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_785_990_1_2, 'Incomplete Sentences 2 (Video)', 'Video', '350MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_785_990_1_2, 'Incomplete Sentences 2 (PDF)', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_re_785_990_1_2, 'Incomplete Sentences 2 PDF 1', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

(@bh_re_785_990_1_2, 'Incomplete Sentences 2 PDF 2', 'PDF', '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_785_990_1_3, 'Incomplete Sentences 3 (Video)', 'Video', '350MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai3/C%C3%A2u%20gi%E1%BA%A3%20%C4%91%E1%BB%8Bnh.mp4')),

(@bh_re_785_990_1_3, 'Incomplete Sentences 3 (PDF)', 'PDF', '10MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Chương 2 - PART 7. Vocabulary & Reading Comprehension Practice
-- Bài 1
(@bh_re_785_990_2_1, 'Vocabulary & Reading Comprehension Practice 1 (Video)', 'Video', '350MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20&%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_re_785_990_2_1, 'Vocabulary & Reading Comprehension Practice 1 (PDF)', 'PDF', '10MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_re_785_990_2_2, 'Vocabulary & Reading Comprehension Practice 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_785_990_2_2, 'Vocabulary & Reading Comprehension Practice 2 (PDF)', 'PDF', '10MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_re_785_990_2_3, 'Vocabulary & Reading Comprehension Practice 3 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205–6.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_re_785_990_2_3, 'Vocabulary & Reading Comprehension Practice 3 (PDF)', 'PDF', '10MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf'));

COMMIT;
