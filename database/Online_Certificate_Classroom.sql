-- =========================================================
-- QL_KHOAHOC - HỆ THỐNG QUẢN LÝ LỚP HỌC CHỨNG CHỈ TRỰC TUYẾN
-- =========================================================
DROP DATABASE IF EXISTS Online_Certificate_Classroom;
CREATE DATABASE Online_Certificate_Classroom
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE Online_Certificate_Classroom;

-- =========================================================
-- 1) PHÂN QUYỀN & NGƯỜI DÙNG
-- =========================================================
CREATE TABLE QUYEN (
    maQuyen VARCHAR(10)  NOT NULL,
    tenQuyen VARCHAR(50) NOT NULL,
    PRIMARY KEY (maQuyen)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE NGUOIDUNG (
    maND INT NOT NULL AUTO_INCREMENT,
    hoTen VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    sdt VARCHAR(20),
    matKhau VARCHAR(255) NOT NULL,
    chuyenMon VARCHAR(255),
    vaiTro ENUM('ADMIN','GIAO_VU','GIANG_VIEN','HOC_VIEN') DEFAULT 'HOC_VIEN',
    trangThai ENUM('ACTIVE','INACTIVE') DEFAULT 'ACTIVE',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maND)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE QUYEN_NGUOIDUNG (
    maND INT NOT NULL,
    maQuyen VARCHAR(10) NOT NULL,
    PRIMARY KEY (maND, maQuyen),
    KEY IX_QND_MAQUYEN (maQuyen),
    CONSTRAINT FK_QND_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE CASCADE,
    CONSTRAINT FK_QND_Q  FOREIGN KEY (maQuyen) REFERENCES QUYEN(maQuyen) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hồ sơ Học viên (1-1 NGUOIDUNG)
CREATE TABLE HOCVIEN (
    maHV INT NOT NULL AUTO_INCREMENT,
    maND INT NOT NULL UNIQUE,
    hoTen VARCHAR(100),
    ngaySinh DATE,
    ngayNhapHoc DATE,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maHV),
    CONSTRAINT FK_HV_ND FOREIGN KEY (maND) REFERENCES NGUOIDUNG(maND) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 2) DANH MỤC / KHÓA HỌC / CHƯƠNG / BÀI
-- =========================================================
CREATE TABLE DANHMUC (
    maDanhMuc INT NOT NULL AUTO_INCREMENT,
    tenDanhMuc VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL UNIQUE,
    icon VARCHAR(100),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maDanhMuc)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE KHOAHOC (
    maKH INT NOT NULL AUTO_INCREMENT,
    maDanhMuc INT NOT NULL,
    tenKH VARCHAR(150) NOT NULL,
    slug VARCHAR(150) NOT NULL UNIQUE,
    hocPhi DECIMAL(12,2) NOT NULL,
    moTa VARCHAR(2000),
    ngayBatDau DATE,
    ngayKetThuc DATE,
    hinhanh VARCHAR(255),
    thoiHanNgay INT,
    trangThai ENUM('DRAFT','PUBLISHED','ARCHIVED') DEFAULT 'PUBLISHED',
    rating_avg DECIMAL(3,2) DEFAULT 0.00,
    rating_count INT DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maKH),
    KEY idx_course_category (maDanhMuc),
    CONSTRAINT FK_KH_DM FOREIGN KEY (maDanhMuc) REFERENCES DANHMUC(maDanhMuc) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- CHƯƠNG thuộc KHÓA HỌC
CREATE TABLE CHUONG (
    maChuong INT NOT NULL AUTO_INCREMENT,
    maKH INT NOT NULL,
    tenChuong VARCHAR(255) NOT NULL,
    thuTu INT NOT NULL DEFAULT 1,
    moTa VARCHAR(1000),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maChuong),
    KEY idx_chuong_kh (maKH),
    CONSTRAINT uq_chuong_order UNIQUE (maKH, thuTu),
    CONSTRAINT FK_CHUONG_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- BÀI HỌC thuộc CHƯƠNG
CREATE TABLE BAIHOC (
    maBH INT NOT NULL AUTO_INCREMENT,
    maChuong INT NOT NULL,
    tieuDe VARCHAR(150) NOT NULL,
    moTa VARCHAR(1000),
    thuTu INT DEFAULT 1,
    loai ENUM('video','doc','quiz','live','assignment') DEFAULT 'video',
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maBH),
    KEY idx_baihoc_chuong (maChuong),
    CONSTRAINT uq_baihoc_order UNIQUE (maChuong, thuTu),
    CONSTRAINT FK_BH_CHUONG FOREIGN KEY (maChuong) REFERENCES CHUONG(maChuong) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 3) TÀI NGUYÊN (R2)
-- =========================================================
CREATE TABLE TAILIEUHOCTAP (
    maTL INT NOT NULL AUTO_INCREMENT,
    maBH INT NOT NULL,
    tenTL VARCHAR(150) NOT NULL,
    loai VARCHAR(50),                 -- 'Video','PDF','Slide',...
    kichThuoc VARCHAR(50),            -- ví dụ '300MB' (hiển thị)
    moTa VARCHAR(1000),
    mime_type  VARCHAR(100),
    visibility ENUM('public','private') DEFAULT 'public',
    public_url TEXT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maTL),
    KEY IX_TL_MABH (maBH),
    KEY IX_TL_BH_LOAI (maBH, loai),
    CONSTRAINT FK_TL_BH FOREIGN KEY (maBH) REFERENCES BAIHOC(maBH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ngăn trùng tài nguyên theo từng bài học (dùng prefix 191 cho TEXT)
CREATE UNIQUE INDEX uq_tl_public ON TAILIEUHOCTAP (maBH, public_url(191));

-- =========================================================
-- 4) ENROLL, MINI-TEST (THEO CHƯƠNG) & KẾT QUẢ
-- =========================================================
CREATE TABLE HOCVIEN_KHOAHOC (
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    ngayNhapHoc DATE,
    trangThai ENUM('PENDING','ACTIVE','EXPIRED') DEFAULT 'ACTIVE',
    activated_at DATETIME,
    expires_at DATETIME,
    progress_percent TINYINT DEFAULT 0,
    last_lesson_id INT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maHV, maKH),
    KEY IX_HVK_MAHV (maHV),
    KEY IX_HVK_MAKH (maKH),
    CONSTRAINT FK_HVK_HV   FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_HVK_KH   FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE,
    CONSTRAINT FK_HVK_LAST FOREIGN KEY (last_lesson_id) REFERENCES BAIHOC(maBH) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MINI-TEST CHỈ THEO CHƯƠNG (khóa chặt ở DB)
CREATE TABLE CHUONG_MINITEST (
    maMT INT NOT NULL AUTO_INCREMENT,
    maKH INT NOT NULL,
    maChuong INT NOT NULL,
    title VARCHAR(150) NOT NULL,
    thuTu INT NOT NULL DEFAULT 1,
    max_score DECIMAL(5,2) DEFAULT 10.00,
    trongSo  DECIMAL(5,2) DEFAULT 0.00,
    time_limit_min INT,
    attempts_allowed TINYINT DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maMT),
    KEY IX_MT_KH (maKH),
    KEY IX_MT_CHUONG (maChuong),
    CONSTRAINT uq_mt_order UNIQUE (maChuong, thuTu),
    CONSTRAINT FK_MT_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE,
    CONSTRAINT FK_MT_CH FOREIGN KEY (maChuong) REFERENCES CHUONG(maChuong) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tài liệu/đề đính kèm cho MINI-TEST (R2 public)
CREATE TABLE MINITEST_TAILIEU (
    id INT NOT NULL AUTO_INCREMENT,
    maMT INT NOT NULL,
    tenTL VARCHAR(255) NOT NULL,
    loai VARCHAR(50) NOT NULL,             -- PDF/ZIP/Video...
    mime_type VARCHAR(100) NOT NULL,
    visibility ENUM('public','private') DEFAULT 'public',
    public_url TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_MTTL_MT (maMT),
    CONSTRAINT FK_MTTL_MT FOREIGN KEY (maMT) REFERENCES CHUONG_MINITEST(maMT) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Không trùng file theo mini-test
CREATE UNIQUE INDEX uq_mttl_public ON MINITEST_TAILIEU (maMT, public_url(191));

-- Kết quả mini-test (ràng buộc vào lần ghi danh)
CREATE TABLE KETQUA_MINITEST (
    maKQDG INT NOT NULL AUTO_INCREMENT,
    maMT INT NOT NULL,
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    attempt_no TINYINT DEFAULT 1,
    diem DECIMAL(5,2),
    nhanxet VARCHAR(1000),
    nop_luc DATETIME,
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
-- 5) THI CUỐI KHÓA & KẾT QUẢ CUỐI KHÓA
-- =========================================================
CREATE TABLE TEST (
    maTest INT NOT NULL AUTO_INCREMENT,
    maKH INT NOT NULL,
    dotTest VARCHAR(50),
    title VARCHAR(150),
    time_limit_min INT,
    total_questions INT,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maTest),
    KEY IX_TEST_MAKH (maKH),
    CONSTRAINT FK_TEST_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tài liệu/đề đính kèm cho FINAL TEST (R2 public)
CREATE TABLE TEST_TAILIEU (
    id INT NOT NULL AUTO_INCREMENT,
    maTest INT NOT NULL,
    tenTL VARCHAR(255) NOT NULL,
    loai VARCHAR(50) NOT NULL,             -- PDF/ZIP/Video...
    mime_type VARCHAR(100) NOT NULL,
    visibility ENUM('public','private') DEFAULT 'public',
    public_url TEXT NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_TESTTL_TEST (maTest),
    CONSTRAINT FK_TESTTL_TEST FOREIGN KEY (maTest) REFERENCES TEST(maTest) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Không trùng file theo final test
CREATE UNIQUE INDEX uq_testtl_public ON TEST_TAILIEU (maTest, public_url(191));

CREATE TABLE KETQUAHOCTAP (
    maKQ INT NOT NULL AUTO_INCREMENT,
    maTest INT NOT NULL,
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    attempt_no TINYINT DEFAULT 1,
    diemsoKQThi DECIMAL(5,2),
    nhanxet VARCHAR(1000),
    ngayNop DATETIME,
    ngayTest DATETIME,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maKQ),
    UNIQUE KEY uq_kq (maTest, maHV, maKH, attempt_no),
    KEY IX_KQ_TEST (maTest),
    KEY IX_KQ_ENROLL (maHV, maKH),
    CONSTRAINT FK_KQ_TEST   FOREIGN KEY (maTest)     REFERENCES TEST(maTest) ON DELETE CASCADE,
    CONSTRAINT FK_KQ_ENROLL FOREIGN KEY (maHV, maKH) REFERENCES HOCVIEN_KHOAHOC(maHV, maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 6) ĐÁNH GIÁ KHÓA HỌC & YÊU THÍCH
-- =========================================================
CREATE TABLE DANHGIAKH (
    maDG INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    diemSo DECIMAL(3,2),
    ngayDG DATETIME,
    nhanxet VARCHAR(1000),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maDG),
    UNIQUE KEY uq_review (maHV, maKH),
    KEY IX_DGKH_MAHV (maHV),
    KEY IX_DGKH_MAKH (maKH),
    CONSTRAINT FK_DGKH_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_DGKH_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
CREATE TABLE CHUNGCHI (
    maCC INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,
    maKH INT NOT NULL,
    tenCC VARCHAR(100),
    moTa VARCHAR(1000),
    code VARCHAR(50) UNIQUE,
    trangThai ENUM('PENDING','ISSUED','REVOKED') DEFAULT 'PENDING',
    issued_at DATETIME,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (maCC),
    KEY IX_CC_MAHV (maHV),
    CONSTRAINT FK_CC_HV FOREIGN KEY (maHV) REFERENCES HOCVIEN(maHV) ON DELETE CASCADE,
    CONSTRAINT FK_CC_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 1 chứng chỉ có thể có nhiều bản ghi chấm/ghi nhận
CREATE TABLE CHUNGCHI_DANHGIA (
    id INT NOT NULL AUTO_INCREMENT,
    maCC INT NOT NULL,
    diem DECIMAL(5,2),
    ngayCap DATE,
    ghiChu VARCHAR(255),
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY IX_CCDG_CC (maCC),
    CONSTRAINT FK_CCDG_CC FOREIGN KEY (maCC) REFERENCES CHUNGCHI(maCC) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 8) THANH TOÁN
-- =========================================================
CREATE TABLE PHUONGTHUCTHANHTOAN (
    maTT VARCHAR(10) NOT NULL,
    tenPhuongThuc VARCHAR(100) NOT NULL,
    PRIMARY KEY (maTT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE HOADON (
    maHD INT NOT NULL AUTO_INCREMENT,
    maHV INT NOT NULL,
    maTT VARCHAR(10),
    maND INT,
    ngayLap DATETIME,
    tongTien DECIMAL(12,2),
    ghiChu VARCHAR(255),
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

CREATE TABLE CTHD (
    maHD INT NOT NULL,
    maKH INT NOT NULL,
    soLuong INT NOT NULL,
    donGia DECIMAL(12,2) NOT NULL,
    thanhTien DECIMAL(12,2) AS (soLuong * donGia) STORED,
    PRIMARY KEY (maHD, maKH),
    KEY IX_CTHD_MAHD (maHD),
    KEY IX_CTHD_MAKH (maKH),
    CONSTRAINT FK_CTHD_HD FOREIGN KEY (maHD) REFERENCES HOADON(maHD) ON DELETE CASCADE,
    CONSTRAINT FK_CTHD_KH FOREIGN KEY (maKH) REFERENCES KHOAHOC(maKH) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- 9) BẢNG HỆ THỐNG LARAVEL
-- =========================================================
CREATE TABLE cache (
    `key` varchar(255) NOT NULL,
    `value` mediumtext NOT NULL,
    `expiration` int NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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