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
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B1/BaiGiang.mp4')),

(@bh_nv_3_1, 'Respond to Questions 1 (PDF)',   'PDF',   '10MB',  'Phân tích các dạng câu hỏi thường gặp và phương pháp trả lời ngắn gọn, đúng trọng tâm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_3_2, 'Respond to Questions 2 (Video)', 'Video', '250MB', 'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B2/BaiGiang.mp4')),

(@bh_nv_3_2, 'Respond to Questions 2 (PDF)',   'PDF',   '10MB',  'Thực hành trả lời các câu hỏi về chủ đề quen thuộc như công việc, sở thích và hoạt động hàng ngày.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_3_3, 'Respond to Questions 3 (Video)', 'Video', '250MB', 'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B3/BaiGiang.mp4')),

(@bh_nv_3_3, 'Respond to Questions 3 (PDF)',   'PDF',   '10MB',  'Luyện tập với các câu hỏi mô phỏng, tập trung cải thiện tốc độ và sự trôi chảy.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C3/B3/TaiLieu.pdf')),


-- Chương 4 - Respond to Questions Using Information
-- Bài 1
(@bh_nv_4_1, 'Respond to Questions Using Information 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B1/BaiGiang.mp4')),

(@bh_nv_4_1, 'Respond to Questions Using Information 1 (PDF)',   'PDF',   '10MB',  'Hướng dẫn cách đọc và phân tích nhanh các loại tài liệu cho sẵn như lịch trình, biểu đồ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_4_2, 'Respond to Questions Using Information 2 (Video)', 'Video', '350MB', 'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B2/BaiGiang.mp4')),

(@bh_nv_4_2, 'Respond to Questions Using Information 2 (PDF)',   'PDF',   '10MB',  'Thực hành kỹ năng tìm kiếm và tổng hợp thông tin từ tài liệu để trả lời câu hỏi.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_4_3, 'Respond to Questions Using Information 3 (Video)', 'Video', '350MB', 'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B3/BaiGiang.mp4')),

(@bh_nv_4_3, 'Respond to Questions Using Information 3 (PDF)',   'PDF',   '10MB',  'Áp dụng kỹ năng vào các bài tập mô phỏng với nhiều dạng tài liệu và câu hỏi phức tạp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C4/B3/TaiLieu.pdf')),


-- Chương 5 - Express an Opinion
-- Bài 1
(@bh_nv_5_1, 'Express an Opinion 1 (Video)', 'Video', '450MB', 'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B1/BaiGiang.mp4')),

(@bh_nv_5_1, 'Express an Opinion 1 (PDF)',   'PDF',   '10MB',  'Xây dựng cấu trúc cho một bài trình bày quan điểm: Mở đầu, luận điểm, và kết luận.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_5_2, 'Express an Opinion 2 (Video)', 'Video', '450MB', 'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B2/BaiGiang.mp4')),

(@bh_nv_5_2, 'Express an Opinion 2 (PDF)',   'PDF',   '10MB',  'Cách sử dụng các cụm từ nối và từ vựng để thể hiện quan điểm một cách thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_5_3, 'Express an Opinion 3 (Video)', 'Video', '450MB', 'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B3/BaiGiang.mp4')),

(@bh_nv_5_3, 'Express an Opinion 3 (PDF)',   'PDF',   '10MB',  'Thực hành trình bày quan điểm về các chủ đề xã hội và công việc thường gặp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C5/B3/TaiLieu.pdf')),

-- Chương 6 - Write a Sentence Based on a Picture
-- Bài 1
(@bh_nv_6_1, 'Write a Sentence Based on a Picture 1 (Video)', 'Video', '500MB', 'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B1/BaiGiang.mp4')),

(@bh_nv_6_1, 'Write a Sentence Based on a Picture 1 (PDF)',   'PDF',   '10MB',  'Các cấu trúc ngữ pháp và từ vựng cần thiết để viết một câu miêu tả ảnh chính xác.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_6_2, 'Write a Sentence Based on a Picture 2 (Video)', 'Video', '300MB', 'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B2/BaiGiang.mp4')),

(@bh_nv_6_2, 'Write a Sentence Based on a Picture 2 (PDF)',   'PDF',   '10MB',  'Luyện tập viết câu tập trung vào việc sử dụng đúng giới từ, thì và dạng của từ.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_6_3, 'Write a Sentence Based on a Picture 3 (Video)', 'Video', '300MB', 'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B3/BaiGiang.mp4')),

(@bh_nv_6_3, 'Write a Sentence Based on a Picture 3 (PDF)',   'PDF',   '10MB',  'Phân tích các lỗi sai thường gặp khi viết câu miêu tả ảnh và cách khắc phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C6/B3/TaiLieu.pdf')),

-- Chương 7 - Respond to Written Resquest
-- Bài 1
(@bh_nv_7_1, 'Respond to Written Resquest 1 (Video)', 'Video', '250MB', 'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B1/BaiGiang.mp4')),

(@bh_nv_7_1, 'Respond to Written Resquest 1 (PDF)',   'PDF',   '10MB',  'Tìm hiểu cấu trúc chuẩn của một email công việc: Chào hỏi, nội dung chính, và kết thúc.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_7_2, 'Respond to Written Resquest 2 (Video)', 'Video', '300MB', 'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B2/BaiGiang.mp4')),

(@bh_nv_7_2, 'Respond to Written Resquest 2 (PDF)',   'PDF',   '10MB',  'Phân tích các yêu cầu thường gặp và cách trả lời email một cách lịch sự, chuyên nghiệp.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_7_3, 'Respond to Written Resquest 3 (Video)', 'Video', '300MB', 'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B3/BaiGiang.mp4')),

(@bh_nv_7_3, 'Respond to Written Resquest 3 (PDF)',   'PDF',   '10MB',  'Thực hành viết email phản hồi cho các tình huống công việc cụ thể.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C7/B3/TaiLieu.pdf')),

-- Chương 8 - Write an Opinion Essay
-- Bài 1
(@bh_nv_8_1, 'Write an Opinion Essay 1 (Video)', 'Video', '350MB', 'Hướng dẫn cách lên dàn ý chi tiết cho một bài luận trình bày quan điểm.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B1/BaiGiang.mp4')),

(@bh_nv_8_1, 'Write an Opinion Essay 1 (PDF)',   'PDF',   '10MB',  'Hướng dẫn cách lên dàn ý chi tiết cho một bài luận trình bày quan điểm.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nv_8_2, 'Write an Opinion Essay 2 (Video)', 'Video', '350MB', 'Phát triển luận điểm, đưa ra ví dụ và dẫn chứng để bài viết có sức thuyết phục.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B2/BaiGiang.mp4')),

(@bh_nv_8_2, 'Write an Opinion Essay 2 (PDF)',   'PDF',   '10MB',  'Phát triển luận điểm, đưa ra ví dụ và dẫn chứng để bài viết có sức thuyết phục.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nv_8_3, 'Write an Opinion Essay 3 (Video)', 'Video', '350MB', 'Thực hành viết một bài luận hoàn chỉnh và các tiêu chí tự đánh giá.', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B3/BaiGiang.mp4')),

(@bh_nv_8_3, 'Write an Opinion Essay 3 (PDF)',   'PDF',   '10MB',  'Thực hành viết một bài luận hoàn chỉnh và các tiêu chí tự đánh giá.', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NoiViet/C8/B3/TaiLieu.pdf'));


-- =========================================================
-- NGHE - ĐỌC
-- =========================================================
INSERT INTO TAILIEUHOCTAP
(maBH, tenTL, loai, kichThuoc, moTa, storage_key, r2_bucket, mime_type, size_bytes, duration_sec, visibility, public_url) VALUES
-- Chương 1 - Photographs
-- Bài 1
(@bh_nd_1_1, 'Video Bài 1', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B1/BaiGiang.mp4')),

(@bh_nd_1_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_1_2, 'Video Bài 2', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B2/BaiGiang.mp4')),

(@bh_nd_1_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_1_3, 'Video Bài 3', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B3/BaiGiang.mp4')),

(@bh_nd_1_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C1/B3/TaiLieu.pdf')),

-- Chương 2 - Question - Response
-- Bài 1
(@bh_nd_2_1, 'Video Bài 1', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B1/BaiGiang.mp4')),

(@bh_nd_2_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_2_2, 'Video Bài 2', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B2/BaiGiang.mp4')),

(@bh_nd_2_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_2_3, 'Video Bài 3', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B3/BaiGiang.mp4')),

(@bh_nd_2_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C2/B3/TaiLieu.pdf')),

-- Chương 3 - Short Conversations
-- Bài 1
(@bh_nd_3_1, 'Video Bài 1', 'Video', '200MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B1/BaiGiang.mp4')),

(@bh_nd_3_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_3_2, 'Video Bài 2', 'Video', '200MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B2/BaiGiang.mp4')),

(@bh_nd_3_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_3_3, 'Video Bài 3', 'Video', '200MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B3/BaiGiang.mp4')),

(@bh_nd_3_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C3/B3/TaiLieu.pdf')),

-- Chương 4 - Short Talks
-- Bài 1
(@bh_nd_4_1, 'Video Bài 1', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B1/BaiGiang.mp4')),

(@bh_nd_4_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_4_2, 'Video Bài 2', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B2/BaiGiang.mp4')),

(@bh_nd_4_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_4_3, 'Video Bài 3', 'Video', '300MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B3/BaiGiang.mp4')),

(@bh_nd_4_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C4/B3/TaiLieu.pdf')),

-- Chương 5 - 6 - Incomplete Sentences
-- Bài 1
(@bh_nd_5_1, 'Video Bài 1', 'Video', '400MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B1/BaiGiang.mp4')),

(@bh_nd_5_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_5_2, 'Video Bài 2', 'Video', '400MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B2/BaiGiang.mp4')),

(@bh_nd_5_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_5_3, 'Video Bài 3', 'Video', '400MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B3/BaiGiang.mp4')),

(@bh_nd_5_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C5/B3/TaiLieu.pdf')),

-- Chương 7 - Vocabulary & Reading Comprhension Practice
-- Bài 1
(@bh_nd_6_1, 'Video Bài 1', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B1/BaiGiang.mp4')),

(@bh_nd_6_1, 'PDF Bài 1',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B1/TaiLieu.pdf')),

-- Bài 2
(@bh_nd_6_2, 'Video Bài 2', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B2/BaiGiang.mp4')),

(@bh_nd_6_2, 'PDF Bài 2',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B2/TaiLieu.pdf')),

-- Bài 3
(@bh_nd_6_3, 'Video Bài 3', 'Video', '250MB', '', 'video/mp4', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B3/BaiGiang.mp4')),

(@bh_nd_6_3, 'PDF Bài 3',   'PDF',   '8MB',   '', 'application/pdf', 'public',
CONCAT(@R2_BASE_PUBLIC, '/NgheDoc/C6/B3/TaiLieu.pdf'));

COMMIT;