// =============================================================
// Admin Courses JS (full)
// - Flash message (SweetAlert)
// - Modal Edit: đổ dữ liệu
// - Validate forms
// - Xác nhận xóa
// - Định dạng học phí (giữ raw khi submit)
// - File preview gọn gàng
// - Auto tính Thời hạn (ngày) <=> Start/End cho cả 2 modal
// =============================================================

document.addEventListener("DOMContentLoaded", () => {
    // ---------------- 0) Flash messages ----------------
    (function showFlash() {
        const el = document.getElementById("flash-data");
        if (!el || typeof Swal === "undefined") return;
        const { success, error } = el.dataset;
        if (error) {
            Swal.fire({
                icon: "error",
                title: "Thất bại",
                text: error,
                confirmButtonText: "OK",
            });
        } else if (success) {
            Swal.fire({
                icon: "success",
                title: "Thành công",
                text: success,
                timer: 2000,
                showConfirmButton: false,
            });
        }
    })();

    // ---------------- Helpers cho ngày & thời hạn ----------------
    const DAY_MS = 24 * 60 * 60 * 1000;

    function parseDate(yyyy_mm_dd) {
        if (!yyyy_mm_dd) return null;
        const d = new Date(yyyy_mm_dd + "T00:00:00"); // tránh lệch tz
        return Number.isNaN(d.getTime()) ? null : d;
    }
    function fmtDate(d) {
        if (!(d instanceof Date) || Number.isNaN(d.getTime())) return "";
        const m = String(d.getMonth() + 1).padStart(2, "0");
        const day = String(d.getDate()).padStart(2, "0");
        return `${d.getFullYear()}-${m}-${day}`;
    }
    function wireDateDuration({ startEl, endEl, durationEl }) {
        if (!startEl || !endEl || !durationEl) return;

        function clearValidity() {
            startEl.setCustomValidity("");
            endEl.setCustomValidity("");
            durationEl.setCustomValidity("");
        }

        function calcDurationFromDates() {
            const s = parseDate(startEl.value);
            const e = parseDate(endEl.value);
            if (!s || !e) return;
            if (e < s) {
                endEl.setCustomValidity("Ngày kết thúc phải ≥ ngày bắt đầu.");
                durationEl.value = "";
                return;
            }
            clearValidity();
            const diff = Math.round((e - s) / DAY_MS) + 1; // inclusive
            durationEl.value = diff;
        }

        function calcEndFromDuration() {
            const s = parseDate(startEl.value);
            const dur = parseInt(durationEl.value, 10);
            if (!s || !Number.isInteger(dur) || dur < 1) return;
            clearValidity();
            const e = new Date(s.getTime() + (dur - 1) * DAY_MS);
            endEl.value = fmtDate(e);
        }

        // Khi đổi start:
        startEl.addEventListener("change", () => {
            // Nếu đã có duration mà chưa có end -> tính end; ngược lại -> tính duration
            if (durationEl.value && !endEl.value) calcEndFromDuration();
            else calcDurationFromDates();
        });
        endEl.addEventListener("change", calcDurationFromDates);
        durationEl.addEventListener("input", calcEndFromDuration);

        // Lần đầu mount: nếu có s & e -> set duration; nếu có s & duration -> set e
        (function initSync() {
            const s = parseDate(startEl.value);
            const e = parseDate(endEl.value);
            const dur = parseInt(durationEl.value, 10);
            if (s && e) calcDurationFromDates();
            else if (s && Number.isInteger(dur) && dur > 0)
                calcEndFromDuration();
        })();
    }

    // ---------------- 1) Modal Edit: Load data ----------------
    const modalEdit = document.getElementById("modalEdit");
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const form = modalEdit.querySelector("#formEdit");
            const id = btn?.getAttribute("data-id");
            form.action = id ? `/admin/courses/${id}` : "";

            // Map thuộc tính data -> field id
            const fields = {
                e_name: "data-name",
                e_category: "data-category",
                e_teacher: "data-teacher",
                e_fee: "data-fee",
                e_duration: "data-duration",
                e_start: "data-start",
                e_end: "data-end",
                e_desc: "data-desc",
                e_status: "data-status",
            };

            Object.keys(fields).forEach((fid) => {
                const val = btn?.getAttribute(fields[fid]) || "";
                const el = modalEdit.querySelector(`#${fid}`);
                if (!el) return;

                if (el.tagName === "SELECT") {
                    if (fid === "e_status") {
                        el.value = val || "DRAFT"; // chỉ mặc định cho trạng thái
                    } else {
                        if (val) el.value = val; // category/teacher: chỉ set khi có giá trị
                    }
                } else {
                    el.value = val;
                }
            });

            // Định dạng và lưu giá trị gốc cho hocPhi sau khi tải dữ liệu
            const feeInput = modalEdit.querySelector("#e_fee");
            if (feeInput && feeInput.value) {
                const raw = feeInput.value.replace(/\D/g, "");
                feeInput.dataset.rawValue = raw;
                feeInput.value = raw
                    ? parseInt(raw, 10).toLocaleString("vi-VN")
                    : "";
            }
            
            // Reset lỗi cũ
            modalEdit
                .querySelectorAll(".is-invalid")
                .forEach((el) => el.classList.remove("is-invalid"));

            // Re-wire auto date-duration cho modal Edit
            const eStart = modalEdit.querySelector("#e_start");
            const eEnd = modalEdit.querySelector("#e_end");
            const eDur = modalEdit.querySelector("#e_duration");
            wireDateDuration({
                startEl: eStart,
                endEl: eEnd,
                durationEl: eDur,
            });
        });
    }

    // ---------------- 2) Form validation & submission ----------------
    function validateCourseForm(form) {
        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            // cho input[type="file"] không bắt buộc -> skip nếu không required
            if (!field.value || !String(field.value).trim()) {
                isValid = false;
                field.classList.add("is-invalid");
            } else field.classList.remove("is-invalid");
        });

        // Kiểm tra trạng thái hợp lệ (nếu có)
        const statusField = form.querySelector("#e_status");
        if (
            statusField &&
            !["DRAFT", "PUBLISHED", "ARCHIVED"].includes(statusField.value)
        ) {
            isValid = false;
            statusField.classList.add("is-invalid");
        }

        // Kiểm tra logic ngày (nếu có 2 trường)
        const startEl = form.querySelector('input[name="ngayBatDau"]');
        const endEl = form.querySelector('input[name="ngayKetThuc"]');
        if (startEl && endEl && startEl.value && endEl.value) {
            const s = parseDate(startEl.value);
            const e = parseDate(endEl.value);
            if (s && e && e < s) {
                isValid = false;
                endEl.classList.add("is-invalid");
                endEl.setCustomValidity("Ngày kết thúc phải ≥ ngày bắt đầu.");
            } else if (endEl) {
                endEl.setCustomValidity("");
            }
        }

        return isValid;
    }

    // Áp dụng validate cho cả 2 modal
    [
        document.getElementById("modalCreate"),
        document.getElementById("modalEdit"),
    ].forEach((modal) => {
        if (!modal) return;
        const form = modal.querySelector("form");
        if (!form) return;

        form.addEventListener("submit", function (e) {
            if (!validateCourseForm(form)) {
                e.preventDefault();
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "error",
                        title: "Vui lòng điền đầy đủ thông tin!",
                        text: "Kiểm tra các trường bắt buộc, ngày tháng và trạng thái hợp lệ.",
                        timer: 2200,
                        showConfirmButton: false,
                    });
                }
                return false;
            }
        });
    });

    // ---------------- 3) Delete confirmation ----------------
    const isDeleteForm = (form) => {
        const hiddenMethod = form.querySelector("input[name='_method']");
        return (
            hiddenMethod &&
            String(hiddenMethod.value).toLowerCase() === "delete"
        );
    };
    document.querySelectorAll("form").forEach((form) => {
        if (!isDeleteForm(form)) return;
        form.addEventListener("submit", function (event) {
            if (form.dataset.confirmed === "true") return;
            event.preventDefault();

            const submitBtn = form.querySelector(
                "button[type='submit'], .btn-danger, .btn-danger-soft"
            );
            if (submitBtn?.disabled) return;

            if (typeof Swal === "undefined") {
                if (confirm("Xoá khóa học này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
                return;
            }
            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xoá khóa học và không thể hoàn tác.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xoá",
                cancelButtonText: "Huỷ",
                confirmButtonColor: "#d33",
            }).then((res) => {
                if (res.isConfirmed) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
            });
        });
    });

    // ---------------- 4) Format hocPhi ----------------
    document.querySelectorAll('input[name="hocPhi"]').forEach((input) => {
        input.dataset.rawValue = input.value
            ? input.value.replace(/\D/g, "")
            : "";

        input.addEventListener("input", function () {
            let value = this.value.replace(/\D/g, "");
            if (value) {
                this.dataset.rawValue = value;
                this.value = parseInt(value, 10).toLocaleString("vi-VN");
            } else {
                this.dataset.rawValue = "";
                this.value = "";
            }
        });

        input.addEventListener("blur", function () {
            let value = this.dataset.rawValue;
            this.value = value
                ? parseInt(value, 10).toLocaleString("vi-VN")
                : "";
        });

        const form = input.closest("form");
        if (form) {
            form.addEventListener("submit", function () {
                input.value = input.dataset.rawValue || "";
            });
        }
    });

    // ---------------- 5) File preview (không cộng dồn) ----------------
    document.querySelectorAll('input[type="file"]').forEach((input) => {
        input.addEventListener("change", function () {
            const fileName = this.files[0]?.name || "";
            // tìm label ngay trước input (cùng group)
            const wrapper =
                this.closest(".col-12, .mb-3, .form-group") ||
                this.parentElement;
            const label = wrapper
                ? wrapper.querySelector("label.form-label")
                : null;
            if (!label) return;

            // reset về text gốc nếu đã có " (đã chọn: ... )"
            const baseText =
                label.getAttribute("data-base-text") ||
                label.textContent.replace(/\s*\(đã chọn:.*\)$/i, "");
            label.setAttribute("data-base-text", baseText);

            if (fileName)
                label.textContent = `${baseText} (đã chọn: ${fileName})`;
            else label.textContent = baseText;
        });
    });

    // ---------------- 6) Auto tính Thời hạn cho cả 2 modal ----------------
    // Modal Create: cần có id c_start, c_end, c_duration (đã hướng dẫn gắn trong Blade)
    const cStart = document.getElementById("c_start");
    const cEnd = document.getElementById("c_end");
    const cDur = document.getElementById("c_duration");
    wireDateDuration({ startEl: cStart, endEl: cEnd, durationEl: cDur });

    // Modal Edit: id e_start, e_end, e_duration (đã có sẵn)
    const eStartStatic = document.getElementById("e_start");
    const eEndStatic = document.getElementById("e_end");
    const eDurStatic = document.getElementById("e_duration");
    wireDateDuration({
        startEl: eStartStatic,
        endEl: eEndStatic,
        durationEl: eDurStatic,
    });
});
