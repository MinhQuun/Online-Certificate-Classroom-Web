document.addEventListener('DOMContentLoaded', () => {
    initManualIssue();
    initPolicyFilters();
    initRevokeModal();
    initTemplateModals();
});

function initManualIssue() {
    const form = document.getElementById('manual-issue-form');
    if (!form) return;

    const templateLabel = document.getElementById('manual-template-label');

    const courseField = buildSearchField({
        input: document.getElementById('manual-course-search'),
        hidden: document.getElementById('manual-course-id'),
        meta: document.getElementById('manual-course-meta'),
        wrapper: document.querySelector('.cert-search[data-scope="course"]'),
        suggestions: form.querySelector('.cert-search__suggestions[data-scope="course"]'),
        endpoint: form.dataset.courseSource,
        formatter: (item) => ({
            label: item.label,
            meta: item.slug ? `Slug: ${item.slug}` : '',
            templateName: item.template?.name ?? '',
        }),
        onSelect: (display) => {
            if (templateLabel) {
                templateLabel.textContent = display.templateName || '---';
            }
            const studentInput = document.getElementById('manual-student-search');
            const studentHidden = document.getElementById('manual-student-id');
            const studentMeta = document.getElementById('manual-student-meta');
            if (studentInput) studentInput.value = '';
            if (studentHidden) studentHidden.value = '';
            if (studentMeta) studentMeta.textContent = 'Chưa chọn học viên';
        },
    });
    courseField.setPlaceholder('Nhập tên khóa học');

    buildSearchField({
        input: document.getElementById('manual-student-search'),
        hidden: document.getElementById('manual-student-id'),
        meta: document.getElementById('manual-student-meta'),
        wrapper: document.querySelector('.cert-search[data-scope="student"]'),
        suggestions: form.querySelector('.cert-search__suggestions[data-scope="student"]'),
        endpoint: form.dataset.studentSource,
        formatter: (item) => ({
            label: item.label,
            meta: item.email || 'Không có email',
        }),
        getParams: () => {
            const courseId = document.getElementById('manual-course-id')?.value;
            return courseId ? { course_id: courseId } : {};
        },
    });

    form.querySelectorAll('.cert-search__clear').forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.target;
            const input = targetId ? document.getElementById(targetId) : null;
            const wrapper = button.closest('.cert-search');
            const hidden = wrapper?.querySelector('input[type="hidden"]');
            const meta = wrapper?.querySelector('.cert-search__meta');

            if (input) input.value = '';
            if (hidden) hidden.value = '';
            if (meta) {
                meta.textContent = wrapper?.dataset.scope === 'student' ? 'Chưa chọn học viên' : 'Chưa chọn khóa học';
            }

            const suggestionBox = wrapper?.querySelector('.cert-search__suggestions');
            if (suggestionBox) {
                suggestionBox.classList.remove('active');
                suggestionBox.replaceChildren();
            }

            if (wrapper?.dataset.scope === 'course' && templateLabel) {
                templateLabel.textContent = '---';
            }
        });
    });
}

function buildSearchField({ input, hidden, meta, suggestions, endpoint, formatter, wrapper, onSelect, getParams }) {
    if (!input || !hidden || !meta || !suggestions) {
        return { setEndpoint() {}, setPlaceholder() {} };
    }

    let currentEndpoint = endpoint || '';
    let controller;

    const renderSuggestions = (items) => {
        suggestions.replaceChildren();
        if (!items.length) {
            suggestions.classList.remove('active');
            return;
        }

        items.forEach((item) => {
            const button = document.createElement('button');
            button.type = 'button';
            const display = formatter ? formatter(item) : { label: item.label || item.name, meta: '' };

            const title = document.createElement('strong');
            title.textContent = display.label;
            const metaLine = document.createElement('div');
            metaLine.className = 'text-muted small';
            metaLine.textContent = display.meta ?? '';

            button.appendChild(title);
            button.appendChild(metaLine);

            button.addEventListener('click', () => {
                input.value = display.label;
                hidden.value = item.id;
                meta.textContent = display.meta || display.label;
                suggestions.classList.remove('active');
                suggestions.replaceChildren();
                if (typeof onSelect === 'function') {
                    onSelect(display);
                }
            });
            suggestions.appendChild(button);
        });
        suggestions.classList.add('active');
    };

    const search = debounce(async (keyword) => {
        if (!currentEndpoint || keyword.length < 2) {
            renderSuggestions([]);
            return;
        }

        controller?.abort();
        controller = new AbortController();

        try {
            const params = new URLSearchParams({ q: keyword });
            if (typeof getParams === 'function') {
                Object.entries(getParams() || {}).forEach(([key, value]) => {
                    if (value !== undefined && value !== null && `${value}` !== '') {
                        params.set(key, value);
                    }
                });
            }

            const response = await fetch(`${currentEndpoint}?${params.toString()}`, { signal: controller.signal });
            if (!response.ok) throw new Error('Failed to fetch suggestions');
            const payload = await response.json();
            renderSuggestions(payload.data || []);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error(error);
            }
            renderSuggestions([]);
        }
    }, 250);

    input.addEventListener('input', (event) => {
        hidden.value = '';
        meta.textContent = wrapper?.dataset.scope === 'student' ? 'Chưa chọn học viên' : 'Chưa chọn khóa học';
        search(event.target.value.trim());
    });

    document.addEventListener('click', (event) => {
        if (!wrapper?.contains(event.target)) {
            suggestions.classList.remove('active');
        }
    });

    return {
        setEndpoint(url) {
            currentEndpoint = url || '';
        },
        setPlaceholder(text) {
            input.placeholder = text;
        },
    };
}

function initPolicyFilters() {
    document.querySelectorAll('.cert-policy-search').forEach((input) => {
        const table = document.querySelector(input.dataset.policyTarget);
        if (!table) return;
        const rows = table.querySelectorAll('[data-policy-row]');
        input.addEventListener('input', () => {
            const keyword = input.value.trim().toLowerCase();
            rows.forEach((row) => {
                const haystack = row.dataset.name || '';
                const shouldHide = keyword !== '' && !haystack.includes(keyword);
                row.style.display = shouldHide ? 'none' : '';
            });
        });
    });
}

function initRevokeModal() {
    const modal = document.getElementById('revokeModal');
    if (!modal) return;
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) return;
        const action = button.getAttribute('data-action');
        const code = button.getAttribute('data-code');
        const form = document.getElementById('revoke-form');
        form?.setAttribute('action', action || '#');
        const codeLabel = document.getElementById('revoke-code');
        if (codeLabel) codeLabel.textContent = code || '#';
        const textarea = form?.querySelector('textarea[name="reason"]');
        if (textarea) textarea.value = '';
    });
}

function initTemplateModals() {
    const presets = getTemplatePresets();

    const createModal = document.getElementById('createTemplateModal');
    if (createModal) {
        const form = document.getElementById('create-template-form');
        const presetButtons = createModal.querySelectorAll('[data-template-preset]');
        presetButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const key = button.getAttribute('data-template-preset') || '';
                const preset = presets.find((item) => item.key === key);
                if (!preset || !form) return;
                applyPresetToTemplateForm(form, preset);
            });
        });
    }

    const editModal = document.getElementById('editTemplateModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;

            const form = document.getElementById('edit-template-form');
            form?.setAttribute('action', button.getAttribute('data-action') || '#');

            setInputValue('edit-template-name', button.getAttribute('data-template-name'));
            setSelectValue('edit-template-status', button.getAttribute('data-template-status'));
            setSelectValue('edit-template-course', button.getAttribute('data-template-course'));
            setInputValue('edit-template-url', button.getAttribute('data-template-url'));
            setTextareaValue('edit-template-description', button.getAttribute('data-template-description'));
            setTextareaValue('edit-template-design', normaliseJson(button.getAttribute('data-template-design')));
        });
    }
}

function applyPresetToTemplateForm(form, preset) {
    setInputValue('create-template-name', preset.name || '');
    setSelectValue('create-template-status', preset.status || 'ACTIVE');
    setSelectValue('create-template-course', preset.maKH || '');
    setInputValue('create-template-url', preset.template_url || '');
    setTextareaValue('create-template-description', preset.description || '');

    const design = preset.design ? JSON.stringify(preset.design, null, 2) : '';
    setTextareaValue('create-template-design', design);

    const nameInput = form.querySelector('#create-template-name');
    nameInput?.focus();
}

function setInputValue(id, value) {
    const input = document.getElementById(id);
    if (input) {
        input.value = value ?? '';
    }
}

function setSelectValue(id, value) {
    const select = document.getElementById(id);
    if (select) {
        select.value = value ?? '';
    }
}

function setTextareaValue(id, value) {
    const textarea = document.getElementById(id);
    if (textarea) {
        textarea.value = value ?? '';
    }
}

function getTemplatePresets() {
    const holder = document.getElementById('template-presets-data');
    if (!holder) return [];
    try {
        return JSON.parse(holder.dataset.presets || '[]') || [];
    } catch (_) {
        return [];
    }
}

function normaliseJson(value) {
    if (!value || value === 'null') {
        return '';
    }
    try {
        return JSON.stringify(JSON.parse(value), null, 2);
    } catch (_) {
        return value;
    }
}

function debounce(fn, delay = 200) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn.apply(null, args), delay);
    };
}
