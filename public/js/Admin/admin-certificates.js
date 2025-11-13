document.addEventListener('DOMContentLoaded', () => {
    initManualIssue();
    initPolicyFilters();
    initRevokeModal();
    initTemplateModals();
});

function initManualIssue() {
    const form = document.getElementById('manual-issue-form');
    if (!form) {
        return;
    }

    const typeSelect = document.getElementById('manual-issue-type');
    const studentField = buildSearchField({
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
    });

    const targetField = buildSearchField({
        input: document.getElementById('manual-target-search'),
        hidden: document.getElementById('manual-target-id'),
        meta: document.getElementById('manual-target-meta'),
        wrapper: document.querySelector('.cert-search[data-scope="target"]'),
        suggestions: form.querySelector('.cert-search__suggestions[data-scope="target"]'),
        endpoint: form.dataset.courseSource,
        formatter: (item) => ({
            label: item.label,
            meta: item.slug ? `Slug: ${item.slug}` : '',
        }),
    });

    const resetButtons = form.querySelectorAll('.cert-search__clear');
    resetButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.target;
            if (!targetId) {
                return;
            }
            const input = document.getElementById(targetId);
            const wrapper = button.closest('.cert-search');
            const hidden = wrapper?.querySelector('input[type="hidden"]');
            const meta = wrapper?.querySelector('.cert-search__meta');
            if (input) input.value = '';
            if (hidden) hidden.value = '';
            if (meta) {
                meta.textContent = wrapper?.dataset.scope === 'student' ? 'Chưa chọn học viên' : 'Chưa chọn đối tượng';
            }
            const suggestionBox = wrapper?.querySelector('.cert-search__suggestions');
            if (suggestionBox) {
                suggestionBox.classList.remove('active');
                suggestionBox.replaceChildren();
            }
        });
    });

    const updateTargetPlaceholder = () => {
        const isCourse = typeSelect?.value !== 'COMBO';
        targetField.setEndpoint(isCourse ? form.dataset.courseSource : form.dataset.comboSource);
        targetField.setPlaceholder(isCourse ? 'Nhập tên khóa học' : 'Nhập tên combo');
        const meta = document.getElementById('manual-target-meta');
        if (meta) meta.textContent = 'Chưa chọn đối tượng';
        const hidden = document.getElementById('manual-target-id');
        if (hidden) hidden.value = '';
    };

    typeSelect?.addEventListener('change', updateTargetPlaceholder);
    updateTargetPlaceholder();
}

function buildSearchField({ input, hidden, meta, suggestions, endpoint, formatter, wrapper }) {
    if (!input || !hidden || !meta || !suggestions) {
        return {
            setEndpoint() {},
            setPlaceholder() {},
        };
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
            const response = await fetch(`${currentEndpoint}?q=${encodeURIComponent(keyword)}`, {
                signal: controller.signal,
            });
            if (!response.ok) {
                throw new Error('Failed to fetch suggestions');
            }
            const payload = await response.json();
            renderSuggestions(payload.data || []);
        } catch (error) {
            if (error.name === 'AbortError') {
                return;
            }
            console.error(error);
            renderSuggestions([]);
        }
    }, 250);

    input.addEventListener('input', (event) => {
        hidden.value = '';
        meta.textContent = wrapper?.dataset.scope === 'student' ? 'Chưa chọn học viên' : 'Chưa chọn đối tượng';
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
        if (!table) {
            return;
        }
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
    if (!modal) {
        return;
    }
    modal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) {
            return;
        }
        const action = button.getAttribute('data-action');
        const code = button.getAttribute('data-code');
        const form = document.getElementById('revoke-form');
        form?.setAttribute('action', action || '#');
        const codeLabel = document.getElementById('revoke-code');
        if (codeLabel) {
            codeLabel.textContent = code || '#';
        }
        const textarea = form?.querySelector('textarea[name="reason"]');
        if (textarea) {
            textarea.value = '';
        }
    });
}

function initTemplateModals() {
    document.querySelectorAll('.template-type-select').forEach((select) => {
        updateTemplateTargets(select);
        select.addEventListener('change', () => updateTemplateTargets(select));
    });

    const editModal = document.getElementById('editTemplateModal');
    if (!editModal) {
        return;
    }

    editModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget;
        if (!button) return;

        const form = document.getElementById('edit-template-form');
        form?.setAttribute('action', button.getAttribute('data-action') || '#');

        setInputValue('edit-template-name', button.getAttribute('data-template-name'));
        setSelectValue('edit-template-type', button.getAttribute('data-template-type'));
        setSelectValue('edit-template-status', button.getAttribute('data-template-status'));
        setSelectValue('edit-template-course', button.getAttribute('data-template-course'));
        setSelectValue('edit-template-combo', button.getAttribute('data-template-combo'));
        setInputValue('edit-template-url', button.getAttribute('data-template-url'));
        setTextareaValue('edit-template-description', button.getAttribute('data-template-description'));
        setTextareaValue('edit-template-design', normaliseJson(button.getAttribute('data-template-design')));

        const typeSelect = document.getElementById('edit-template-type');
        updateTemplateTargets(typeSelect);
    });
}

function updateTemplateTargets(select) {
    if (!select) return;
    const group = select.dataset.group;
    const isCourse = select.value !== 'COMBO';
    const modal = select.closest('.modal');
    if (!modal) return;

    modal.querySelectorAll(`.template-target[data-group="${group}"]`).forEach((block) => {
        const target = block.dataset.target;
        const control = block.querySelector('select');
        if (!control) return;
        if ((isCourse && target === 'course') || (!isCourse && target === 'combo')) {
            block.classList.remove('d-none');
            control.disabled = false;
        } else {
            block.classList.add('d-none');
            control.disabled = true;
            control.value = '';
        }
    });
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
