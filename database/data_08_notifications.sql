USE Online_Certificate_Classroom;

START TRANSACTION;

-- Map user & course ids for seeded notifications
SELECT maND INTO @nd_trinh FROM nguoidung WHERE email = 'nguyenthitutrinh120504@gmail.com';
SELECT maND INTO @nd_luan  FROM nguoidung WHERE email = 'hakachi303@gmail.com';
SELECT maND INTO @nd_tri   FROM nguoidung WHERE email = 'tringhe2004@gmail.com';

SELECT maKH INTO @kh_speaking_405_600  FROM khoahoc WHERE slug = 'luyen-thi-toeic-speaking-405-600';
SELECT maKH INTO @kh_writing_405_600   FROM khoahoc WHERE slug = 'luyen-thi-toeic-writing-405-600';
SELECT maKH INTO @kh_listening_405_600 FROM khoahoc WHERE slug = 'luyen-thi-toeic-listening-405-600';
SELECT maKH INTO @kh_reading_605_780   FROM khoahoc WHERE slug = 'luyen-thi-toeic-reading-605-780';

SELECT maGoi INTO @goi_intermediate FROM goi_khoa_hoc WHERE slug = 'toeic-intermediate-full-pack-605-780';

INSERT INTO thongbao (
    maND, maKH, maGoi, loai, tieuDe, noiDung, action_url, action_label, hinhAnh, metadata, is_read, read_at, created_at, updated_at
) VALUES
(@nd_trinh, @kh_writing_405_600, NULL, 'GRADE',
    'Giang vien da cham bai viet',
    'Feedback chi tiet cho bai viet Writing Part 2 cua ban da san sang. Xem de cap nhat diem va huong dan nang diem nhanh hon.',
    '/student/my-courses', 'Xem phan hoi', 'toeic-writing-405-600.png',
    JSON_OBJECT('cta', 'writing_grade'), 0, NULL, '2025-11-15 08:30:00', '2025-11-15 08:30:00'),
(@nd_trinh, @kh_listening_405_600, NULL, 'COURSE',
    'Lich live Q&A Listening tuans nay',
    'Giang vien se giai dap truc tiep cac cau hoi Listening 405-600 vao 20:00 Thu Nam. Dat loi nhac va tham gia dung gio.',
    '/courses/luyen-thi-toeic-listening-405-600', 'Xem lich chi tiet', 'toeic-listening-405-600.png',
    JSON_OBJECT('cta', 'live_qna'), 0, NULL, '2025-11-16 09:15:00', '2025-11-16 09:15:00'),
(@nd_trinh, NULL, @goi_intermediate, 'PROMOTION',
    'Uu dai TOEIC Intermediate chi cho hoc vien da dang ky',
    'Combo TOEIC Intermediate Full Pack giam 25% trong tuan nay. Ban da hoan thanh band 405-600, nang band ngay de lay diem 780+.',
    '/combos/toeic-intermediate-full-pack-605-780', 'Nhan uu dai', 'combo_toeic_intermediate_605-780.jpg',
    JSON_OBJECT('cta', 'upgrade_combo'), 0, NULL, '2025-11-17 07:50:00', '2025-11-17 07:50:00'),

(@nd_luan, @kh_speaking_405_600, NULL, 'COURSE',
    'Nhac hoc Speaking hom nay',
    'Ban con 1 nhiem vu phat am va 1 video Listening da luu trong muc Tiep tuc hoc. Vao hoc ngay de giu nhịp dien.',
    '/courses/luyen-thi-toeic-speaking-405-600', 'Tiep tuc hoc', 'toeic-speaking-405-600.png',
    JSON_OBJECT('cta', 'continue_learning'), 1, '2025-11-14 06:30:00', '2025-11-14 06:00:00', '2025-11-14 06:30:00'),
(@nd_luan, NULL, NULL, 'PROMOTION',
    'Ma uu dai hoc phi cuoi tuan',
    'Nhap ma OCCWEEKEND de giam 15% cho moi khoa le den 23:59 Chu Nhat. Ap dung cho ca khach hang cu.',
    '/student/cart', 'Dung ma ngay', NULL,
    JSON_OBJECT('code', 'OCCWEEKEND'), 0, NULL, '2025-11-15 10:10:00', '2025-11-15 10:10:00'),

(@nd_tri, @kh_reading_605_780, @goi_intermediate, 'SYSTEM',
    'Dong bo tien do va chuc chi nang band',
    'Tai khoan da duoc dong bo. Khi hoan thanh 70% noi dung Reading 605-780 ban se duoc de xuat cap chung chi tich luy.',
    '/student/progress', 'Xem tien do', 'toeic-reading-605-780.png',
    JSON_OBJECT('progress_target', 70), 0, NULL, '2025-11-16 12:20:00', '2025-11-16 12:20:00');

COMMIT;
