﻿(function () {
    'use strict';

    const config = window.lessonDiscussionBootstrap || {};
    config.permissions = config.permissions || {};
    config.moderation = config.moderation || null;
    config.user = config.user || null;

    const root = document.querySelector('[data-discussion-root]');
    const toggle = document.querySelector('[data-discussion-toggle]');

    if (!root || !toggle || !config.fetchUrl) {
        return;
    }

    const panel = root.querySelector('.lesson-discussion__panel');
    const overlay = root.querySelector('.lesson-discussion__overlay');
    const listEl = root.querySelector('[data-discussion-list]');
    const emptyEl = root.querySelector('[data-discussion-empty]');
    const footerEl = root.querySelector('[data-discussion-footer]');
    const loadMoreBtn = footerEl ? footerEl.querySelector('[data-discussion-load-more]') : null;
    const composerForm = root.querySelector('[data-discussion-form]');
    const composerInput = root.querySelector('[data-discussion-input]');
    const toggleCount = document.querySelector('[data-discussion-count]');

    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    const state = {
        discussions: [],
        meta: null,
        total: Number(config.total || 0),
        isLoading: false,
        isLoaded: false,
        isSubmitting: false,
    };

    let previousOverflow = '';

    toggle.addEventListener('click', function () {
        if (root.classList.contains('is-open')) {
            closePanel();
            return;
        }

        openPanel();
        if (!state.isLoaded) {
            fetchDiscussions();
        }
    });

    if (overlay) {
        overlay.addEventListener('click', closePanel);
    }

    root.addEventListener('click', function (event) {
        const closeTrigger = event.target.closest('[data-discussion-close]');
        if (closeTrigger) {
            closePanel();
            return;
        }

        const actionBtn = event.target.closest('[data-discussion-action]');
        if (actionBtn) {
            const action = actionBtn.getAttribute('data-discussion-action');
            const idAttr = actionBtn.getAttribute('data-discussion-id');
            const discussionId = idAttr ? parseInt(idAttr, 10) : NaN;

            if (!discussionId) {
                return;
            }

            if (action === 'reply-toggle') {
                toggleReplyForm(discussionId, null, null);
                return;
            }

            if (action === 'pin') {
                togglePin(discussionId, actionBtn);
                return;
            }

            if (action === 'lock') {
                toggleLock(discussionId, actionBtn);
                return;
            }

            if (action === 'mark-resolved') {
                updateStatus(discussionId, 'RESOLVED', actionBtn);
                return;
            }

            if (action === 'mark-open') {
                updateStatus(discussionId, 'OPEN', actionBtn);
                return;
            }

            if (action === 'delete') {
                deleteDiscussion(discussionId, actionBtn);
                return;
            }
        }

        const replyActionBtn = event.target.closest('[data-reply-action]');
        if (replyActionBtn) {
            const discussionAttr = replyActionBtn.getAttribute('data-discussion-id');
            const discussionId = discussionAttr ? parseInt(discussionAttr, 10) : NaN;
            if (!discussionId) {
                return;
            }

            const action = replyActionBtn.getAttribute('data-reply-action');
            if (action === 'delete') {
                const replyAttr = replyActionBtn.getAttribute('data-reply-id');
                const replyId = replyAttr ? parseInt(replyAttr, 10) : NaN;

                if (replyId) {
                    deleteReply(discussionId, replyId, replyActionBtn);
                }
                return;
            }

            if (action === 'reply') {
                const replyAttr = replyActionBtn.getAttribute('data-reply-id');
                const replyId = replyAttr ? parseInt(replyAttr, 10) : NaN;
                if (!replyId) {
                    return;
                }
                const replyItem = replyActionBtn.closest('.discussion-reply');
                toggleReplyForm(discussionId, replyId, replyItem);
            }
        }
    });

    root.addEventListener('submit', function (event) {
        const replyForm = event.target.closest('.reply-form');
        if (!replyForm) {
            return;
        }

        event.preventDefault();
        const discussionAttr = replyForm.getAttribute('data-discussion-id');
        const discussionId = discussionAttr ? parseInt(discussionAttr, 10) : NaN;
        const textarea = replyForm.querySelector('textarea');

        if (!discussionId || !textarea) {
            return;
        }

        submitReply(discussionId, textarea.value, replyForm);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && root.classList.contains('is-open')) {
            closePanel();
        }
    });

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function () {
            if (!state.meta || !state.meta.has_more || state.isLoading) {
                return;
            }

            const nextPage = state.meta.current_page ? state.meta.current_page + 1 : 2;
            fetchDiscussions(nextPage, true);
        });
    }

    if (composerForm && composerInput && config.storeUrl) {
        composerForm.addEventListener('submit', function (event) {
            event.preventDefault();
            if (state.isSubmitting) {
                return;
            }

            const content = composerInput.value.trim();
            if (content.length < 8) {
                renderComposerError('Vui lòng mô tả chi tiết hơn (tối thiểu 8 ký tự).');
                return;
            }

            submitDiscussion(content);
        });
    }

    function openPanel() {
        if (root.classList.contains('is-open')) {
            return;
        }

        root.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
        previousOverflow = document.body.style.overflow;
        document.body.style.overflow = 'hidden';

        if (panel) {
            panel.setAttribute('tabindex', '-1');
            panel.focus({ preventScroll: true });
        }
    }

    function closePanel() {
        if (!root.classList.contains('is-open')) {
            return;
        }

        root.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = previousOverflow;

        if (panel) {
            panel.removeAttribute('tabindex');
        }
    }

    async function fetchDiscussions(page, append) {
        if (state.isLoading) {
            return;
        }

        setLoading(true);

        try {
            const url = new URL(config.fetchUrl, window.location.origin);
            url.searchParams.set('page', String(page || 1));

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('REQUEST_FAILED');
            }

            const payload = await safeJson(response) || {};
            const incoming = Array.isArray(payload.data) ? payload.data : [];

            state.meta = payload.meta || null;
            if (payload.meta && typeof payload.meta.total === 'number') {
                state.total = payload.meta.total;
            }

            state.discussions = append ? state.discussions.concat(incoming) : incoming;
            state.isLoaded = true;

            renderDiscussions();
            updateCount();
        } catch (error) {
            renderGlobalError('Không thể tải danh sách hỏi đáp. Vui lòng thử lại.');
        } finally {
            setLoading(false);
        }
    }

    function renderDiscussions() {
        Array.from(listEl.querySelectorAll('.discussion-card')).forEach(function (node) {
            node.remove();
        });

        Array.from(listEl.querySelectorAll('.discussion-list__error')).forEach(function (node) {
            node.remove();
        });

        if (!state.discussions.length) {
            if (emptyEl) {
                emptyEl.hidden = false;
            }
        } else if (emptyEl) {
            emptyEl.hidden = true;
        }

        const fragment = document.createDocumentFragment();

        state.discussions.forEach(function (discussion) {
            fragment.appendChild(createDiscussionCard(discussion));
        });

        listEl.appendChild(fragment);

        if (loadMoreBtn) {
            const hasMore = state.meta && Boolean(state.meta.has_more);
            loadMoreBtn.hidden = !hasMore;
            loadMoreBtn.disabled = !hasMore || state.isLoading;
        }
    }

    function createDiscussionCard(discussion) {
        const card = document.createElement('article');
        card.className = 'discussion-card';
        card.dataset.discussionId = String(discussion.id);

        if (discussion.is_pinned) {
            card.classList.add('is-pinned');
        }

        const author = discussion.author || {};

        const header = document.createElement('div');
        header.className = 'discussion-card__header';

        const user = document.createElement('div');
        user.className = 'discussion-card__user';

        const avatar = document.createElement('div');
        avatar.className = 'discussion-card__avatar';
        avatar.textContent = author.initials || 'HV';

        const meta = document.createElement('div');
        meta.className = 'discussion-card__meta';

        const nameRow = document.createElement('div');
        nameRow.className = 'discussion-card__name';

        const name = document.createElement('span');
        name.textContent = author.name || 'Người dùng';
        nameRow.appendChild(name);

        const timestamp = document.createElement('div');
        timestamp.className = 'discussion-card__timestamp';
        timestamp.textContent = discussion.created_human || '';

        const labels = document.createElement('div');
        labels.className = 'discussion-card__labels';

        if (discussion.is_pinned) {
            labels.appendChild(createLabel('Đã ghim', 'label--pinned', 'bi-pin-angle'));
        }

        if (discussion.status === 'RESOLVED') {
            labels.appendChild(createLabel('Đã giải đáp', 'label--resolved', 'bi-check-circle'));
        }

        if (discussion.is_locked) {
            labels.appendChild(createLabel('Đã khóa', 'label--locked', 'bi-lock'));
        }

        meta.appendChild(nameRow);
        meta.appendChild(timestamp);
        if (labels.childElementCount) {
            meta.appendChild(labels);
        }

        user.appendChild(avatar);
        user.appendChild(meta);
        header.appendChild(user);
        card.appendChild(header);

        const content = document.createElement('div');
        content.className = 'discussion-card__content';
        content.textContent = discussion.content || '';
        card.appendChild(content);

        const actions = document.createElement('div');
        actions.className = 'discussion-card__actions';

        const leftActions = document.createElement('div');
        leftActions.className = 'discussion-card__actions';

        if (config.permissions.can_reply && discussion.status !== 'HIDDEN') {
            const replyBtn = document.createElement('button');
            replyBtn.type = 'button';
            replyBtn.dataset.discussionAction = 'reply-toggle';
            replyBtn.dataset.discussionId = String(discussion.id);
            replyBtn.innerHTML = "<i class='bi bi-reply'></i> <span>Trả lời</span>";
            leftActions.appendChild(replyBtn);
        }

        const repliesMeta = document.createElement('div');
        repliesMeta.className = 'discussion-card__actions-aux';
        const replyCount = typeof discussion.reply_count === 'number'
            ? discussion.reply_count
            : (discussion.replies ? discussion.replies.length : 0);
        repliesMeta.innerHTML = "<i class='bi bi-chat-dots'></i> <span>" + replyCount + " phản hồi</span>";
        leftActions.appendChild(repliesMeta);
        actions.appendChild(leftActions);

        const rightActions = document.createElement('div');
        rightActions.className = 'discussion-card__moderation';

        if (canDeleteDiscussion(discussion)) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.dataset.discussionAction = 'delete';
            deleteBtn.dataset.discussionId = String(discussion.id);
            deleteBtn.textContent = 'Xóa';
            rightActions.appendChild(deleteBtn);
        }

        if (config.permissions.can_moderate) {
            const pinBtn = document.createElement('button');
            pinBtn.type = 'button';
            pinBtn.dataset.discussionAction = 'pin';
            pinBtn.dataset.discussionId = String(discussion.id);
            pinBtn.textContent = discussion.is_pinned ? 'Bỏ ghim' : 'Ghim';
            rightActions.appendChild(pinBtn);

            const lockBtn = document.createElement('button');
            lockBtn.type = 'button';
            lockBtn.dataset.discussionAction = 'lock';
            lockBtn.dataset.discussionId = String(discussion.id);
            lockBtn.textContent = discussion.is_locked ? 'Mở khóa' : 'Khóa';
            rightActions.appendChild(lockBtn);

            const statusBtn = document.createElement('button');
            statusBtn.type = 'button';
            statusBtn.dataset.discussionAction = discussion.status === 'RESOLVED'
                ? 'mark-open'
                : 'mark-resolved';
            statusBtn.dataset.discussionId = String(discussion.id);
            statusBtn.textContent = discussion.status === 'RESOLVED'
                ? 'Mở lại'
                : 'Đánh dấu đã giải đáp';
            rightActions.appendChild(statusBtn);
        }

        if (rightActions.childElementCount) {
            actions.appendChild(rightActions);
        }

        card.appendChild(actions);

        const repliesContainer = document.createElement('div');
        repliesContainer.className = 'discussion-replies';

        const replyTree = buildReplyTree(discussion.replies || []);
        replyTree.forEach(function (replyNode) {
            repliesContainer.appendChild(createReplyThread(replyNode, discussion));
        });

        if (config.permissions.can_reply && !discussion.is_locked && discussion.status !== 'HIDDEN') {
            repliesContainer.appendChild(createReplyForm(discussion.id));
        }

        card.appendChild(repliesContainer);

        return card;
    }

    function createReplyThread(node, discussion) {
        const reply = node || {};
        const replyWrap = document.createElement('div');
        replyWrap.className = 'discussion-reply';
        replyWrap.dataset.replyId = String(reply.id);
        replyWrap.dataset.replyAuthor = reply.author?.name || '';
        replyWrap.dataset.discussionId = String(discussion.id);

        if (reply.is_official) {
            replyWrap.classList.add('is-official');
        }

        const author = reply.author || {};

        const header = document.createElement('div');
        header.className = 'discussion-reply__header';

        const name = document.createElement('span');
        name.className = 'discussion-reply__name';
        name.textContent = author.name || 'Người dùng';
        header.appendChild(name);

        if (reply.is_official || author.role === 'GIANG_VIEN') {
            const badge = document.createElement('span');
            badge.className = 'discussion-reply__badge';
            badge.textContent = reply.is_official ? 'Giảng viên' : resolveRoleLabel(author.role);
            header.appendChild(badge);
        }

        const timestamp = document.createElement('span');
        timestamp.className = 'discussion-reply__timestamp';
        timestamp.textContent = reply.created_human || '';
        header.appendChild(timestamp);

        replyWrap.appendChild(header);

        const content = document.createElement('div');
        content.className = 'discussion-reply__content';
        content.textContent = reply.content || '';
        replyWrap.appendChild(content);

        const controls = document.createElement('div');
        controls.className = 'discussion-reply__actions';

        if (config.permissions.can_reply && !discussion.is_locked) {
            const replyBtn = document.createElement('button');
            replyBtn.type = 'button';
            replyBtn.dataset.replyAction = 'reply';
            replyBtn.dataset.discussionId = String(discussion.id);
            replyBtn.dataset.replyId = String(reply.id);
            replyBtn.textContent = 'Trả lời';
            controls.appendChild(replyBtn);
        }

        if (canDeleteReply(reply, discussion)) {
            const deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.dataset.replyAction = 'delete';
            deleteBtn.dataset.discussionId = String(discussion.id);
            deleteBtn.dataset.replyId = String(reply.id);
            deleteBtn.textContent = 'Xóa';
            controls.appendChild(deleteBtn);
        }

        if (controls.childElementCount) {
            replyWrap.appendChild(controls);
        }

        const children = Array.isArray(node.children) ? node.children : [];
        if (children.length) {
            const childrenContainer = document.createElement('div');
            childrenContainer.className = 'discussion-reply__children';
            children.forEach(function (childNode) {
                childrenContainer.appendChild(createReplyThread(childNode, discussion));
            });
            replyWrap.appendChild(childrenContainer);
        }

        return replyWrap;
    }

    function createReplyForm(discussionId) {
        const form = document.createElement('form');
        form.className = 'reply-form';
        form.dataset.discussionId = String(discussionId);
        form.dataset.parentId = '';

        const context = document.createElement('div');
        context.className = 'reply-form__context';
        context.setAttribute('data-reply-context', '');
        context.hidden = true;
        form.appendChild(context);

        const textarea = document.createElement('textarea');
        textarea.placeholder = 'Bạn muốn chia sẻ gì?';
        form.appendChild(textarea);

        const actions = document.createElement('div');
        actions.className = 'reply-form__actions';

        const submitBtn = document.createElement('button');
        submitBtn.type = 'submit';
        submitBtn.className = 'btn btn--primary';
        submitBtn.textContent = 'Gửi phản hồi';
        actions.appendChild(submitBtn);

        const cancelBtn = document.createElement('button');
        cancelBtn.type = 'button';
        cancelBtn.className = 'btn btn--ghost';
        cancelBtn.textContent = 'Hủy';
        cancelBtn.addEventListener('click', function () {
            resetReplyForm(form);
        });
        actions.appendChild(cancelBtn);

        form.appendChild(actions);
        return form;
    }

    function toggleReplyForm(discussionId, parentReplyId, replyElement) {
        const card = listEl.querySelector('[data-discussion-id="' + discussionId + '"]');
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

        Array.from(listEl.querySelectorAll('.reply-form')).forEach(function (node) {
            if (node !== form) {
                resetReplyForm(node);
            }
        });

        if (isVisible) {
            resetReplyForm(form);
            return;
        }

        let container = card.querySelector('.discussion-replies');
        let replyTarget = replyElement || null;

        if (parentReplyId && !replyTarget) {
            replyTarget = listEl.querySelector('[data-reply-id="' + parentReplyId + '"]');
        }

        if (parentReplyId) {
            const nestedContainer = ensureReplyChildContainer(replyTarget);
            container = nestedContainer || container;
        }

        if (!container) {
            return;
        }

        container.appendChild(form);
        form.dataset.parentId = targetParent;

        const context = form.querySelector('[data-reply-context]');
        if (context) {
            if (parentReplyId && replyTarget) {
                const authorName = replyTarget.getAttribute('data-reply-author') || 'một phản hồi';
                context.hidden = false;
                context.textContent = 'Đang trả lời ' + authorName;
            } else {
                context.hidden = true;
                context.textContent = '';
            }
        }

        form.classList.add('is-visible');
        const textarea = form.querySelector('textarea');
        if (textarea) {
            textarea.focus();
        }
    }

    async function submitDiscussion(content) {
        state.isSubmitting = true;
        renderComposerError(null);

        try {
            const response = await fetch(config.storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({ noi_dung: content }),
            });

            if (!response.ok) {
                const errorPayload = await safeJson(response);
                const message = errorPayload && errorPayload.message
                    ? errorPayload.message
                    : 'Không thể đăng câu hỏi. Vui lòng thử lại.';
                renderComposerError(message);
                return;
            }

            const payload = await safeJson(response) || {};
            if (payload.data) {
                state.discussions.unshift(payload.data);
                state.total += 1;
                renderDiscussions();
                updateCount();

                if (composerInput) {
                    composerInput.value = '';
                }
            }
        } catch (error) {
            renderComposerError('Không thể đăng câu hỏi. Vui lòng thử lại.');
        } finally {
            state.isSubmitting = false;
        }
    }

    async function submitReply(discussionId, rawContent, form) {
        if (!config.replyUrlTemplate) {
            return;
        }

        const content = (rawContent || '').trim();
        if (content.length < 3) {
            renderInlineError(form, 'Vui lòng nhập tối thiểu 3 ký tự.');
            return;
        }

        const url = config.replyUrlTemplate.replace('__DISCUSSION__', discussionId);
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
        }
        const textarea = form.querySelector('textarea');
        if (textarea) {
            textarea.disabled = true;
        }
        renderInlineError(form, null);

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
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const errorPayload = await safeJson(response);
                const message = errorPayload && errorPayload.message
                    ? errorPayload.message
                    : 'Không thể gửi phản hồi.';
                renderInlineError(form, message);
                return;
            }

            const payloadBody = await safeJson(response) || {};
            const updated = payloadBody.data && payloadBody.data.discussion;

            if (updated) {
                state.discussions = state.discussions.map(function (item) {
                    return item.id === updated.id ? updated : item;
                });

                renderDiscussions();
                resetReplyForm(form);
            }
        } catch (error) {
            renderInlineError(form, 'Không thể gửi phản hồi. Vui lòng thử lại!');
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
            }
            if (textarea) {
                textarea.disabled = false;
            }
        }
    }

    function togglePin(discussionId, trigger) {
        if (!config.moderation || !config.moderation.pinUrlTemplate) {
            return;
        }

        const url = config.moderation.pinUrlTemplate.replace('__DISCUSSION__', discussionId);
        mutateDiscussion(url, trigger, null, 'PATCH');
    }

    function toggleLock(discussionId, trigger) {
        if (!config.moderation || !config.moderation.lockUrlTemplate) {
            return;
        }

        const url = config.moderation.lockUrlTemplate.replace('__DISCUSSION__', discussionId);
        mutateDiscussion(url, trigger, null, 'PATCH');
    }

    function updateStatus(discussionId, status, trigger) {
        if (!config.moderation || !config.moderation.statusUrlTemplate) {
            return;
        }

        const url = config.moderation.statusUrlTemplate.replace('__DISCUSSION__', discussionId);
        mutateDiscussion(url, trigger, { status: status }, 'PATCH');
    }

    function deleteDiscussion(discussionId, trigger) {
        if (!config.deleteUrlTemplate) {
            return;
        }

        if (!window.confirm('Bạn có chắc muốn ẩn câu hỏi này?')) {
            return;
        }

        const url = config.deleteUrlTemplate.replace('__DISCUSSION__', discussionId);
        mutateDiscussion(url, trigger, null, 'DELETE');
    }

    async function deleteReply(discussionId, replyId, trigger) {
        if (!config.deleteReplyUrlTemplate) {
            return;
        }

        if (!window.confirm('Bạn có chắc muốn xóa phản hồi này?')) {
            return;
        }

        const url = config.deleteReplyUrlTemplate
            .replace('__DISCUSSION__', discussionId)
            .replace('__REPLY__', replyId);

        trigger.disabled = true;

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('REQUEST_FAILED');
            }

            state.discussions = state.discussions.map(function (item) {
                if (item.id !== discussionId) {
                    return item;
                }

                const replies = Array.isArray(item.replies) ? item.replies.slice() : [];
                const branchIds = collectReplyBranchIds(replies, replyId);
                if (!branchIds.length) {
                    return item;
                }

                const remaining = replies.filter(function (reply) {
                    return !branchIds.includes(reply.id);
                });

                const removedCount = replies.length - remaining.length;

                return Object.assign({}, item, {
                    replies: remaining,
                    reply_count: Math.max(0, (item.reply_count || 0) - removedCount),
                });
            });

            renderDiscussions();
        } catch (error) {
            renderGlobalError('Không thể xóa phản hồi. Vui lòng thử lại.');
        } finally {
            trigger.disabled = false;
        }
    }

    async function mutateDiscussion(url, trigger, body, method) {
        trigger.disabled = true;

        try {
            const response = await fetch(url, {
                method: method || 'PATCH',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: body ? JSON.stringify(body) : null,
            });

            if (!response.ok) {
                throw new Error('REQUEST_FAILED');
            }

            const currentPage = state.meta && state.meta.current_page
                ? state.meta.current_page
                : 1;
            await fetchDiscussions(currentPage);
        } catch (error) {
            renderGlobalError('Không thể cập nhật chủ đề. Vui lòng thử lại.');
        } finally {
            trigger.disabled = false;
        }
    }

    function buildReplyTree(flatReplies) {
        if (!Array.isArray(flatReplies) || !flatReplies.length) {
            return [];
        }

        const map = new Map();
        flatReplies.forEach(function (reply) {
            const node = Object.assign({}, reply);
            node.children = [];
            map.set(reply.id, node);
        });

        const roots = [];
        map.forEach(function (node) {
            if (node.parent_reply_id && map.has(node.parent_reply_id)) {
                const parent = map.get(node.parent_reply_id);
                parent.children.push(node);
            } else {
                roots.push(node);
            }
        });

        return roots;
    }

    function collectReplyBranchIds(replies, rootId) {
        if (!rootId) {
            return [];
        }
        const ids = [];
        const queue = [rootId];

        while (queue.length) {
            const current = queue.shift();
            ids.push(current);

            replies.forEach(function (reply) {
                if (reply.parent_reply_id === current) {
                    queue.push(reply.id);
                }
            });
        }

        return ids;
    }

    function ensureReplyChildContainer(replyElement) {
        if (!replyElement) {
            return null;
        }
        let children = replyElement.querySelector('.discussion-reply__children');
        if (!children) {
            children = document.createElement('div');
            children.className = 'discussion-reply__children';
            replyElement.appendChild(children);
        }
        return children;
    }

    function resetReplyForm(form) {
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

        const context = form.querySelector('[data-reply-context]');
        if (context) {
            context.hidden = true;
            context.textContent = '';
        }

        const card = form.closest('.discussion-card');
        const rootContainer = card ? card.querySelector('.discussion-replies') : null;
        if (rootContainer && form.parentElement !== rootContainer) {
            rootContainer.appendChild(form);
        }

        renderInlineError(form, null);
    }

    function updateCount() {
        if (!toggleCount) {
            return;
        }
        toggleCount.textContent = String(state.total || 0);
    }

    function setLoading(flag) {
        state.isLoading = flag;
        root.dataset.loading = flag ? 'true' : 'false';
        if (loadMoreBtn) {
            loadMoreBtn.disabled = flag;
        }
    }

    function renderComposerError(message) {
        if (!composerForm) {
            return;
        }

        let errorBlock = composerForm.querySelector('.lesson-discussion__error');

        if (!message) {
            if (errorBlock) {
                errorBlock.remove();
            }
            return;
        }

        if (!errorBlock) {
            errorBlock = document.createElement('div');
            errorBlock.className = 'lesson-discussion__error';
            composerForm.prepend(errorBlock);
        }

        errorBlock.textContent = message;
    }

    function renderInlineError(form, message) {
        if (!form) {
            return;
        }

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
            form.insertBefore(errorBlock, form.firstChild);
        }

        errorBlock.textContent = message;
    }

    function renderGlobalError(message) {
        let errorBlock = listEl.querySelector('.discussion-list__error');
        if (!errorBlock) {
            errorBlock = document.createElement('div');
            errorBlock.className = 'lesson-discussion__error discussion-list__error';
            listEl.insertBefore(errorBlock, listEl.firstChild);
        }
        errorBlock.textContent = message;
    }

    function createLabel(text, modifier, icon) {
        const span = document.createElement('span');
        span.className = 'label ' + modifier;
        span.innerHTML = "<i class='" + icon + "'></i> <span>" + text + "</span>";
        return span;
    }

    function resolveRoleLabel(role) {
        if (role === 'GIANG_VIEN') {
            return 'Giảng viên';
        }
        if (role === 'ADMIN') {
            return 'Quản trị';
        }
        if (role === 'HOC_VIEN') {
            return 'Học viên';
        }
        return role || 'Thành viên';
    }

    function canDeleteDiscussion(discussion) {
        if (config.permissions.can_moderate) {
            return true;
        }
        if (!config.user) {
            return false;
        }
        return discussion.author && discussion.author.id === config.user.id;
    }

    function canDeleteReply(reply, discussion) {
        if (config.permissions.can_moderate) {
            return true;
        }
        if (!config.user) {
            return false;
        }
        return reply.author && reply.author.id === config.user.id;
    }

    async function safeJson(response) {
        try {
            return await response.json();
        } catch (error) {
            return null;
        }
    }
})();