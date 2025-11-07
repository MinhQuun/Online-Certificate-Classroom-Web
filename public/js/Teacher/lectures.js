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

    // ---------------- 1) Course Selector ----------------
    const courseSelector = document.getElementById("courseSelector");
    if (courseSelector) {
        courseSelector.addEventListener("change", () => {
            const base = courseSelector.dataset.baseUrl || window.location.pathname;
            const url = new URL(base, window.location.origin);
            if (courseSelector.value) {
                url.searchParams.set("course", courseSelector.value);
            } else {
                url.searchParams.delete("course");
            }
            window.location.href = url.toString();
        });
    }

    // ---------------- 2) Modal Create: Load course_id and chapter_id ----------------
    const modalCreate = document.getElementById("createLessonModal");
    const createCourseId = document.getElementById("createLessonCourse");
    const createChapterId = document.getElementById("createLessonChapter");
    if (modalCreate && createCourseId && createChapterId) {
        modalCreate.addEventListener("show.bs.modal", (event) => {
            const trigger = event.relatedTarget;
            const courseId = trigger?.getAttribute("data-course") || "";
            const chapterId = trigger?.getAttribute("data-chapter") || "";
            createCourseId.value = courseId;
            createChapterId.value = chapterId;

            const form = modalCreate.querySelector("form");
            if (form) {
                form.reset();
                createCourseId.value = courseId;
                createChapterId.value = chapterId;
            }
        });
    }

    // ---------------- 3) Modal Edit: Load data ----------------
    const modalEdit = document.getElementById("editLessonModal");
    if (modalEdit) {
        modalEdit.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const form = modalEdit.querySelector("#editLessonForm");
            const lessonData = JSON.parse(btn?.getAttribute("data-lesson") || "{}");
            form.action = lessonData.id ? `/teacher/lectures/${lessonData.id}` : "";

            const editCourseId = document.getElementById("editLessonCourse");
            const editChapterId = document.getElementById("editLessonChapter");
            editCourseId.value = lessonData.course || "";
            editChapterId.value = lessonData.chapter || "";

            const fields = {
                editLessonTitle: "title",
                editLessonOrder: "order",
                editLessonDescription: "description",
                editLessonType: "type",
            };

            Object.keys(fields).forEach((fid) => {
                const val = lessonData[fields[fid]] || "";
                const el = modalEdit.querySelector(`#${fid}`);
                if (el) el.value = val;
            });

            // Reset lỗi cũ
            modalEdit.querySelectorAll(".is-invalid").forEach((el) => el.classList.remove("is-invalid"));
        });
    }

    // ---------------- 4) Modal Create Material: Load lesson_id and handle AJAX submission ----------------
    const modalMaterial = document.getElementById("createMaterialModal");
    if (modalMaterial) {
        modalMaterial.addEventListener("show.bs.modal", (evt) => {
            const btn = evt.relatedTarget;
            const form = modalMaterial.querySelector("#createMaterialForm");
            const lessonId = btn?.getAttribute("data-lesson") || "";
            form.action = lessonId ? `/teacher/lectures/${lessonId}/materials` : "";

            // Reset form
            const formElements = form.querySelectorAll("input, select, textarea");
            formElements.forEach((el) => {
                el.value = "";
                el.classList.remove("is-invalid");
            });

            // Populate type select with resourcePresets keys
            const typeSelect = form.querySelector("select[name='type']");
            if (typeSelect) {
                typeSelect.innerHTML = ''; // Clear existing options
                Object.keys({
                    'video/mp4': 0,
                    'application/pdf': 0,
                    'application/vnd.ms-powerpoint': 0,
                    'application/vnd.openxmlformats-officedocument.presentationml.presentation': 0,
                    'application/zip': 0,
                    'audio/mpeg': 0,
                    'text/html': 0
                }).forEach(mime => {
                    const option = document.createElement('option');
                    option.value = mime;
                    option.textContent = mime === 'text/html' ? 'Liên kết' : mime.split('/')[1].toUpperCase();
                    typeSelect.appendChild(option);
                });
            }

            // Xử lý file input
            const fileInput = form.querySelector("input[name='file']");
            const urlInput = form.querySelector("input[name='url']");
            const sizeInput = form.querySelector("input[name='size']");

            fileInput.addEventListener("change", () => {
                const file = fileInput.files[0];
                if (file) {
                    urlInput.disabled = true;
                    sizeInput.value = formatFileSize(file.size);
                    if (typeSelect) typeSelect.value = guessFileType(file.type);
                } else {
                    urlInput.disabled = false;
                    sizeInput.value = "";
                    if (typeSelect) typeSelect.value = "";
                }
            });

            urlInput.addEventListener("input", () => {
                if (urlInput.value) {
                    fileInput.disabled = true;
                    if (typeSelect) typeSelect.value = 'text/html';
                } else {
                    fileInput.disabled = false;
                    if (typeSelect) typeSelect.value = "";
                }
            });
        });

        // AJAX submission for material form
        const materialForm = document.getElementById("createMaterialForm");
        if (materialForm) {
            materialForm.addEventListener("submit", function(e) {
                e.preventDefault();

                if (!validateLessonForm(this)) {
                    Swal.fire({
                        icon: "error",
                        title: "Vui lòng điền đầy đủ thông tin!",
                        text: "Kiểm tra các trường bắt buộc hoặc cung cấp file/URL.",
                        timer: 2200,
                        showConfirmButton: false,
                    });
                    return;
                }

                const formData = new FormData(this);
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                console.log("Form data sent:", Object.fromEntries(formData));

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken
                    }
                })
                .then(response => {
                    console.log("Response status:", response.status);
                    return response.text().then(text => {
                        console.log("Raw response text:", text);
                        if (!response.ok) {
                            if (response.status === 422) {
                                try {
                                    const errors = JSON.parse(text);
                                    let errorMessage = 'Có lỗi xảy ra: ';
                                    for (let field in errors.errors) {
                                        errorMessage += errors.errors[field][0] + ' ';
                                    }
                                    throw new Error(errorMessage);
                                } catch (e) {
                                    throw new Error("Không thể parse lỗi validation: " + text.substring(0, 100));
                                }
                            } else if (response.status === 419) {
                                throw new Error("CSRF token không hợp lệ. Vui lòng làm mới trang.");
                            }
                            throw new Error(`Lỗi server: ${response.status} - ${text.substring(0, 100)}...`);
                        }
                        return JSON.parse(text);
                    });
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false,
                        }).then(() => {
                            modalMaterial.querySelector('.btn-close').click();
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.error || 'Upload thất bại',
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối',
                        text: error.message,
                    });
                });
            });
        }
    }

    // ---------------- 5) Form validation & submission (cho các modal khác) ----------------
    function validateLessonForm(form) {
        const requiredFields = form.querySelectorAll("[required]");
        let isValid = true;

        requiredFields.forEach((field) => {
            if (!field.value || !String(field.value).trim()) {
                isValid = false;
                field.classList.add("is-invalid");
            } else {
                field.classList.remove("is-invalid");
            }
        });

        const fileInput = form.querySelector("input[name='file']");
        const urlInput = form.querySelector("input[name='url']");
        if (fileInput && urlInput && !fileInput.files[0] && !urlInput.value) {
            isValid = false;
            urlInput.classList.add("is-invalid");
        }

        return isValid;
    }

    [document.getElementById("createLessonModal"), document.getElementById("editLessonModal")]
        .forEach((modal) => {
            if (!modal) return;
            const form = modal.querySelector("form");
            if (!form) return;

            form.addEventListener("submit", function (e) {
                if (!validateLessonForm(form)) {
                    e.preventDefault();
                    if (typeof Swal !== "undefined") {
                        Swal.fire({
                            icon: "error",
                            title: "Vui lòng điền đầy đủ thông tin!",
                            text: "Kiểm tra các trường bắt buộc hoặc cung cấp file/URL.",
                            timer: 2200,
                            showConfirmButton: false,
                        });
                    }
                    return false;
                }
            });
        });

    // ---------------- 6) Delete confirmation ----------------
    const isDeleteForm = (form) => {
        const hiddenMethod = form.querySelector("input[name='_method']");
        return hiddenMethod && String(hiddenMethod.value).toLowerCase() === "delete";
    };
    document.querySelectorAll("form").forEach((form) => {
        if (!isDeleteForm(form)) return;
        form.addEventListener("submit", function (event) {
            if (form.dataset.confirmed === "true") return;
            event.preventDefault();

            const submitBtn = form.querySelector("button[type='submit'], .btn-danger, .btn-danger-soft");
            if (submitBtn?.disabled) return;

            if (typeof Swal === "undefined") {
                if (confirm("Xóa bài giảng/tài liệu này?")) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
                return;
            }
            Swal.fire({
                title: "Bạn chắc chắn?",
                text: "Thao tác này sẽ xóa và không thể hoàn tác.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Xóa",
                cancelButtonText: "Hủy",
                confirmButtonColor: "#d33",
            }).then((res) => {
                if (res.isConfirmed) {
                    form.dataset.confirmed = "true";
                    form.submit();
                }
            });
        });
    });

    // ---------------- 7) Lesson discussions ----------------
    initTeacherDiscussions();

    // ---------------- 8) Helper functions ----------------
    function formatFileSize(bytes) {
        if (bytes >= 1073741824) {
            return (bytes / 1073741824).toFixed(2) + " GB";
        } else if (bytes >= 1048576) {
            return (bytes / 1048576).toFixed(2) + " MB";
        } else if (bytes >= 1024) {
            return (bytes / 1024).toFixed(2) + " KB";
        }
        return bytes + " B";
    }

    function guessFileType(mimeType) {
        const typeMap = {
            'video/mp4': 'video/mp4',
            'application/pdf': 'application/pdf',
            'application/zip': 'application/zip',
            'application/vnd.ms-powerpoint': 'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'audio/mpeg': 'audio/mpeg',
            'text/html': 'text/html',
        };
        return typeMap[mimeType] || 'application/octet-stream';
    }
});

function initTeacherDiscussions() {
    const root = document.querySelector('[data-discussion-root]');
    if (!root) {
        return;
    }

    const triggers = document.querySelectorAll('[data-discussion-trigger]');
    if (!triggers.length) {
        return;
    }

    const panel = new TeacherDiscussionPanel(root);

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const encoded = trigger.getAttribute('data-discussion-config') || '';
            const config = decodeDiscussionConfig(encoded);
            if (!config) {
                notify('Không tìm thấy dữ liệu hỏi đáp cho bài giảng này.', 'error');
                return;
            }
            panel.open(config, trigger);
        });
    });
}

class TeacherDiscussionPanel {
    constructor(root) {
        this.root = root;
        this.panel = root.querySelector('.lesson-discussion__panel');
        this.overlay = root.querySelector('.lesson-discussion__overlay');
        this.listEl = root.querySelector('[data-discussion-list]');
        this.emptyEl = root.querySelector('[data-discussion-empty]');
        this.subtitleEl = root.querySelector('[data-discussion-subtitle]');
        this.titleEl = root.querySelector('#teacherDiscussionTitle');
        this.loadMoreBtn = root.querySelector('[data-discussion-load-more]');
        this.closeButtons = root.querySelectorAll('[data-discussion-close]');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        this.currentConfig = null;
        this.currentTrigger = null;
        this.state = {
            discussions: [],
            meta: null,
            loading: false,
            total: 0,
            lessonId: null,
        };
        this.previousOverflow = '';

        this.handleClick = this.handleClick.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
        this.onKeydown = this.onKeydown.bind(this);

        this.bindEvents();
    }

    bindEvents() {
        if (this.overlay) {
            this.overlay.addEventListener('click', () => this.close());
        }
        this.closeButtons.forEach((btn) => {
            btn.addEventListener('click', () => this.close());
        });
        if (this.loadMoreBtn) {
            this.loadMoreBtn.addEventListener('click', () => {
                if (this.state.loading) {
                    return;
                }
                const nextPage = (this.state.meta?.current_page || 1) + 1;
                this.fetchPage(nextPage, true);
            });
        }

        this.root.addEventListener('click', this.handleClick);
        this.root.addEventListener('submit', this.handleSubmit);
    }

    onKeydown(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }

    open(config, trigger) {
        if (!config || !config.fetchUrl) {
            notify('Không thể mở hỏi đáp cho bài giảng này.', 'error');
            return;
        }

        this.currentConfig = { ...config };
        this.currentTrigger = trigger || null;
        this.state = {
            discussions: [],
            meta: null,
            loading: false,
            total: Number(config.total ?? 0),
            lessonId: config.lessonId || null,
        };
        this.clearList();
        this.toggleEmpty(true);
        this.clearGlobalError();
        this.updateHeader();
        this.openPanel();
        this.fetchPage(1, false);
    }

    openPanel() {
        if (this.root.classList.contains('is-open')) {
            return;
        }
        this.previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';
        document.addEventListener('keydown', this.onKeydown);
        requestAnimationFrame(() => {
            this.root.classList.add('is-open');
        });
    }

    close() {
        if (!this.root.classList.contains('is-open')) {
            return;
        }
        this.root.classList.remove('is-open');
        document.body.style.overflow = this.previousOverflow;
        document.removeEventListener('keydown', this.onKeydown);
        this.state.loading = false;
        this.root.dataset.loading = 'false';
    }

    async fetchPage(page = 1, append = false) {
        if (!this.currentConfig?.fetchUrl) {
            return;
        }

        this.setLoading(true);
        this.clearGlobalError();

        try {
            const url = new URL(this.currentConfig.fetchUrl, window.location.origin);
            url.searchParams.set('page', page);

            const response = await fetch(url.toString(), {
                credentials: 'same-origin',
                headers: { Accept: 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Không thể tải dữ liệu thảo luận.');
            }

            const payload = await safeJson(response);
            const discussions = Array.isArray(payload?.data) ? payload.data : [];

            if (append) {
                this.state.discussions = this.state.discussions.concat(discussions);
            } else {
                this.state.discussions = discussions;
            }

            this.state.meta = payload?.meta || {};
            this.state.meta.current_page = this.state.meta.current_page || page;
            this.state.meta.has_more = Boolean(this.state.meta.has_more);
            this.state.total = typeof this.state.meta.total === 'number'
                ? this.state.meta.total
                : this.state.discussions.length;

            this.renderDiscussions();
            this.updateSummary();
            this.updatePagination();
            this.updateTriggerCount(this.state.total);
            this.currentConfig.total = this.state.total;
            this.updateTriggerConfig(this.currentConfig);
        } catch (error) {
            this.showGlobalError(error.message || 'Không thể tải dữ liệu thảo luận.');
        } finally {
            this.setLoading(false);
        }
    }

    renderDiscussions() {
        this.clearList();
        const discussions = Array.isArray(this.state.discussions) ? this.state.discussions : [];

        if (!discussions.length) {
            this.toggleEmpty(true);
            return;
        }

        this.toggleEmpty(false);
        const fragment = document.createDocumentFragment();
        discussions.forEach((discussion) => {
            fragment.appendChild(this.createDiscussionCard(discussion));
        });
        this.listEl.appendChild(fragment);
    }

    clearList() {
        if (!this.listEl) {
            return;
        }
        Array.from(this.listEl.querySelectorAll('.discussion-card, .lesson-discussion__error')).forEach((node) => {
            node.remove();
        });
    }

    updateHeader() {
        if (!this.currentConfig) {
            return;
        }
        if (this.titleEl) {
            const lessonTitle = this.currentConfig.lessonTitle || 'Bài học';
            this.titleEl.textContent = `Hỏi đáp: ${lessonTitle}`;
        }
        if (this.subtitleEl) {
            const parts = [];
            if (this.currentConfig.chapterTitle) {
                parts.push(`Chương ${this.currentConfig.chapterTitle}`);
            }
            if (this.currentConfig.lessonOrder) {
                parts.push(`Bài ${this.currentConfig.lessonOrder}`);
            }
            const total = this.currentConfig.total ?? this.state.total ?? 0;
            parts.push(`${total} thảo luận`);
            this.subtitleEl.textContent = parts.join(' • ');
        }
    }

    updateSummary() {
        if (!this.subtitleEl || !this.currentConfig) {
            return;
        }

        const parts = [];
        if (this.currentConfig.chapterTitle) {
            parts.push(`Chương ${this.currentConfig.chapterTitle}`);
        }
        if (this.currentConfig.lessonOrder) {
            parts.push(`Bài ${this.currentConfig.lessonOrder}`);
        }
        parts.push(`${this.state.total} thảo luận`);
        this.subtitleEl.textContent = parts.join(' • ');

        if (this.titleEl) {
            const lessonTitle = this.currentConfig.lessonTitle || 'Bài học';
            this.titleEl.textContent = `Hỏi đáp: ${lessonTitle}`;
        }
    }

    updatePagination() {
        if (!this.loadMoreBtn) {
            return;
        }
        const hasMore = Boolean(this.state.meta?.has_more);
        this.loadMoreBtn.hidden = !hasMore;
        this.loadMoreBtn.disabled = this.state.loading;
    }

    updateTriggerCount(total) {
        if (!this.currentConfig?.lessonId) {
            return;
        }
        const value = total ?? 0;
        const lessonId = this.currentConfig.lessonId;
        const buttons = document.querySelectorAll(`[data-discussion-lesson="${lessonId}"]`);
        buttons.forEach((btn) => {
            const badge = btn.querySelector('.discussion-count-badge');
            if (badge) {
                badge.textContent = String(value);
            }
        });
    }

    updateTriggerConfig(config) {
        if (!config?.lessonId) {
            return;
        }
        const buttons = document.querySelectorAll(`[data-discussion-lesson="${config.lessonId}"]`);
        const encoded = encodeDiscussionConfig(config);
        buttons.forEach((btn) => {
            if (encoded) {
                btn.setAttribute('data-discussion-config', encoded);
            }
        });
    }

    toggleEmpty(flag) {
        if (this.emptyEl) {
            this.emptyEl.hidden = !flag;
        }
    }

    setLoading(flag) {
        this.state.loading = flag;
        this.root.dataset.loading = flag ? 'true' : 'false';
        if (this.loadMoreBtn) {
            this.loadMoreBtn.disabled = flag;
        }
    }

    showGlobalError(message) {
        if (!this.listEl) {
            return;
        }
        let errorBlock = this.listEl.querySelector('.lesson-discussion__error');
        if (!errorBlock) {
            errorBlock = document.createElement('div');
            errorBlock.className = 'lesson-discussion__error';
            this.listEl.prepend(errorBlock);
        }
        errorBlock.textContent = message;
    }

    clearGlobalError() {
        const errorBlock = this.listEl?.querySelector('.lesson-discussion__error');
        if (errorBlock) {
            errorBlock.remove();
        }
    }

    handleClick(event) {
        const actionBtn = event.target.closest('[data-discussion-action]');
        if (actionBtn) {
            const action = actionBtn.getAttribute('data-discussion-action');
            const idAttr = actionBtn.getAttribute('data-discussion-id');
            const discussionId = idAttr ? parseInt(idAttr, 10) : NaN;

            if (!discussionId) {
                return;
            }

            if (action === 'reply-toggle') {
                this.toggleReplyForm(discussionId, null, null);
                return;
            }
            if (action === 'pin') {
                this.togglePin(discussionId, actionBtn);
                return;
            }
            if (action === 'lock') {
                this.toggleLock(discussionId, actionBtn);
                return;
            }
            if (action === 'resolve') {
                this.updateStatus(discussionId, 'RESOLVED', actionBtn);
                return;
            }
            if (action === 'reopen') {
                this.updateStatus(discussionId, 'OPEN', actionBtn);
                return;
            }
            if (action === 'delete') {
                this.deleteDiscussion(discussionId);
                return;
            }
        }

        const replyActionBtn = event.target.closest('[data-reply-action]');
        if (replyActionBtn) {
            const discussionId = parseInt(replyActionBtn.getAttribute('data-discussion-id') || '', 10);
            const replyId = parseInt(replyActionBtn.getAttribute('data-reply-id') || '', 10);
            const action = replyActionBtn.getAttribute('data-reply-action');

            if (!discussionId) {
                return;
            }

            if (action === 'delete' && replyId) {
                this.deleteReply(discussionId, replyId, replyActionBtn);
                return;
            }

            if (action === 'reply' && replyId) {
                const replyItem = replyActionBtn.closest('.discussion-reply');
                this.toggleReplyForm(discussionId, replyId, replyItem);
            }
        }
    }

    handleSubmit(event) {
        const form = event.target.closest('.reply-form');
        if (!form) {
            return;
        }
        event.preventDefault();
        const discussionId = parseInt(form.getAttribute('data-discussion-id') || '', 10);
        if (!discussionId) {
            return;
        }
        this.submitReply(discussionId, form);
    }

    toggleReplyForm(discussionId, parentReplyId = null, replyElement = null) {
        const card = this.listEl?.querySelector(`[data-discussion-id="${discussionId}"]`);
        if (!card) {
            return;
        }

        const form = card.querySelector('.reply-form');
        if (!form) {
            return;
        }

        const targetParent = parentReplyId ? String(parentReplyId) : '';
        const currentParent = form.dataset.parentId || '';
        const isVisible = form.classList.contains('is-visible') && targetParent === currentParent;

        this.listEl?.querySelectorAll('.reply-form').forEach((node) => {
            if (node !== form) {
                this.resetReplyForm(node);
            }
        });

        if (isVisible) {
            this.resetReplyForm(form);
            return;
        }

        let container = card.querySelector('.discussion-replies');
        let replyTarget = replyElement || null;

        if (parentReplyId && !replyTarget) {
            replyTarget = card.querySelector(`[data-reply-id="${parentReplyId}"]`);
        }

        if (parentReplyId) {
            const nested = this.ensureReplyChildContainer(replyTarget);
            container = nested || container;
        }

        if (!container) {
            return;
        }

        container.appendChild(form);
        form.dataset.parentId = targetParent;
        this.updateReplyFormContext(form, replyTarget);

        form.classList.add('is-visible');
        const textarea = form.querySelector('textarea');
        if (textarea) {
            textarea.focus();
        }
    }

    ensureReplyChildContainer(replyNode) {
        if (!replyNode) {
            return null;
        }
        let container = replyNode.querySelector('.discussion-reply__children');
        if (!container) {
            container = document.createElement('div');
            container.className = 'discussion-reply__children';
            replyNode.appendChild(container);
        }
        return container;
    }

    resetReplyForm(form) {
        if (!form) {
            return;
        }
        form.classList.remove('is-visible');
        form.dataset.parentId = '';

        const textarea = form.querySelector('textarea');
        if (textarea) {
            textarea.value = '';
            textarea.disabled = false;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = false;
        }

        this.updateReplyFormContext(form, null);

        const card = form.closest('.discussion-card');
        const container = card?.querySelector('.discussion-replies');
        if (container && form.parentElement !== container) {
            container.appendChild(form);
        }

        this.showInlineError(form, '');
    }

    updateReplyFormContext(form, replyNode) {
        const context = form.querySelector('[data-reply-context]');
        if (!context) {
            return;
        }

        if (replyNode) {
            const authorName = replyNode.getAttribute('data-reply-author') || 'một phản hồi';
            context.hidden = false;
            context.textContent = 'Đang trả lời ' + authorName;
        } else {
            context.hidden = true;
            context.textContent = '';
        }
    }

    async submitReply(discussionId, form) {
        if (!this.currentConfig?.replyUrlTemplate) {
            this.showInlineError(form, 'Bạn không có quyền trả lời câu hỏi này.');
            return;
        }

        const textarea = form.querySelector('textarea');
        if (!textarea) {
            return;
        }

        const content = textarea.value.trim();
        if (content.length < 3) {
            this.showInlineError(form, 'Vui lòng nhập tối thiểu 3 ký tự.');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        textarea.disabled = true;
        this.showInlineError(form, '');

        const url = this.currentConfig.replyUrlTemplate.replace('__DISCUSSION__', String(discussionId));
        const payload = { noi_dung: content };
        const parentId = form.dataset.parentId;
        if (parentId) {
            payload.parent_reply_id = parseInt(parentId, 10);
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const payloadBody = await safeJson(response);
                const message = payloadBody?.message || 'Không thể gửi phản hồi.';
                this.showInlineError(form, message);
                return;
            }

            this.resetReplyForm(form);
            notify('Đã gửi phản hồi.', 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            this.showInlineError(form, 'Không thể gửi phản hồi. Vui lòng thử lại.');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            textarea.disabled = false;
        }
    }

    showInlineError(form, message) {
        let errorBlock = form.querySelector('.lesson-discussion__error');
        if (!message) {
            if (errorBlock) {
                errorBlock.remove();
            }
            return;
        }
        if (!errorBlock) {
            errorBlock = document.createElement('div');
            errorBlock.className = 'lesson-discussion__error';
            form.prepend(errorBlock);
        }
        errorBlock.textContent = message;
    }

    async togglePin(discussionId, trigger) {
        if (!this.currentConfig?.moderation?.pinUrlTemplate) {
            return;
        }
        const url = this.currentConfig.moderation.pinUrlTemplate.replace('__DISCUSSION__', String(discussionId));
        try {
            const payload = await mutateDiscussion(url, 'PATCH', null, this.csrfToken);
            const message = payload?.message || 'Đã cập nhật trạng thái ghim.';
            notify(message, 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            notify(error.message || 'Không thể cập nhật trạng thái ghim.', 'error');
        } finally {
            if (trigger) {
                trigger.disabled = false;
            }
        }
    }

    async toggleLock(discussionId, trigger) {
        if (!this.currentConfig?.moderation?.lockUrlTemplate) {
            return;
        }
        const url = this.currentConfig.moderation.lockUrlTemplate.replace('__DISCUSSION__', String(discussionId));
        try {
            const payload = await mutateDiscussion(url, 'PATCH', null, this.csrfToken);
            const message = payload?.message || 'Đã cập nhật trạng thái khóa.';
            notify(message, 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            notify(error.message || 'Không thể cập nhật trạng thái khóa.', 'error');
        } finally {
            if (trigger) {
                trigger.disabled = false;
            }
        }
    }

    async updateStatus(discussionId, status, trigger) {
        if (!this.currentConfig?.moderation?.statusUrlTemplate) {
            return;
        }
        const url = this.currentConfig.moderation.statusUrlTemplate.replace('__DISCUSSION__', String(discussionId));
        try {
            const payload = await mutateDiscussion(url, 'PATCH', { status }, this.csrfToken);
            const message = payload?.message || 'Đã cập nhật trạng thái.';
            notify(message, 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            notify(error.message || 'Không thể cập nhật trạng thái.', 'error');
        } finally {
            if (trigger) {
                trigger.disabled = false;
            }
        }
    }

    async deleteDiscussion(discussionId) {
        if (!this.currentConfig?.deleteUrlTemplate) {
            return;
        }
        if (!window.confirm('Bạn có chắc chắn muốn xóa thảo luận này?')) {
            return;
        }
        const url = this.currentConfig.deleteUrlTemplate.replace('__DISCUSSION__', String(discussionId));
        try {
            await mutateDiscussion(url, 'DELETE', null, this.csrfToken);
            notify('Đã xóa thảo luận.', 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            notify(error.message || 'Không thể xóa thảo luận.', 'error');
        }
    }

    async deleteReply(discussionId, replyId, trigger) {
        if (!this.currentConfig?.deleteReplyUrlTemplate) {
            return;
        }
        if (!window.confirm('Bạn có chắc chắn muốn xóa phản hồi này?')) {
            return;
        }
        const url = this.currentConfig.deleteReplyUrlTemplate
            .replace('__DISCUSSION__', String(discussionId))
            .replace('__REPLY__', String(replyId));
        try {
            await mutateDiscussion(url, 'DELETE', null, this.csrfToken);
            notify('Đã xóa phản hồi.', 'success');
            await this.fetchPage(1, false);
        } catch (error) {
            notify(error.message || 'Không thể xóa phản hồi.', 'error');
        } finally {
            if (trigger) {
                trigger.disabled = false;
            }
        }
    }

    createDiscussionCard(discussion) {
        const card = document.createElement('article');
        card.className = 'discussion-card';
        card.dataset.discussionId = String(discussion.id);

        if (discussion.is_pinned) {
            card.classList.add('is-pinned');
        }

        const header = document.createElement('div');
        header.className = 'discussion-card__header';

        header.appendChild(this.createUserMeta(discussion.author, discussion.created_human));

        const labels = document.createElement('div');
        labels.className = 'discussion-card__labels';
        if (discussion.is_pinned) {
            labels.appendChild(createLabel('Đã ghim', 'label--pinned', 'bi-pin-angle'));
        }
        if (discussion.is_locked) {
            labels.appendChild(createLabel('Đang khóa', 'label--locked', 'bi-shield-lock'));
        }
        if (discussion.status === 'RESOLVED') {
            labels.appendChild(createLabel('Đã giải quyết', 'label--resolved', 'bi-check-circle'));
        }
        header.appendChild(labels);
        card.appendChild(header);

        const content = document.createElement('div');
        content.className = 'discussion-card__content';
        content.textContent = discussion.content || '';
        card.appendChild(content);

        card.appendChild(this.createActionsRow(discussion));

        const repliesSection = this.createRepliesSection(discussion);
        if (repliesSection) {
            card.appendChild(repliesSection);
        }

        return card;
    }

    createUserMeta(author = {}, timestamp = '') {
        const wrapper = document.createElement('div');
        wrapper.className = 'discussion-card__user';

        const avatar = document.createElement('div');
        avatar.className = 'discussion-card__avatar';
        avatar.textContent = author.initials || initialsFromName(author.name) || 'HV';

        const meta = document.createElement('div');
        meta.className = 'discussion-card__meta';

        const nameRow = document.createElement('div');
        nameRow.className = 'discussion-card__name';

        const name = document.createElement('span');
        name.textContent = author.name || 'Người dùng';

        nameRow.appendChild(name);
        const badge = createRoleBadge(author.role);
        if (badge) {
            nameRow.appendChild(badge);
        }
        meta.appendChild(nameRow);

        if (timestamp) {
            const time = document.createElement('div');
            time.className = 'discussion-card__timestamp';
            time.textContent = timestamp;
            meta.appendChild(time);
        }

        wrapper.appendChild(avatar);
        wrapper.appendChild(meta);
        return wrapper;
    }

    createActionsRow(discussion) {
        const container = document.createElement('div');
        container.className = 'discussion-card__actions';

        const left = document.createElement('div');
        left.className = 'discussion-card__actions';

        if (this.currentConfig?.permissions?.can_reply && !discussion.is_locked) {
            const replyBtn = document.createElement('button');
            replyBtn.type = 'button';
            replyBtn.className = 'btn btn-link p-0 text-decoration-none';
            replyBtn.dataset.discussionAction = 'reply-toggle';
            replyBtn.dataset.discussionId = String(discussion.id);
            replyBtn.innerHTML = "<i class='bi bi-reply'></i> <span>Trả lời</span>";
            left.appendChild(replyBtn);
        }

        const repliesMeta = document.createElement('span');
        repliesMeta.className = 'discussion-card__actions-aux';
        const replyCount = typeof discussion.reply_count === 'number'
            ? discussion.reply_count
            : (Array.isArray(discussion.replies) ? discussion.replies.length : 0);
        repliesMeta.innerHTML = "<i class='bi bi-chat-dots'></i> <span>" + replyCount + " phản hồi</span>";
        left.appendChild(repliesMeta);

        container.appendChild(left);

        const right = document.createElement('div');
        right.className = 'discussion-card__moderation';

        if (this.currentConfig?.permissions?.can_moderate) {
            right.appendChild(this.createModerationButton(
                discussion.is_pinned ? 'Bỏ ghim' : 'Ghim',
                'pin',
                discussion.id,
                discussion.is_pinned
            ));

            right.appendChild(this.createModerationButton(
                discussion.is_locked ? 'Mở khóa' : 'Khóa',
                'lock',
                discussion.id,
                discussion.is_locked
            ));

            right.appendChild(this.createModerationButton(
                discussion.status === 'RESOLVED' ? 'Mở lại' : 'Đã giải quyết',
                discussion.status === 'RESOLVED' ? 'reopen' : 'resolve',
                discussion.id,
                discussion.status === 'RESOLVED'
            ));
        }

        if (discussion.can_delete) {
            right.appendChild(this.createModerationButton(
                'Xóa',
                'delete',
                discussion.id,
                false
            ));
        }

        container.appendChild(right);
        return container;
    }

    createModerationButton(label, action, discussionId, active) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.dataset.discussionAction = action;
        btn.dataset.discussionId = String(discussionId);
        btn.dataset.state = active ? 'active' : 'inactive';
        btn.textContent = label;
        return btn;
    }

    createRepliesSection(discussion) {
        const wrapper = document.createElement('div');
        wrapper.className = 'discussion-replies';

        const tree = buildReplyTree(discussion.replies || []);
        tree.forEach((node) => {
            wrapper.appendChild(this.createReplyItem(node, discussion));
        });

        if (this.currentConfig?.permissions?.can_reply && !discussion.is_locked) {
            const form = this.createReplyForm(discussion);
            if (form) {
                wrapper.appendChild(form);
            }
        }

        return wrapper.childElementCount ? wrapper : null;
    }

    createReplyItem(node, discussion) {
        const reply = node || {};
        const item = document.createElement('div');
        item.className = 'discussion-reply';
        item.dataset.replyId = String(reply.id);
        item.dataset.replyAuthor = reply.author?.name || '';

        const header = document.createElement('div');
        header.className = 'discussion-reply__header';

        const meta = document.createElement('div');
        meta.className = 'discussion-reply__meta';

        const avatar = document.createElement('div');
        avatar.className = 'discussion-reply__avatar';
        avatar.textContent = reply.author?.initials || initialsFromName(reply.author?.name) || 'HV';
        meta.appendChild(avatar);

        const body = document.createElement('div');
        body.className = 'discussion-reply__body';

        const nameRow = document.createElement('div');
        nameRow.className = 'discussion-reply__name';

        const name = document.createElement('span');
        name.textContent = reply.author?.name || 'Người dùng';
        nameRow.appendChild(name);

        const roleBadge = createRoleBadge(reply.author?.role, true);
        if (roleBadge) {
            nameRow.appendChild(roleBadge);
        }
        body.appendChild(nameRow);

        if (reply.created_human) {
            const time = document.createElement('div');
            time.className = 'discussion-reply__timestamp';
            time.textContent = reply.created_human;
            body.appendChild(time);
        }

        meta.appendChild(body);
        header.appendChild(meta);

        const actions = document.createElement('div');
        actions.className = 'discussion-reply__actions';

        if (this.currentConfig?.permissions?.can_reply && !discussion.is_locked) {
            const replyBtn = document.createElement('button');
            replyBtn.type = 'button';
            replyBtn.dataset.replyAction = 'reply';
            replyBtn.setAttribute('data-discussion-id', String(discussion.id));
            replyBtn.setAttribute('data-reply-id', String(reply.id));
            replyBtn.textContent = 'Trả lời';
            actions.appendChild(replyBtn);
        }

        if (reply.can_delete) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.dataset.replyAction = 'delete';
            deleteBtn.setAttribute('data-discussion-id', String(discussion.id));
            deleteBtn.setAttribute('data-reply-id', String(reply.id));
            deleteBtn.textContent = 'Xóa';
            actions.appendChild(deleteBtn);
        }

        if (actions.childElementCount) {
            header.appendChild(actions);
        }

        item.appendChild(header);

        const content = document.createElement('div');
        content.className = 'discussion-reply__content';
        content.textContent = reply.content || '';
        item.appendChild(content);

        const children = Array.isArray(node.children) ? node.children : [];
        if (children.length) {
            const childrenContainer = document.createElement('div');
            childrenContainer.className = 'discussion-reply__children';
            children.forEach((child) => {
                childrenContainer.appendChild(this.createReplyItem(child, discussion));
            });
            item.appendChild(childrenContainer);
        }

        return item;
    }

    createReplyForm(discussion) {
        if (!this.currentConfig?.permissions?.can_reply || discussion.is_locked) {
            return null;
        }

        const form = document.createElement('form');
        form.className = 'reply-form';
        form.dataset.discussionId = String(discussion.id);
        form.dataset.parentId = '';

        const context = document.createElement('div');
        context.className = 'reply-form__context';
        context.setAttribute('data-reply-context', '');
        context.hidden = true;
        form.appendChild(context);

        const textarea = document.createElement('textarea');
        textarea.setAttribute('rows', '3');
        textarea.placeholder = 'Nhập nội dung phản hồi...';
        form.appendChild(textarea);

        const actions = document.createElement('div');
        actions.className = 'reply-form__actions';

        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.textContent = 'Gửi phản hồi';
        actions.appendChild(submitBtn);

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.textContent = 'Hủy';
        cancelBtn.addEventListener('click', () => this.resetReplyForm(form));
        actions.appendChild(cancelBtn);

        form.appendChild(actions);
        return form;
    }
}

async function mutateDiscussion(url, method, body, csrfToken) {
    const options = {
        method,
        headers: {
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
    };

    if (body) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    const response = await fetch(url, options);
    if (!response.ok) {
        const payload = await safeJson(response);
        const message = payload?.message || `Không thể thực hiện hành động (${response.status}).`;
        const error = new Error(message);
        error.payload = payload;
        throw error;
    }

    return safeJson(response);
}

function buildReplyTree(flatReplies) {
    if (!Array.isArray(flatReplies) || !flatReplies.length) {
        return [];
    }

    const map = new Map();
    flatReplies.forEach((reply) => {
        const node = Object.assign({}, reply);
        node.children = [];
        map.set(reply.id, node);
    });

    const roots = [];
    map.forEach((node) => {
        if (node.parent_reply_id && map.has(node.parent_reply_id)) {
            const parent = map.get(node.parent_reply_id);
            parent.children.push(node);
        } else {
            roots.push(node);
        }
    });

    return roots;
}

function createLabel(text, modifier, icon) {
    const span = document.createElement('span');
    span.className = `label ${modifier}`;
    span.innerHTML = `<i class="${icon}"></i> <span>${text}</span>`;
    return span;
}

function createRoleBadge(role, isReply = false) {
    if (!role) {
        return null;
    }
    const span = document.createElement('span');
    const normalized = role.toUpperCase();
    const baseClass = isReply ? 'discussion-reply__badge' : 'role-badge';
    let label = 'Thành viên';
    let modifier = '';

    if (normalized === 'GIANG_VIEN') {
        label = 'Giảng viên';
        modifier = isReply ? '' : ' role-badge--teacher';
    } else if (normalized === 'ADMIN') {
        label = 'Quản trị';
        modifier = isReply ? '' : ' role-badge--admin';
    } else if (normalized === 'HOC_VIEN') {
        label = 'Học viên';
    } else {
        label = normalized;
    }

    span.className = `${baseClass}${modifier}`;
    span.textContent = label;
    return span;
}

function initialsFromName(name) {
    if (!name) {
        return null;
    }
    const parts = String(name)
        .trim()
        .split(/\s+/u)
        .filter(Boolean);
    if (!parts.length) {
        return null;
    }
    const initials = parts
        .slice(-2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
    return initials || null;
}

function decodeDiscussionConfig(encoded) {
    if (!encoded) {
        return null;
    }
    try {
        const binary = atob(encoded);
        if (window.TextDecoder) {
            const bytes = Uint8Array.from(binary, (char) => char.charCodeAt(0));
            const json = new TextDecoder().decode(bytes);
            return JSON.parse(json);
        }
        return JSON.parse(decodeURIComponent(escape(binary)));
    } catch (error) {
        try {
            return JSON.parse(atob(encoded));
        } catch (inner) {
            console.error('Failed to decode discussion config', inner);
            return null;
        }
    }
}

function encodeDiscussionConfig(config) {
    try {
        const json = JSON.stringify(config);
        if (window.TextEncoder) {
            const bytes = new TextEncoder().encode(json);
            let binary = '';
            bytes.forEach((byte) => {
                binary += String.fromCharCode(byte);
            });
            return btoa(binary);
        }
        return btoa(unescape(encodeURIComponent(json)));
    } catch (error) {
        console.error('Failed to encode discussion config', error);
        return '';
    }
}

async function safeJson(response) {
    try {
        return await response.json();
    } catch (error) {
        return null;
    }
}

function notify(message, type = 'success') {
    if (!message) {
        return;
    }
    if (window.Swal && typeof window.Swal.fire === 'function') {
        window.Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            timer: 2200,
            showConfirmButton: false,
        });
    } else {
        if (type === 'error') {
            console.error(message);
        } else {
            console.log(message);
        }
    }
}