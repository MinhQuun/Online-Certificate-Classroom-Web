-- =========================================================
-- Online_Certificate_Classroom - HỆ THỐNG QUẢN LÝ LỚP HỌC CHỨNG CHỈ TRỰC TUYẾN
-- =========================================================

DROP DATABASE IF EXISTS Online_Certificate_Classroom;
CREATE DATABASE Online_Certificate_Classroom
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE Online_Certificate_Classroom;

-- =========================================================
-- 1) PHÂN QUYỀN & NGƯỜI DÙNG
-- =========================================================
-- Bảng QUYEN: Lưu trữ các quyền hệ thống (ví dụ: admin, giáo vụ, giảng viên, học viên).
CREATE TABLE QUYEN (
    maQuyen VARCHAR(10)  NOT NULL,
    tenQuyen VARCHAR(50) NOT NULL,
    PRIMARY KEY (maQuyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng NGUOIDUNG: Thông tin người dùng chung (admin, giáo vụ, giảng viên, học viên).
CREATE TABLE NGUOIDUNG (
    maND INT NOT NULL AUTO_INCREMENT,
    hoTen VARCHAR(100) NOT NULL,              -- Họ tên đầy đủ
    email VARCHAR(255) NOT NULL UNIQUE,       -- Email duy nhất
    sdt VARCHAR(20),                          -- Số điện thoại
    matKhau VARCHAR(255) NOT NULL,            -- Mật khẩu (hashed)
    chuyenMon VARCHAR(255),                   -- Chuyên môn (cho giảng viên)
    vaiTro ENUM('ADMIN','GIAO_VU','GIANG_VIEN','HOC_VIEN') DEFAULT 'HOC_VIEN',  -- Vai trò mặc định là học viên
    trangThai ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',                       -- Trạng thái hoạt động
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maND)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng QUYEN_NGUOIDUNG: Liên kết quyền với người dùng (nhiều-nhiều).
CREATE TABLE QUYEN_NGUOIDUNG (
    maND INT NOT NULL,
    maQuyen VARCHAR(10) NOT NULL,
    PRIMARY KEY (maND, maQuyen),
    KEY IX_QND_MAQUYEN (maQuyen),
    CONSTRAINT FK_QND_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE CASCADE,
    CONSTRAINT FK_QND_Q  FOREIGN KEY (maQuyen) REFERENCES QUYEN(maQuyen) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng HOCVIEN: Hồ sơ chi tiết học viên (1-1 với NGUOIDUNG).
CREATE TABLE HOCVIEN (
    maHV INT NOT NULL AUTO_INCREMENT,
    maND INT NOT NULL UNIQUE,                 -- Liên kết với NGUOIDUNG
    hoTen VARCHAR(100),                       -- Họ tên (có thể override)
    ngaySinh DATE,                            -- Ngày sinh
    ngayNhapHoc DATE,                         -- Ngày nhập học
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maHV),
    CONSTRAINT FK_HV_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2) DANH MỤC / KHÓA HỌC / CHƯƠNG / BÀI HỌC / TÀI LIỆU
-- =========================================================
-- Bảng DANHMUC: Danh mục khóa học (ví dụ: CNTT, Kinh doanh).
CREATE TABLE DANHMUC (
    maDanhMuc INT NOT NULL AUTO_INCREMENT,
    tenDanhMuc VARCHAR(100) NOT NULL,         -- Tên danh mục
    slug VARCHAR(120) NOT NULL UNIQUE,        -- Slug cho URL
    icon VARCHAR(100),                        -- Icon đại diện
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maDanhMuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng KHOAHOC: Thông tin khóa học.
CREATE TABLE KHOAHOC (
    maKH INT NOT NULL AUTO_INCREMENT,
    maDanhMuc INT NOT NULL,                   -- Liên kết danh mục
    maND INT NOT NULL,                        -- Người tạo (giảng viên)
    tenKH VARCHAR(150) NOT NULL,              -- Tên khóa học
    slug VARCHAR(150) NOT NULL UNIQUE,        -- Slug cho URL
    hocPhi DECIMAL(12,2) NOT NULL,            -- Học phí
    moTa VARCHAR(2000),                       -- Mô tả chi tiết
    ngayBatDau DATE,                          -- Ngày bắt đầu
    ngayKetThuc DATE,                         -- Ngày kết thúc
    hinhanh VARCHAR(255),                     -- Hình ảnh đại diện
    thoiHanNgay INT,                          -- Thời hạn khóa học (ngày)
    trangThai ENUM('DRAFT','PUBLISHED','ARCHIVED') DEFAULT 'PUBLISHED',  -- Trạng thái khóa học
    rating_avg DECIMAL(3,2) DEFAULT 0.00,     -- Điểm đánh giá trung bình
    rating_count INT DEFAULT 0,               -- Số lượng đánh giá
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maKH),
    KEY idx_course_category (maDanhMuc),
    CONSTRAINT FK_KH_DM FOREIGN KEY (maDanhMuc) REFERENCES DANHMUC(maDanhMuc) ON DELETE CASCADE,
    CONSTRAINT FK_KH_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng CHUONG: Chương trong khóa học.
CREATE TABLE CHUONG (
    maChuong INT NOT NULL AUTO_INCREMENT,
    maKH INT NOT NULL,                        -- Liên kết khóa học
    tenChuong VARCHAR(255) NOT NULL,          -- Tên chương
    thuTu INT NOT NULL DEFAULT 1,             -- Thứ tự chương
    moTa VARCHAR(1000),                       -- Mô tả chương
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maChuong),
    KEY idx_chuong_kh (maKH),
    CONSTRAINT uq_chuong_order UNIQUE (maKH, thuTu),
    CONSTRAINT FK_CHUONG_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng BAIHOC: Bài học trong chương.
CREATE TABLE BAIHOC (
    maBH INT NOT NULL AUTO_INCREMENT,
    maChuong INT NOT NULL,                    -- Liên kết chương
    tieuDe VARCHAR(150) NOT NULL,             -- Tiêu đề bài học
    moTa VARCHAR(1000),                       -- Mô tả bài học
    thuTu INT DEFAULT 1,                      -- Thứ tự bài học
    loai ENUM('video','doc','quiz','live','assignment') DEFAULT 'video',  -- Loại bài học
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maBH),
    KEY idx_baihoc_chuong (maChuong),
    CONSTRAINT uq_baihoc_order UNIQUE (maChuong, thuTu),
    CONSTRAINT FK_BH_CHUONG FOREIGN KEY (maChuong) REFERENCES CHUONG(maChuong) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng TAILIEUHOCTAP: Tài liệu học tập đính kèm bài học.
CREATE TABLE TAILIEUHOCTAP (
    maTL INT NOT NULL AUTO_INCREMENT,
    maBH INT NOT NULL,                        -- Liên kết bài học
    tenTL VARCHAR(150) NOT NULL,              -- Tên tài liệu
    loai VARCHAR(50),                         -- Loại tài liệu (Video, PDF, Slide,...)
    kichThuoc VARCHAR(50),                    -- Kích thước (ví dụ: '300MB')
    moTa VARCHAR(1000),                       -- Mô tả
    mime_type  VARCHAR(100),                  -- MIME type
    visibility ENUM('public','private') DEFAULT 'public',  -- Quyền truy cập
    public_url VARCHAR(700) COLLATE utf8mb4_bin NOT NULL,  -- URL công khai
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maTL),
    KEY IX_TL_MABH (maBH),
    KEY IX_TL_BH_LOAI (maBH, loai),
    CONSTRAINT FK_TL_BH FOREIGN KEY (maBH) REFERENCES BAIHOC(maBH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3) GHI DANH HỌC VIÊN VÀ TIẾN ĐỘ HỌC TẬP
-- =========================================================
-- Bảng HOCVIEN_KHOAHOC: Ghi danh học viên vào khóa học.
CREATE TABLE HOCVIEN_KHOAHOC (
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    ngayNhapHoc DATE,                         -- Ngày nhập học
    trangThai ENUM('PENDING','ACTIVE','EXPIRED') DEFAULT 'PENDING',  -- Trạng thái ghi danh
    activated_at DATETIME,                    -- Thời điểm kích hoạt
    expires_at DATETIME,                      -- Thời điểm hết hạn
    progress_percent TINYINT DEFAULT 0,       -- % tiến độ tổng quát
    video_progress_percent TINYINT DEFAULT 0, -- % hoàn thành video
    avg_minitest_score DECIMAL(5,2) DEFAULT NULL,  -- Điểm trung bình mini-test
    last_lesson_id INT NULL,                  -- Bài học gần nhất
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maHV, maKH),
    KEY IX_HVK_MAHV (maHV),
    KEY IX_HVK_MAKH (maKH),
    CONSTRAINT FK_HVK_HV   FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_HVK_KH   FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE,
    CONSTRAINT FK_HVK_LAST FOREIGN KEY (last_lesson_id) REFERENCES BAIHOC(maBH) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng TIENDO_HOCTAP: Tiến độ chi tiết theo từng bài học.
CREATE TABLE TIENDO_HOCTAP (
    id INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    maBH INT NOT NULL,                        -- Liên kết bài học
    trangThai ENUM('NOT_STARTED', 'IN_PROGRESS', 'COMPLETED') DEFAULT 'NOT_STARTED',  -- Trạng thái bài học
    thoiGianHoc INT DEFAULT 0 COMMENT 'Tổng thời gian học (giây)',
    lanXemCuoi DATETIME COMMENT 'Lần xem gần nhất',
    soLanXem INT DEFAULT 0 COMMENT 'Số lần truy cập bài học',
    video_progress_seconds INT DEFAULT 0 COMMENT 'Vị trí dừng video (giây)',
    video_duration_seconds INT COMMENT 'Tổng thời lượng video (giây)',
    is_video_completed TINYINT(1) AS (
        CASE
            WHEN video_duration_seconds IS NOT NULL
                AND video_duration_seconds > 0
                AND video_progress_seconds >= video_duration_seconds * 0.9
            THEN 1
            WHEN trangThai = 'COMPLETED' THEN 1
            ELSE 0
        END
    ) STORED COMMENT '1 nếu học viên đã coi gần hết (>=90%) hoặc đánh dấu COMPLETED',
    completed_at DATETIME COMMENT 'Thời điểm hoàn thành bài học',
    ghiChu VARCHAR(500),                      -- Ghi chú
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_progress (maHV, maKH, maBH),
    KEY IX_TD_ENROLL (maHV, maKH),
    KEY IX_TD_BAIHOC (maBH),
    KEY IX_TD_STATUS (trangThai),
    CONSTRAINT FK_TD_ENROLL FOREIGN KEY (maHV, maKH) REFERENCES HOCVIEN_KHOAHOC(maHV, maKH) ON DELETE CASCADE,
    CONSTRAINT FK_TD_BAIHOC FOREIGN KEY (maBH) REFERENCES BAIHOC(maBH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 4) THANH TOÁN
-- =========================================================
-- Bảng PHUONGTHUCTHANHTOAN: Các phương thức thanh toán (ví dụ: chuyển khoản, thẻ tín dụng).
CREATE TABLE PHUONGTHUCTHANHTOAN (
    maTT VARCHAR(10) NOT NULL,
    tenPhuongThuc VARCHAR(100) NOT NULL,      -- Tên phương thức
    PRIMARY KEY (maTT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE GIAODICH_VNPAY (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,              -- Học viên mua (FK tới HOCVIEN.maHV)
    maKH INT NOT NULL,              -- Khóa học được mua (FK tới KHOAHOC.maKH)
    soTien DECIMAL(12,2) NOT NULL,  -- Số tiền VND bạn dự định thu cho khóa học tại thời điểm bấm thanh toán

    txn_ref VARCHAR(64) NOT NULL,   -- Mã đơn gửi sang VNPay (vnp_TxnRef)
    vnp_response_code VARCHAR(10) NULL,     -- Mã phản hồi VNPay (vnp_ResponseCode)
    vnp_transaction_no VARCHAR(50) NULL,    -- Mã giao dịch tại VNPay/ngân hàng (vnp_TransactionNo)

    trangThai ENUM('PENDING','PAID','FAILED') DEFAULT 'PENDING',
    paid_at DATETIME NULL,          -- Thời điểm bạn xác nhận thanh toán thành công (sau IPN)

    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_txnref (txn_ref),
    KEY IX_GDVNPAY_HVKH (maHV, maKH),

    CONSTRAINT FK_GDVNPAY_HV FOREIGN KEY (maHV)
        REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,

    CONSTRAINT FK_GDVNPAY_KH FOREIGN KEY (maKH)
        REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng HOADON: Hóa đơn thanh toán.
CREATE TABLE HOADON (
    maHD INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,                        -- Học viên thanh toán
    maTT VARCHAR(10),                         -- Phương thức thanh toán
    maND INT,                                 -- Người xử lý (giáo vụ/admin)
    ngayLap DATETIME,                         -- Ngày lập hóa đơn
    tongTien DECIMAL(12,2),                   -- Tổng tiền
    ghiChu VARCHAR(255),                      -- Ghi chú
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maHD),
    KEY IX_HD_MAHV (maHV),
    KEY IX_HD_MATT (maTT),
    KEY IX_HD_MAND (maND),
    CONSTRAINT FK_HD_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_HD_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE SET NULL,
    CONSTRAINT FK_HD_TT FOREIGN KEY (maTT) REFERENCES PHUONGTHUCTHANHTOAN(maTT) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng CTHD: Chi tiết hóa đơn (các khóa học trong hóa đơn).
CREATE TABLE CTHD (
    maHD INT NOT NULL,
    maKH INT NOT NULL,                        -- Khóa học mua
    soLuong INT NOT NULL,                     -- Số lượng (thường là 1)
    donGia DECIMAL(12,2) NOT NULL,            -- Đơn giá
    thanhTien DECIMAL(12,2) AS (soLuong * donGia) STORED,  -- Thành tiền tính toán
    PRIMARY KEY (maHD, maKH),
    KEY IX_CTHD_MAHD (maHD),
    KEY IX_CTHD_MAKH (maKH),
    CONSTRAINT FK_CTHD_HD FOREIGN KEY (maHD) REFERENCES HOADON(maHD) ON DELETE CASCADE,
    CONSTRAINT FK_CTHD_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 5) MÃ KÍCH HOẠT & MINI-TEST (THEO CHƯƠNG) & KẾT QUẢ
-- =========================================================
-- Bảng MA_KICH_HOAT: Mã kích hoạt ghi danh khóa học.
CREATE TABLE MA_KICH_HOAT (
    id INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,
    maKH INT NOT NULL,                        -- Liên kết ghi danh
    maHD INT NULL,                            -- Liên kết hóa đơn
    code VARCHAR(50) NOT NULL UNIQUE,         -- Mã code
    trangThai ENUM('CREATED','SENT','USED','EXPIRED') DEFAULT 'CREATED',  -- Trạng thái mã
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    sent_at DATETIME NULL,
    used_at DATETIME NULL,
    expires_at DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_MAKH_CODE (code),
    KEY IX_MAKH_ENROLL (maHV, maKH),
    KEY IX_MAKH_HD (maHD),
    CONSTRAINT FK_MAKH_ENROLL FOREIGN KEY (maHV, maKH) REFERENCES HOCVIEN_KHOAHOC(maHV, maKH) ON DELETE CASCADE,
    CONSTRAINT FK_MAKH_HD FOREIGN KEY (maHD) REFERENCES HOADON(maHD) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng CHUONG_MINITEST: Mini-test theo chương.
CREATE TABLE CHUONG_MINITEST (
    maMT INT NOT NULL AUTO_INCREMENT,
    maKH INT NOT NULL,                        -- Liên kết khóa học
    maChuong INT NOT NULL,                    -- Liên kết chương
    title VARCHAR(150) NOT NULL,              -- Tiêu đề mini-test
    thuTu INT NOT NULL DEFAULT 1,             -- Thứ tự
    max_score DECIMAL(5,2) DEFAULT 10.00,     -- Thang điểm tối đa
    trongSo  DECIMAL(5,2) DEFAULT 0.00,       -- Trọng số
    time_limit_min INT,                       -- Thời gian giới hạn (phút)
    attempts_allowed TINYINT DEFAULT 1,       -- Số lần thử
    is_active TINYINT(1) DEFAULT 1,           -- Hoạt động hay không
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maMT),
    KEY IX_MT_KH (maKH),
    KEY IX_MT_CHUONG (maChuong),
    CONSTRAINT uq_mt_order UNIQUE (maChuong, thuTu),
    CONSTRAINT FK_MT_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE,
    CONSTRAINT FK_MT_CH FOREIGN KEY (maChuong) REFERENCES CHUONG(maChuong) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng MINITEST_TAILIEU: Tài liệu đính kèm mini-test.
CREATE TABLE MINITEST_TAILIEU (
    id INT NOT NULL AUTO_INCREMENT,
    maMT INT NOT NULL,                        -- Liên kết mini-test
    tenTL VARCHAR(255) NOT NULL,              -- Tên tài liệu
    loai VARCHAR(50) NOT NULL,                -- Loại (PDF, ZIP, Video,...)
    mime_type VARCHAR(100) NOT NULL,          -- MIME type
    visibility ENUM('public','private') DEFAULT 'public',  -- Quyền truy cập
    public_url VARCHAR(700) COLLATE utf8mb4_bin NOT NULL,  -- URL công khai
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_MTTL_MT (maMT),
    CONSTRAINT FK_MTTL_MT FOREIGN KEY (maMT) REFERENCES CHUONG_MINITEST(maMT) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng MINITEST_QUESTIONS: Câu hỏi trong mini-test.
CREATE TABLE MINITEST_QUESTIONS (
    maCauHoi INT NOT NULL AUTO_INCREMENT,
    maMT INT NOT NULL,                        -- Liên kết mini-test
    thuTu INT NOT NULL DEFAULT 1,             -- Thứ tự câu hỏi
    loai ENUM('single_choice', 'multiple_choice', 'true_false', 'short_answer') DEFAULT 'single_choice',  -- Loại câu hỏi
    noiDungCauHoi TEXT NOT NULL,              -- Nội dung câu hỏi
    phuongAnA TEXT NULL,                      -- Phương án A
    phuongAnB TEXT NULL,                      -- Phương án B
    phuongAnC TEXT NULL,                      -- Phương án C
    phuongAnD TEXT NULL,                      -- Phương án D
    dapAnDung VARCHAR(50) NULL,               -- Đáp án đúng (ví dụ: 'A', 'A;C', 'TRUE')
    giaiThich TEXT NULL,                      -- Giải thích
    diem DECIMAL(5,2) DEFAULT 1.00,           -- Điểm câu hỏi
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maCauHoi),
    CONSTRAINT uq_mtq_order UNIQUE (maMT, thuTu),
    KEY IX_MTQ_MT (maMT),
    CONSTRAINT FK_MTQ_MT FOREIGN KEY (maMT) REFERENCES CHUONG_MINITEST(maMT) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng KETQUA_MINITEST: Kết quả mini-test của học viên.
CREATE TABLE KETQUA_MINITEST (
    maKQDG INT NOT NULL AUTO_INCREMENT,
    maMT INT NOT NULL,                        -- Liên kết mini-test
    maHV INT NOT NULL,
    maKH INT NOT NULL,                        -- Liên kết ghi danh
    attempt_no TINYINT DEFAULT 1,             -- Lần thử
    diem DECIMAL(5,2),                        -- Điểm đạt được
    nhanxet VARCHAR(1000),                    -- Nhận xét
    nop_luc DATETIME,                         -- Thời điểm nộp
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maKQDG),
    UNIQUE KEY uq_kqdg (maMT, maHV, maKH, attempt_no),
    KEY IX_KQDG_MT (maMT),
    KEY IX_KQDG_ENROLL (maHV, maKH),
    CONSTRAINT FK_KQDG_MT     FOREIGN KEY (maMT)        REFERENCES CHUONG_MINITEST(maMT) ON DELETE CASCADE,
    CONSTRAINT FK_KQDG_ENROLL FOREIGN KEY (maHV, maKH)  REFERENCES HOCVIEN_KHOAHOC(maHV, maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 6) ĐÁNH GIÁ KHÓA HỌC & YÊU THÍCH
-- =========================================================
-- Bảng DANHGIAKH: Đánh giá khóa học từ học viên.
CREATE TABLE DANHGIAKH (
    maDG INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,                        -- Học viên đánh giá
    maKH INT NOT NULL,                        -- Khóa học
    diemSo DECIMAL(3,2),                      -- Điểm số
    ngayDG DATETIME,                          -- Ngày đánh giá
    nhanxet VARCHAR(1000),                    -- Nhận xét
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maDG),
    UNIQUE KEY uq_review (maHV, maKH),
    KEY IX_DGKH_MAHV (maHV),
    KEY IX_DGKH_MAKH (maKH),
    CONSTRAINT FK_DGKH_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_DGKH_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng KHOAHOC_YEUTHICH: Khóa học yêu thích của học viên.
CREATE TABLE KHOAHOC_YEUTHICH (
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (maHV, maKH),
    CONSTRAINT FK_YT_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_YT_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 7) CHỨNG CHỈ
-- =========================================================
-- Bảng CHUNGCHI: Chứng chỉ cấp cho học viên sau khóa học.
CREATE TABLE CHUNGCHI (
    maCC INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,                        -- Học viên
    maKH INT NOT NULL,                        -- Khóa học
    tenCC VARCHAR(100),                       -- Tên chứng chỉ
    moTa VARCHAR(1000),                       -- Mô tả
    code VARCHAR(50) UNIQUE,                  -- Mã chứng chỉ
    trangThai ENUM('PENDING','ISSUED','REVOKED') DEFAULT 'PENDING',  -- Trạng thái
    issued_at DATETIME,                       -- Thời điểm cấp
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maCC),
    KEY IX_CC_MAHV (maHV),
    CONSTRAINT FK_CC_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_CC_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng CHUNGCHI_DANHGIA: Đánh giá/ghi nhận cho chứng chỉ (nhiều bản ghi).
CREATE TABLE CHUNGCHI_DANHGIA (
    id INT NOT NULL AUTO_INCREMENT,
    maCC INT NOT NULL,                        -- Liên kết chứng chỉ
    diem DECIMAL(5,2),                        -- Điểm
    ngayCap DATE,                             -- Ngày cấp
    ghiChu VARCHAR(255),                      -- Ghi chú
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_CCDG_CC (maCC),
    CONSTRAINT FK_CCDG_CC FOREIGN KEY (maCC) REFERENCES CHUNGCHI(maCC) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 8) BẢNG HỆ THỐNG LARAVEL
-- =========================================================
-- Bảng cache: Lưu trữ cache hệ thống.
CREATE TABLE cache (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng sessions: Quản lý session người dùng.
CREATE TABLE sessions (
    `id` varchar(255) NOT NULL,
    `user_id` bigint unsigned NULL,
    `ip_address` varchar(45) NULL,
    `user_agent` text NULL,
    `payload` longtext NOT NULL,
    `last_activity` int NOT NULL,
    PRIMARY KEY (`id`),
    KEY `sessions_user_id_index` (`user_id`),
    KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng jobs: Quản lý hàng đợi công việc.
CREATE TABLE jobs (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `queue` varchar(255) NOT NULL,
    `payload` longtext NOT NULL,
    `attempts` tinyint unsigned NOT NULL,
    `reserved_at` int unsigned DEFAULT NULL,
    `available_at` int unsigned NOT NULL,
    `created_at` int unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng failed_jobs: Công việc thất bại.
CREATE TABLE failed_jobs (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `uuid` varchar(255) DEFAULT NULL,
    `connection` text NOT NULL,
    `queue` text NOT NULL,
    `payload` longtext NOT NULL,
    `exception` longtext NOT NULL,
    `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng job_batches: Batch công việc.
CREATE TABLE job_batches (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) DEFAULT NULL,
    `total_jobs` int NOT NULL,
    `pending_jobs` int NOT NULL,
    `failed_jobs` int NOT NULL,
    `failed_job_ids` longtext,
    `options` mediumtext,
    `cancelled_at` int DEFAULT NULL,
    `created_at` int NOT NULL,
    `finished_at` int DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
