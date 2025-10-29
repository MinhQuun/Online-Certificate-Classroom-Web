document.addEventListener('DOMContentLoaded', () => {
    const table = document.querySelector('.invoice-table');
    const modalEl = document.getElementById('invoiceDetailModal');
    if (!table || !modalEl) {
        return;
    }

    const config = window.invoiceDetailConfig || {};
    const detailUrlTemplate = table.dataset.detailUrl || '';
    const pdfUrlTemplate = table.dataset.pdfUrl || '';

    const modal = new bootstrap.Modal(modalEl);
    const loader = modalEl.querySelector('[data-modal-loader]');
    const content = modalEl.querySelector('[data-modal-content]');
    const errorBox = modalEl.querySelector('[data-modal-error]');
    const pdfButton = modalEl.querySelector('[data-pdf-button]');

    const nodeRefs = {
        number: modalEl.querySelector('[data-invoice-number]'),
        numberSummary: modalEl.querySelector('[data-invoice-number-summary]'),
        issuedFull: modalEl.querySelector('[data-invoice-issued-full]'),
        issuedDate: modalEl.querySelector('[data-issued-date]'),
        issuedTime: modalEl.querySelector('[data-issued-time]'),
        paymentMethod: modalEl.querySelector('[data-payment-method]'),
        totalAmount: modalEl.querySelector('[data-total-amount]'),
        processor: modalEl.querySelector('[data-processor]'),
        itemCount: modalEl.querySelector('[data-item-count]'),
        note: modalEl.querySelector('[data-invoice-note]'),
        studentName: modalEl.querySelector('[data-student-name]'),
        studentEmail: modalEl.querySelector('[data-student-email]'),
        studentPhone: modalEl.querySelector('[data-student-phone]'),
        studentId: modalEl.querySelector('[data-student-id]'),
        itemsBody: modalEl.querySelector('[data-items-body]'),
        itemsTotal: modalEl.querySelector('[data-items-total]'),
        relatedList: modalEl.querySelector('[data-related-list]'),
        relatedEmpty: modalEl.querySelector('[data-related-empty]'),
    };

    table.addEventListener('click', (event) => {
        const buttonTrigger = event.target.closest('.js-invoice-detail');
        if (buttonTrigger) {
            const invoiceId = buttonTrigger.dataset.invoiceId;
            if (invoiceId) {
                openDetail(invoiceId);
            }
            return;
        }

        if (event.target.closest('button, a')) {
            return;
        }

        const rowTrigger = event.target.closest('tr.invoice-row');
        if (!rowTrigger) {
            return;
        }

        const invoiceId = rowTrigger.dataset.invoiceId;
        if (invoiceId) {
            openDetail(invoiceId);
        }
    });

    async function openDetail(invoiceId) {
        showLoader();
        modal.show();

        try {
            const url = buildUrl(detailUrlTemplate, invoiceId);
            if (!url) {
                throw new Error('Missing detail URL template.');
            }

            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            const data = await response.json();
            renderInvoice(data);
        } catch (error) {
            showError(error?.message || config.notFound || 'Khong the tai du lieu hoa don.');
        }
    }

    function showLoader() {
        loader?.classList.remove('d-none');
        content?.classList.add('d-none');
        errorBox?.classList.add('d-none');
    }

    function showError(message) {
        loader?.classList.add('d-none');
        content?.classList.add('d-none');
        if (errorBox) {
            errorBox.textContent = message;
            errorBox.classList.remove('d-none');
        }
    }

    function renderInvoice(payload) {
        loader?.classList.add('d-none');
        errorBox?.classList.add('d-none');
        content?.classList.remove('d-none');

        const invoice = payload.invoice || {};
        const student = payload.student || {};
        const related = Array.isArray(payload.related_invoices) ? payload.related_invoices : [];

        setText(nodeRefs.number, ` ${invoice.id ?? '---'}`);
        setText(nodeRefs.numberSummary, invoice.id ?? '---');
        setText(nodeRefs.totalAmount, invoice.total_amount_text ?? '---');
        setText(nodeRefs.paymentMethod, invoice.payment_method ?? '---');
        setText(nodeRefs.processor, invoice.processor ?? '---');
        setText(nodeRefs.itemCount, invoice.items_total_quantity ?? 0);
        setText(nodeRefs.note, invoice.note ?? 'Khong co');

        const issued = invoice.issued_at || null;
        setText(nodeRefs.issuedFull, issued?.full ?? '');
        setText(nodeRefs.issuedDate, issued?.date ?? 'N/A');
        setText(nodeRefs.issuedTime, issued?.time ?? '');

        setText(nodeRefs.studentName, student.name ?? '---');
        setText(nodeRefs.studentEmail, student.email ?? '---');
        setText(nodeRefs.studentPhone, student.phone ?? '---');
        setText(nodeRefs.studentId, student.student_id ?? '---');

        renderItems(invoice);
        renderRelated(related);

        if (pdfButton) {
            const pdfUrl = payload.pdf_url ?? buildUrl(pdfUrlTemplate, invoice.id);
            pdfButton.href = pdfUrl || '#';
            pdfButton.classList.toggle('disabled', !pdfUrl);
        }
    }

    function renderItems(invoice) {
        const items = Array.isArray(invoice.items) ? invoice.items : [];
        const tbody = nodeRefs.itemsBody;
        if (tbody) {
            tbody.innerHTML = '';

            if (items.length === 0) {
                const emptyRow = document.createElement('tr');
                const td = document.createElement('td');
                td.colSpan = 5;
                td.className = 'text-center text-muted py-3';
                td.textContent = 'Khong co khoa hoc trong hoa don.';
                emptyRow.appendChild(td);
                tbody.appendChild(emptyRow);
            } else {
                items.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td>
                            <div class="fw-semibold">${escapeHtml(item.course_name ?? 'Khoa hoc')}</div>
                            <div class="text-muted small">Ma KH: ${escapeHtml(String(item.course_id ?? 'N/A'))}</div>
                        </td>
                        <td class="text-center">${item.quantity ?? 0}</td>
                        <td class="text-end">${escapeHtml(item.unit_price_text ?? '0 VND')}</td>
                        <td class="text-end">${escapeHtml(item.line_total_text ?? '0 VND')}</td>
                    `;
                    tbody.appendChild(tr);
                });
            }
        }

        setText(nodeRefs.itemsTotal, invoice.items_total_text ?? '0 VND');
    }

    function renderRelated(related) {
        const list = nodeRefs.relatedList;
        const empty = nodeRefs.relatedEmpty;
        if (!list) {
            return;
        }

        list.innerHTML = '';

        if (!related.length) {
            if (empty) {
                empty.classList.remove('d-none');
            }
            return;
        }

        if (empty) {
            empty.classList.add('d-none');
        }

        related.forEach((item) => {
            const wrapper = document.createElement('div');
            wrapper.className = 'list-group-item';
            wrapper.innerHTML = `
                <div>
                    <div class="fw-semibold">#${escapeHtml(String(item.id ?? '---'))} - ${escapeHtml(item.issued_at ?? 'N/A')}</div>
                    <div class="text-muted small">${escapeHtml(item.method ?? 'N/A')}</div>
                </div>
                <strong>${escapeHtml(item.amount_text ?? '0 VND')}</strong>
            `;
            list.appendChild(wrapper);
        });
    }

    function setText(element, value) {
        if (!element) {
            return;
        }
        element.textContent = value ?? '';
    }

    function buildUrl(template, invoiceId) {
        if (!template || !invoiceId) {
            return '';
        }

        return template.replace('__INVOICE__', invoiceId);
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) {
            return '';
        }
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }
});
