USE Online_Certificate_Classroom;

START TRANSACTION;

SELECT maND INTO @admin_id FROM nguoidung WHERE vaiTro='ADMIN' ORDER BY maND ASC LIMIT 1;
SET @admin_id = IFNULL(@admin_id, 1);

INSERT INTO certificate_template (loaiTemplate, maKH, maGoi, tenTemplate, moTa, design_json, template_url, created_by, trangThai) VALUES
('COURSE', NULL, NULL, 'OCC Modern Blue', 'Mẫu tối giản với màu xanh chủ đạo của OCC, phù hợp cho hầu hết khóa học.', '{"primary":"#2563eb","primaryDark":"#1d4ed8","accent":"#22d3ee","text":"#0f172a","muted":"#475569"}', 'Assets/Images/certificate-watermark-wave.svg', @admin_id, 'ACTIVE'),
('COURSE', NULL, NULL, 'OCC Orbit Gradient', 'Hiệu ứng quỹ đạo và điểm nhấn cam nhẹ, thích hợp cho chứng chỉ kỹ năng.', '{"primary":"#1e3a8a","primaryDark":"#0f172a","accent":"#f97316","text":"#0b1220","muted":"#475569"}', 'Assets/Images/certificate-watermark-orbit.svg', @admin_id, 'DRAFT'),
('COURSE', NULL, NULL, 'OCC Ribbon Contrast', 'Ribbons tương phản, nổi bật logo và mã chứng chỉ.', '{"primary":"#0f172a","primaryDark":"#111827","accent":"#f59e0b","text":"#0f172a","muted":"#6b7280"}', 'Assets/Images/certificate-watermark-ribbon.svg', @admin_id, 'ARCHIVED')
ON DUPLICATE KEY UPDATE
    tenTemplate = VALUES(tenTemplate),
    moTa = VALUES(moTa),
    design_json = VALUES(design_json),
    template_url = VALUES(template_url),
    trangThai = VALUES(trangThai),
    updated_at = CURRENT_TIMESTAMP;

COMMIT;
