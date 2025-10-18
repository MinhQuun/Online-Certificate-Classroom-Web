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
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES
-- Chương 1 - Read a Text Aloud
-- Bài 1 
(@bh_nv_1_1, 'Read a Text Aloud 1 (Video)', 'Video', '300MB', 'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai1/BaiGiang1_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_nv_1_1, 'Read a Text Aloud 1 (PDF)',   'PDF',   '10MB',  'Giới thiệu về trọng âm từ, trọng âm câu và ngữ điệu cơ bản để đọc tự nhiên.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai1/TaiLieuBai1_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 2
(@bh_nv_1_2, 'Read a Text Aloud 2 (Video)', 'Video', '300MB', 'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai2/BaiGiang2_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_nv_1_2, 'Read a Text Aloud 2 (PDF)',   'PDF',   '10MB',  'Thực hành các mẫu câu phổ biến để làm quen với ngữ điệu lên và xuống trong tiếng Anh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai2/TaiLieuBai2_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Bài 3
(@bh_nv_1_3, 'Read a Text Aloud 3 (Video)', 'Video', '300MB', 'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/BaiGiang/Bai3/BaiGiang3_N1.%20Read%20a%20Text%20Aloud.mp4')),

(@bh_nv_1_3, 'Read a Text Aloud 3 (PDF)',   'PDF',   '10MB',  'Giải đáp các thắc mắc thường gặp và mẹo thực hành để cải thiện kỹ năng đọc thành tiếng.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N1.%20Read%20a%20Text%20Aloud/TaiLieuHocTap/Bai3/TaiLieuBai3_N1.%20Read%20a%20Text%20Aloud.pdf')),

-- Chương 2 - Describe a Picture
-- Bài 1
(@bh_nv_2_1, 'Describe a Picture 1 (Video)', 'Video', '350MB', 'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai1/BaiGiang1_N2.%20Describe%20a%20Picture.mp4')),

(@bh_nv_2_1, 'Describe a Picture 1 (PDF)',   'PDF',   '10MB',  'Chiến lược quản lý thời gian và xây dựng cấu trúc câu cơ bản để miêu tả một bức ảnh.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai1/TaiLieuBai1_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 2
(@bh_nv_2_2, 'Describe a Picture 2 (Video)', 'Video', '350MB', 'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai2/BaiGiang2_N2.%20Describe%20a%20Picture.mp4')),

(@bh_nv_2_2, 'Describe a Picture 2 (PDF)',   'PDF',   '10MB',  'Hướng dẫn cách thêm các chi tiết về vị trí, hành động, và đối tượng để bài miêu tả sinh động hơn.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai2/TaiLieuBai2_N2.%20Describe%20a%20Picture.pdf')),

-- Bài 3
(@bh_nv_2_3, 'Describe a Picture 3 (Video)', 'Video', '350MB', 'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/BaiGiang/Bai3/BaiGiang3_N2.%20Describe%20a%20Picture.mp4')),
(@bh_nv_2_3, 'Describe a Picture 3 (PDF)',   'PDF',   '10MB',  'Thực hành với các bài tập mô phỏng phần Đọc và Miêu tả ảnh để làm quen với áp lực phòng thi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N2.%20Describe%20a%20Picture/TaiLieuHocTap/Bai3/TaiLieuBai3_N2.%20Describe%20a%20Picture.pdf')),


-- Chương 3 - Respond to Questions
-- Bài 1
(@bh_nv_3_1, 'Respond to Questions 1 (Video)', 'Video', '250MB', 'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai1/BaiGiang1_N3.%20Respond%20to%20Questions.mp4')),

(@bh_nv_3_1, 'Respond to Questions 1 (PDF)',   'PDF',   '10MB',  'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai1/TaiLieuBai1_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 2
(@bh_nv_3_2, 'Respond to Questions 2 (Video)', 'Video', '250MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai2/BaiGiang2_N3.%20Respond%20to%20Questions.mp4')),

(@bh_nv_3_2, 'Respond to Questions 2 (PDF)',   'PDF',   '10MB',  'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai2/TaiLieuBai2_N3.%20Respond%20to%20Questions.pdf')),

-- Bài 3
(@bh_nv_3_3, 'Respond to Questions 3 (Video)', 'Video', '250MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/BaiGiang/Bai3/BaiGiang3_N3.%20Respond%20to%20Questions.mp4')),

(@bh_nv_3_3, 'Respond to Questions 3 (PDF)',   'PDF',   '10MB',  'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N3.%20Respond%20to%20Questions/TaiLieuHocTap/Bai3/TaiLieuBai3_N3.%20Respond%20to%20Questions.pdf')),


-- Chương 4 - Respond to Questions Using Information
-- Bài 1
(@bh_nv_4_1, 'Respond to Questions Using Information 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/BaiGiang/Bai1/BaiGiang1_N4.%20Respond%20to%20Questions%20Using%20Infomation%20Provided.mp4')),

(@bh_nv_4_1, 'Respond to Questions Using Information 1 (PDF)',   'PDF',   '10MB',  'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai1/TaiLieuBai1_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 2
(@bh_nv_4_2, 'Respond to Questions Using Information 2 (Video)', 'Video', '350MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/BaiGiang/Bai2/BaiGiang2_N4.%20Respond%20to%20Questions%20Using%20Infomation%20Provided.mp4')),

(@bh_nv_4_2, 'Respond to Questions Using Information 2 (PDF)',   'PDF',   '10MB',  'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai2/TaiLieuBai2_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),

-- Bài 3
(@bh_nv_4_3, 'Respond to Questions Using Information 3 (Video)', 'Video', '350MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/BaiGiang/Bai3/BaiGiang3_N4.%20Respond%20to%20Questions%20Using%20Infomation%20Provided.mp4')),

(@bh_nv_4_3, 'Respond to Questions Using Information 3 (PDF)',   'PDF',   '10MB',  'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N4.%20Respond%20to%20Questions%20Using%20Information%20Provided/TaiLieuHocTap/Bai3/TaiLieuBai3_N4.%20Respond%20to%20Questions%20Using%20Information%20Provided.pdf')),


-- Chương 5 - Express an Opinion
-- Bài 1
(@bh_nv_5_1, 'Express an Opinion 1 (Video)', 'Video', '450MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/BaiGiang/Bai1/BaiGiang1_N5.%20Express%20an%20Opinion.mp4')),

(@bh_nv_5_1, 'Express an Opinion 1 (PDF)',   'PDF',   '10MB',  'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/TaiLieuHocTap/Bai1/TaiLieuBai1_N5.%20Express%20an%20Opinion.pdf')),

-- Bài 2
(@bh_nv_5_2, 'Express an Opinion 2 (Video)', 'Video', '450MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/BaiGiang/Bai2/BaiGiang2_N5.%20Express%20an%20Opinion.mp4')),

(@bh_nv_5_2, 'Express an Opinion 2 (PDF)',   'PDF',   '10MB',  'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/TaiLieuHocTap/Bai2/TaiLieuBai2_N5.%20Express%20an%20Opinion.pdf')),

-- Bài 3
(@bh_nv_5_3, 'Express an Opinion 3 (Video)', 'Video', '450MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/BaiGiang/Bai3/BaiGiang3_N5.%20Express%20an%20Opinion.mp4')),

(@bh_nv_5_3, 'Express an Opinion 3 (PDF)',   'PDF',   '10MB',  'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/N5.%20Express%20an%20Opinion/TaiLieuHocTap/Bai3/TaiLieuBai3_N5.%20Express%20an%20Opinion.pdf')),

-- Chương 6 - Write a Sentence Based on a Picture
-- Bài 1
(@bh_nv_6_1, 'Write a Sentence Based on a Picture 1 (Video)', 'Video', '500MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/BaiGiang/Bai1/BaiGiang1_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.mp4')),

(@bh_nv_6_1, 'Write a Sentence Based on a Picture 1 (PDF)',   'PDF',   '10MB',  'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/TaiLieuHocTap/Bai1/TaiLieuBai1_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf')),

-- Bài 2
(@bh_nv_6_2, 'Write a Sentence Based on a Picture 2 (Video)', 'Video', '300MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/BaiGiang/Bai2/BaiGiang2_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.mp4')),

(@bh_nv_6_2, 'Write a Sentence Based on a Picture 2 (PDF)',   'PDF',   '10MB',  'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/TaiLieuHocTap/Bai2/TauLieuBai2_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf')),

-- Bài 3
(@bh_nv_6_3, 'Write a Sentence Based on a Picture 3 (Video)', 'Video', '300MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/BaiGiang/Bai3/BaiGiang3_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.mp4')),

(@bh_nv_6_3, 'Write a Sentence Based on a Picture 3 (PDF)',   'PDF',   '10MB',  'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture/TaiLieuHocTap/Bai3/TaiLieuBai3_V1.%20Write%20a%20Sentence%20Based%20on%20a%20Picture.pdf')),

-- Chương 7 - Respond to Written Resquest
-- Bài 1
(@bh_nv_7_1, 'Respond to Written Resquest 1 (Video)', 'Video', '250MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/BaiGiang/Bai1/BaiGiang1_V2.%20Respond%20to%20a%20Written%20Resquest.mp4')),

(@bh_nv_7_1, 'Respond to Written Resquest 1 (PDF)',   'PDF',   '10MB',  'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/TaiLieuHocTap/Bai1/TaiLieuBai1_V2.%20Respond%20to%20a%20Written%20Resquest.pdf')),

-- Bài 2
(@bh_nv_7_2, 'Respond to Written Resquest 2 (Video)', 'Video', '300MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/BaiGiang/Bai2/BaiGiang2_V2.%20Respond%20to%20a%20Written%20Resquest.mp4')),

(@bh_nv_7_2, 'Respond to Written Resquest 2 (PDF)',   'PDF',   '10MB',  'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/TaiLieuHocTap/Bai2/TaiLieuBai2_V2.%20Respond%20to%20a%20Written%20Resquest.pdf')),

-- Bài 3
(@bh_nv_7_3, 'Respond to Written Resquest 3 (Video)', 'Video', '300MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/BaiGiang/Bai3/BaiGiang3_V2.%20Respond%20to%20a%20Written%20Resquest.mp4')),

(@bh_nv_7_3, 'Respond to Written Resquest 3 (PDF)',   'PDF',   '10MB',  'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V2.%20Respond%20to%20a%20Written%20Resquest/TaiLieuHocTap/Bai3/TaiLieuBai3_V2.%20Respond%20to%20a%20Written%20Resquest.pdf')),

-- Chương 8 - Write an Opinion Essay
-- Bài 1
(@bh_nv_8_1, 'Write an Opinion Essay 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách lên dàn ý chi tiết cho một bài luận trình bày quan điểm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/BaiGiang/Bai1/BaiGiang1_V3.%20Write%20an%20Opinion%20Essay.mp4')),

(@bh_nv_8_1, 'Write an Opinion Essay 1 (PDF)',   'PDF',   '10MB',  'Hướng dẫn cách lên dàn ý chi tiết cho một bài luận trình bày quan điểm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/TaiLieuHocTap/Bai1/TaiLieuBai1_V3.%20Write%20an%20Opinion%20Essay.pdf')),

-- Bài 2
(@bh_nv_8_2, 'Write an Opinion Essay 2 (Video)', 'Video', '350MB', 'Phát triển luận điểm, đưa ra ví dụ và dẫn chứng để bài viết có sức thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/BaiGiang/Bai2/BaiGiang2_V3.%20Write%20an%20Opinion%20Essay.mp4')),

(@bh_nv_8_2, 'Write an Opinion Essay 2 (PDF)',   'PDF',   '10MB',  'Phát triển luận điểm, đưa ra ví dụ và dẫn chứng để bài viết có sức thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/TaiLieuHocTap/Bai2/TaiLieuBai2_V3.%20Write%20an%20Opinion%20Essay.pdf')),

-- Bài 3
(@bh_nv_8_3, 'Write an Opinion Essay 3 (Video)', 'Video', '350MB', 'Thực hành viết một bài luận hoàn chỉnh và các tiêu chí tự đánh giá.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/BaiGiang/Bai3/BaiGiang3_V3.%20Write%20an%20Opinion%20Essay.mp4')),

(@bh_nv_8_3, 'Write an Opinion Essay 3 (PDF)',   'PDF',   '10MB',  'Thực hành viết một bài luận hoàn chỉnh và các tiêu chí tự đánh giá.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(N%C3%B3i%20-%20Vi%E1%BA%BFt)/V3.%20Write%20an%20Opinion%20Essay/TaiLieuHocTap/Bai3/TaiLieuBai3_V3.%20Write%20an%20Opinion%20Essay.pdf'));


-- =========================================================
-- NGHE - ĐỌC
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, mime_type, visibility, public_url) VALUES
-- Chương 1 - Photographs
-- Bài 1
(@bh_nd_1_1, 'Photographs Video Bài 1', 'Video', '350MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/BaiGiang/Bai1/Introduction%20to%20TOEIC%20PART%201%20and%20Photos%20of%20People.mp4')),

(@bh_nd_1_1, 'Photographs PDF Bài 1.1',   'PDF',   '10MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/TaiLieuHocTap/Bai1/TaiLieuBai1.1_P1.%20Photographs.pdf')),

(@bh_nd_1_1, 'Photographs PDF Bài 1.2',   'PDF',   '10MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/TaiLieuHocTap/Bai1/TaiLieuBai1.2_P1.%20Photographs.pdf')),

(@bh_nd_1_1, 'Photographs Audio 1 Bài 1','Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.1%20P1%20B1.mp3')),

(@bh_nd_1_1, 'Photographs Audio 2 Bài 1','Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.2%20P1%20B1.mp3')),

(@bh_nd_1_1, 'Photographs Audio 3 Bài 1','Audio', '20MB', 'Chiến lược làm bài thi Nghe tổng quan và các mẹo để tập trung tối đa.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai1/mp3.3%20P1%20B1.mp3')),

-- Bài 2
(@bh_nd_1_2, 'Photographs Video Bài 2', 'Video', '350MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/BaiGiang/Bai2/Photos%20of%20People%20and%20Photos%20of%20Objects%20and%20Views.mp4')),

(@bh_nd_1_2, 'Photographs PDF Bài 2',   'PDF',   '10MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/TaiLieuHocTap/Bai2/TaiLieuBai2_P1.%20Photographs.pdf')),

(@bh_nd_1_2, 'Photographs Audio 1 Bài 2','Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.1%20P1%20B2.mp3')),

(@bh_nd_1_2, 'Photographs Audio 2 Bài 2','Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.2%20P1%20B2.mp3')),

(@bh_nd_1_2, 'Photographs Audio 3 Bài 2','Audio', '20MB', 'Phân tích các loại bẫy thường gặp trong Part 1 (từ đồng âm, mô tả sai đối tượng).', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai2/mp3.3%20P1%20B2.mp3')),

-- Bài 3
(@bh_nd_1_3, 'Photographs Video Bài 3', 'Video', '350MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/BaiGiang/Bai3/Practice%201.mp4')),

(@bh_nd_1_3, 'Photographs PDF Bài 3.1',   'PDF',   '10MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/TaiLieuHocTap/Bai3/TaiLieuBai3.1_P1.%20Photographs.pdf')),

(@bh_nd_1_3, 'Photographs PDF Bài 3.2',   'PDF',   '10MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/TaiLieuHocTap/Bai3/TaiLieuBai3.2_P1.%20Photographs.pdf')),

(@bh_nd_1_3, 'Photographs Audio 1 Bài 3','Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.1%20P1%20B3.mp3')),

(@bh_nd_1_3, 'Photographs Audio 2 Bài 3','Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.2%20P1%20B3.mp3')),

(@bh_nd_1_3, 'Photographs Audio 3 Bài 3','Audio', '20MB', 'Luyện nghe với các hình ảnh đa dạng về người, vật và phong cảnh.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%201.%20Photographs/Audio/Bai3/mp3.3%20P1%20B3.mp3')),

-- Chương 2 - Question - Response
-- Bài 1
(@bh_nd_2_1, 'Question - Response Video Bài 1', 'Video', '350MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/BaiGiang/Bai1/T%E1%BB%95ng%20quan%20v%E1%BB%81%20TOEIC%20PART%202%20v%C3%A0%20k%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20d%E1%BA%A1ng%20b%C3%A0i%20Wh-questions.mp4')),

(@bh_nd_2_1, 'Question - Response PDF Bài 1',   'PDF',   '10MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/TaiLieuHocTap/Bai1/TaiLieuBai1_P2.%20Question-Respondse.pdf')),

(@bh_nd_2_1, 'Question - Response Audio 1 Bài 1','Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai1/mp3.1%20P2%20B1.MP3')),

(@bh_nd_2_1, 'Question - Response Audio 2 Bài 1','Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai1/mp3.2%20P2%20B1.mp3')),

(@bh_nd_2_1, 'Question - Response Audio 3 Bài 1','Audio', '20MB', 'Nhận diện các dạng câu hỏi (Who, What, Where, When, Why, How) và cách trả lời tương ứng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai1/mp3.3%20P2%20B1.MP3')),

-- Bài 2
(@bh_nd_2_2, 'Question - Response Video Bài 2', 'Video', '350MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/BaiGiang/Bai2/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20d%E1%BA%A1ng%20b%C3%A0i%20Wh-questions.mp4')),

(@bh_nd_2_2, 'Question - Response PDF Bài 2',   'PDF',   '10MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/TaiLieuHocTap/Bai2/TaiLieuBai2_P2.%20Question-Respondse.pdf')),

(@bh_nd_2_2, 'Question - Response Audio 1 Bài 2','Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai2/mp3.1%20P2%20B2.MP3')),

(@bh_nd_2_2, 'Question - Response Audio 2 Bài 2','Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai2/mp3.2%20P2%20B2.mp3')),

(@bh_nd_2_2, 'Question - Response Audio 3 Bài 2','Audio', '20MB', 'Luyện tập nghe các câu hỏi và câu trả lời với nhiều giọng đọc khác nhau.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai2/mp3.3%20P2%20B2.MP3')),

-- Bài 3
(@bh_nd_2_3, 'Question - Response Video Bài 3', 'Video', '350MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/BaiGiang/Bai3/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20d%E1%BA%A1ng%20b%C3%A0i%20Yes-No%20questions.mp4')),

(@bh_nd_2_3, 'Question - Response PDF Bài 3',   'PDF',   '10MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/TaiLieuHocTap/Bai3/TaiLieuBai3_P2.%20Question-Respondse.pdf')),

(@bh_nd_2_3, 'Question - Response Audio 1 Bài 3','Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai3/mp3.1%20P2%20B3.MP3')),

(@bh_nd_2_3, 'Question - Response Audio 2 Bài 3','Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai3/mp3.2%20P2%20B3.mp3')),

(@bh_nd_2_3, 'Question - Response Audio 3 Bài 3','Audio', '20MB', 'Phân tích các lựa chọn gây nhiễu và mẹo để chọn đáp án đúng một cách nhanh chóng.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%202.%20Question%E2%80%93Response/Audio/Bai3/mp3.3%20P2%20B3.MP3')),

-- Chương 3 - Short Conversations
-- Bài 1
(@bh_nd_3_1, 'Short Conversations Video Bài 1', 'Video', '350MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/BaiGiang/Bai1/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20%C4%91o%E1%BA%A1n%20h%E1%BB%99i%20tho%E1%BA%A1i%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Service.mp4')),

(@bh_nd_3_1, 'Short Conversations PDF Bài 1',   'PDF',   '10MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/TaiLieuHocTap/Bai1/TaiLieuBai1_P3.%20Short%20Conversations.pdf')),

(@bh_nd_3_1, 'Short Conversations Audio 1 Bài 1','Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.1%20P3%20B1.mp3')),

(@bh_nd_3_1, 'Short Conversations Audio 2 Bài 1','Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.2%20P3%20B1.mp3')),

(@bh_nd_3_1, 'Short Conversations Audio 3 Bài 1','Audio', '20MB', 'Kỹ năng đọc trước câu hỏi để định hướng thông tin cần nghe trong đoạn hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai1/mp3.3%20P3%20B1.mp3')),

-- Bài 2
(@bh_nd_3_2, 'Short Conversations Video Bài 2', 'Video', '350MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/BaiGiang/Bai2/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20%C4%91o%E1%BA%A1n%20h%E1%BB%99i%20tho%E1%BA%A1i%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20At%20the%20office.mp4')),

(@bh_nd_3_2, 'Short Conversations PDF Bài 2',   'PDF',   '10MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/TaiLieuHocTap/Bai2/TaiLieuBai2_P3.%20Short%20Conversations.pdf')),

(@bh_nd_3_2, 'Short Conversations Audio 1 Bài 2','Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.1%20P3%20B2.mp3')),

(@bh_nd_3_2, 'Short Conversations Audio 2 Bài 2','Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.2%20P3%20B2.mp3')),

(@bh_nd_3_2, 'Short Conversations Audio 3 Bài 2','Audio', '20MB', 'Luyện nghe các đoạn hội thoại về chủ đề công sở, mua sắm, và đời sống hàng ngày.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai2/mp3.3%20P3%20B2.mp3')),

-- Bài 3
(@bh_nd_3_3, 'Short Conversations Video Bài 3', 'Video', '350MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/BaiGiang/Bai3/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20%C4%91o%E1%BA%A1n%20h%E1%BB%99i%20tho%E1%BA%A1i%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Shopping%20and%20Entertainment.mp4')),

(@bh_nd_3_3, 'Short Conversations PDF Bài 3',   'PDF',   '10MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/TaiLieuHocTap/Bai3/TaiLieuBai3_P3.%20Short%20Conversations.pdf')),

(@bh_nd_3_3, 'Short Conversations Audio 1 Bài 3','Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.1%20P3%20B3.mp3')),

(@bh_nd_3_3, 'Short Conversations Audio 2 Bài 3','Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.2%20P3%20B3.mp3')),

(@bh_nd_3_3, 'Short Conversations Audio 3 Bài 3','Audio', '20MB', 'Cách suy luận ý của người nói và mục đích của cuộc hội thoại.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%203.%20Short%20Conversations/Audio/Bai3/mp3.3%20P3%20B3.mp3')),

-- Chương 4 - Short Talks
-- Bài 1
(@bh_nd_4_1, 'Short Talks Video Bài 1', 'Video', '350MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/BaiGiang/Bai1/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20nh%E1%BB%AFng%20c%C3%A2u%20h%E1%BB%8Fi%20thu%E1%BB%99c%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Office.mp4')),

(@bh_nd_4_1, 'Short Talks PDF Bài 1',   'PDF',   '10MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/TaiLieuHocTap/Bai1/TaiLieuBai1_P4.%20Short%20Talks.pdf')),

(@bh_nd_4_1, 'Short Talks Audio 1 Bài 1','Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.1%20P4%20B1.mp3')),

(@bh_nd_4_1, 'Short Talks Audio 2 Bài 1','Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.2%20P4%20B1.mp3')),

(@bh_nd_4_1, 'Short Talks Audio 3 Bài 1','Audio', '20MB', 'Nhận diện các dạng bài nói ngắn: tin nhắn thoại, thông báo, quảng cáo, báo cáo.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai1/mp3.3%20P4%20B1.mp3')),

-- Bài 2
(@bh_nd_4_2, 'Short Talks Video Bài 2', 'Video', '350MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/BaiGiang/Bai2/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20nh%E1%BB%AFng%20c%C3%A2u%20h%E1%BB%8Fi%20thu%E1%BB%99c%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Shopping%20and%20Entertainment.mp4')),

(@bh_nd_4_2, 'Short Talks PDF Bài 2',   'PDF',   '10MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/TaiLieuHocTap/Bai2/TaiLieuBai2_P4.%20Short%20Talks.pdf')),

(@bh_nd_4_2, 'Short Talks Audio 1 Bài 2','Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.1%20P4%20B2.mp3')),

(@bh_nd_4_2, 'Short Talks Audio 2 Bài 2','Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.2%20P4%20B2.mp3')),

(@bh_nd_4_2, 'Short Talks Audio 3 Bài 2','Audio', '20MB', 'Luyện nghe và nắm bắt các thông tin chi tiết như số liệu, thời gian, địa điểm.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai2/mp3.3%20P4%20B2.mp3')),

-- Bài 3
(@bh_nd_4_3, 'Short Talks Video Bài 3', 'Video', '350MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/BaiGiang/Bai3/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20nh%E1%BB%AFng%20c%C3%A2u%20h%E1%BB%8Fi%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Conference%20-%20Meeting%20-%20Training%20Session.mp4')),

(@bh_nd_4_3, 'Short Talks PDF Bài 3',   'PDF',   '10MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/TaiLieuHocTap/Bai3/TaiLieuBai3_P4.%20Short%20Talks.pdf')),

(@bh_nd_4_3, 'Short Talks Audio 1 Bài 3','Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.1%20P4%20B3.mp3')),

(@bh_nd_4_3, 'Short Talks Audio 2 Bài 3','Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.2%20P4%20B3.mp3')),

(@bh_nd_4_3, 'Short Talks Audio 3 Bài 3','Audio', '20MB', 'Thực hành với các bài nói có tốc độ nhanh và chứa nhiều thông tin.', 'audio/mpeg', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%204.%20Short%20Talks/Audio/Bai3/mp3.3%20P4%20B3.mp3')),

-- Chương 5 - 6 - Incomplete Sentences
-- Bài 1
(@bh_nd_5_1, 'Incomplete Sentences Video Bài 1', 'Video', '350MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/BaiGiang/Bai1/Sentence%20Structure%20-%20C%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_nd_5_1, 'Incomplete Sentences PDF Bài 1',   'PDF',   '10MB', 'Ôn tập các chủ điểm ngữ pháp cốt lõi thường gặp trong bài thi (thì, dạng từ, giới từ).', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/TaiLieuHocTap/Bai1/TaiLieuBai1_P5-6.%20Incomplete%20Sentences.pdf')),


-- Bài 2
(@bh_nd_5_2, 'Incomplete Sentences Video Bài 2', 'Video', '350MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/BaiGiang/Bai2/Sentence%20Structure-%20Ch%E1%BB%AFa%20b%C3%A0i%20t%E1%BA%ADp%20c%C3%A1c%20th%C3%A0nh%20ph%E1%BA%A7n%20c%C6%A1%20b%E1%BA%A3n%20c%E1%BB%A7a%20c%C3%A2u.mp4')),

(@bh_nd_5_2, 'Incomplete Sentences PDF 1 Bài 2',   'PDF',   '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/TaiLieuHocTap/Bai2/TaiLieuBai2.1_P5-6.%20Incomplete%20Sentences.pdf')),

(@bh_nd_5_2, 'Incomplete Sentences PDF 2 Bài 2',   'PDF',   '10MB', 'Mở rộng vốn từ vựng theo các chủ đề thương mại, văn phòng và giao dịch.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/TaiLieuHocTap/Bai2/TaiLieuBai2.2_P5-6.%20Incomplete%20Sentences.pdf')),


-- Bài 3
(@bh_nd_5_3, 'Incomplete Sentences Video Bài 3', 'Video', '350MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/BaiGiang/Bai3/C%C3%A2u%20gi%E1%BA%A3%20%C4%91%E1%BB%8Bnh.mp4')),

(@bh_nd_5_3, 'Incomplete Sentences PDF Bài 3',   'PDF',   '10MB', 'Chiến lược đọc và phân tích ngữ cảnh để lựa chọn đáp án đúng cho đoạn văn.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%205%E2%80%936.%20Incomplete%20Sentences/TaiLieuHocTap/Bai3/TaiLieuBai3_P5-6.%20Incomplete%20Sentences.pdf')),


-- Chương 7 - Vocabulary & Reading Comprhension Practice
-- Bài 1
(@bh_nd_6_1, 'Vocabulary & Reading Comprhension Practice Video Bài 1', 'Video', '350MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/BaiGiang/Bai1/An%20Introduction%20to%20Part%207.mp4')),

(@bh_nd_6_1, 'Vocabulary & Reading Comprhension Practice PDF Bài 1',   'PDF',   '10MB', 'Kỹ năng đọc lướt (skimming) và đọc quét (scanning) để tìm thông tin trong một đoạn văn.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/TaiLieuHocTap/Bai1/TaiLieuBai1_P7.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice.pdf')),


-- Bài 2
(@bh_nd_6_2, 'Vocabulary & Reading Comprhension Practice Video Bài 2', 'Video', '350MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/BaiGiang/Bai2/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20c%C3%A2u%20h%E1%BB%8Fi%20thu%E1%BB%99c%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20E-%20mail-Letter-Fax.mp4')),

(@bh_nd_6_2, 'Vocabulary & Reading Comprhension Practice PDF Bài 2',   'PDF',   '10MB', 'Hướng dẫn cách liên kết thông tin giữa hai hoặc ba đoạn văn để trả lời câu hỏi.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/TaiLieuHocTap/Bai2/TaiLieuBai2_P7.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice.pdf')),


-- Bài 3
(@bh_nd_6_3, 'Vocabulary & Reading Comprhension Practice Video Bài 3', 'Video', '350MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'video/mp4', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/BaiGiang/Bai3/K%C4%A9%20n%C4%83ng%20x%E1%BB%AD%20l%C3%AD%20c%C3%A2u%20h%E1%BB%8Fi%20thu%E1%BB%99c%20ch%E1%BB%A7%20%C4%91i%E1%BB%83m%20Memo%20-Notice%20-Announcement.mp4')),

(@bh_nd_6_3, 'Vocabulary & Reading Comprhension Practice PDF Bài 3',   'PDF',   '10MB', 'Chiến lược quản lý thời gian hiệu quả và xử lý các câu hỏi về từ vựng và suy luận.', 'application/pdf', 'public', 
CONCAT(@R2_BASE_PUBLIC, '/Luy%E1%BB%87n%20thi%20TOEIC%20(Nghe%20-%20%C4%90%E1%BB%8Dc)/PART%207.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice/TaiLieuHocTap/Bai3/TaiLieuBai3_P7.%20Vocabulary%20%26%20Reading%20Comprehension%20Practice.pdf'));

COMMIT;