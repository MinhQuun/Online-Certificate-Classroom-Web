document.addEventListener("DOMContentLoaded", () => {
    const trigger = document.querySelector("[data-notification-trigger]");
    const overlay = document.querySelector("[data-notification-overlay]");

    if (!trigger || !overlay) return;

    const listEl = overlay.querySelector("[data-notification-list]");
    const loadingEl = overlay.querySelector("[data-notification-loading]");
    const emptyEl = overlay.querySelector("[data-notification-empty]");
    const guestEl = overlay.querySelector("[data-notification-guest]");
    const errorEl = overlay.querySelector("[data-notification-error]");
    const unreadEl = overlay.querySelector("[data-notification-unread]");
    const badgeEl = trigger.querySelector("[data-notification-badge]");
    const closeBtn = overlay.querySelector("[data-notification-close]");
    const markAllBtn = overlay.querySelector("[data-notification-mark-all]");
    const refreshBtn = overlay.querySelector("[data-notification-refresh]");

    const endpoints = {
        fetch: trigger.getAttribute("data-endpoint"),
        markAll: trigger.getAttribute("data-mark-all-endpoint"),
        markOneTemplate: trigger.getAttribute("data-read-template"),
    };

    const csrf =
        document.querySelector('meta[name="csrf-token"]')?.content || "";

    let hasLoaded = false;
    let forceHideList = false;

    const toggleOverlay = (shouldShow) => {
        overlay.classList.toggle("is-open", shouldShow);
        document.body.classList.toggle("notification-open", shouldShow);
        overlay.setAttribute("aria-hidden", shouldShow ? "false" : "true");
    };

    const setUnread = (value) => {
        const count = Math.max(0, parseInt(value ?? 0, 10) || 0);

        if (badgeEl) {
            if (count > 0) {
                badgeEl.textContent = count > 9 ? "9+" : String(count);
                badgeEl.classList.remove("is-hidden");
            } else {
                badgeEl.classList.add("is-hidden");
            }
        }

        if (unreadEl) {
            unreadEl.textContent = count;
        }

        if (markAllBtn) {
            markAllBtn.disabled = count === 0;
        }

        trigger.dataset.unreadCount = String(count);
    };

    const toggleLoading = (state) => {
        loadingEl?.classList.toggle("is-hidden", !state);
        listEl?.classList.toggle("is-hidden", state);
    };

    const showGuestState = () => {
        guestEl?.classList.remove("is-hidden");
        emptyEl?.classList.add("is-hidden");
        listEl?.classList.add("is-hidden");
        markAllBtn?.setAttribute("disabled", "disabled");
    };

    const renderList = (items) => {
        if (!listEl) return;
        listEl.innerHTML = "";

        if (!items || items.length === 0) {
            emptyEl?.classList.remove("is-hidden");
            return;
        }

        emptyEl?.classList.add("is-hidden");

        items.forEach((item) => {
            listEl.appendChild(buildCard(item));
        });
    };

    const buildCard = (item) => {
        const card = document.createElement("article");
        card.className = "notification-card";
        if (item?.is_read) {
            card.classList.add("is-read");
        } else {
            card.classList.add("is-unread");
        }
        card.dataset.notificationId = item?.id;

        const media = document.createElement("div");
        media.className = "notification-card__media";
        const img = document.createElement("img");
        img.src = item?.thumbnail || trigger.dataset.fallbackImage || "";
        img.alt = "Minh hoa thong bao";
        img.loading = "lazy";
        media.appendChild(img);

        const content = document.createElement("div");
        content.className = "notification-card__content";

        const top = document.createElement("div");
        top.className = "notification-card__top";

        const pill = document.createElement("span");
        const tone = item?.badge_tone || "accent";
        pill.className = `notification-pill notification-pill--${tone}`;
        pill.textContent = item?.type_label || "Thong bao";

        const time = document.createElement("span");
        time.className = "notification-card__time";
        time.textContent = item?.time_label || "";

        top.appendChild(pill);
        top.appendChild(time);

        const title = document.createElement("h4");
        title.className = "notification-card__title";
        title.textContent = item?.title || "Thong bao moi";

        const desc = document.createElement("p");
        desc.className = "notification-card__desc";
        desc.textContent = item?.content || "";

        const actions = document.createElement("div");
        actions.className = "notification-card__actions";

        if (item?.action_url) {
            const link = document.createElement("a");
            link.className = "notification-card__cta";
            link.href = item.action_url;
            link.textContent = item?.action_label || "Xem chi tiết";
            actions.appendChild(link);
        }

        if (item?.is_read) {
            const status = document.createElement("span");
            status.className = "notification-card__status";
            status.textContent = "Da doc";
            actions.appendChild(status);
        } else {
            const markBtn = document.createElement("button");
            markBtn.type = "button";
            markBtn.className = "notification-card__mark";
            markBtn.dataset.action = "mark-read";
            markBtn.textContent = "Danh dau da doc";
            actions.appendChild(markBtn);
        }

        content.appendChild(top);
        content.appendChild(title);
        content.appendChild(desc);
        content.appendChild(actions);

        card.appendChild(media);
        card.appendChild(content);

        return card;
    };

    const fetchNotifications = async () => {
        if (trigger.dataset.authenticated !== "1") return;

        forceHideList = false;
        toggleLoading(true);
        errorEl?.classList.add("is-hidden");
        guestEl?.classList.add("is-hidden");

        try {
            const res = await fetch(endpoints.fetch || "", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (res.status === 401 || res.status === 403) {
                forceHideList = true;
                showGuestState();
                return;
            }

            if (!res.ok) {
                throw new Error("Failed to load notifications");
            }

            const data = await res.json();
            renderList(data?.data || []);
            setUnread(data?.meta?.unread ?? trigger.dataset.unreadCount);
            hasLoaded = true;
        } catch (err) {
            console.error(err);
            errorEl?.classList.remove("is-hidden");
        } finally {
            toggleLoading(false);
            if (forceHideList) {
                listEl?.classList.add("is-hidden");
            }
        }
    };

    const markAsRead = async (id, card) => {
        if (!id || !endpoints.markOneTemplate) return;

        const url = endpoints.markOneTemplate.replace("__ID__", id);

        try {
            const res = await fetch(url, {
                method: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!res.ok) {
                return;
            }

            const data = await res.json();
            card?.classList.remove("is-unread");
            card?.classList.add("is-read");

            const btn = card?.querySelector("[data-action='mark-read']");
            if (btn) {
                btn.textContent = "Da doc";
                btn.disabled = true;
                btn.removeAttribute("data-action");
                btn.classList.remove("notification-card__mark");
                btn.classList.add("notification-card__status");
            }

            setUnread(data?.unread ?? trigger.dataset.unreadCount);
        } catch (err) {
            console.error(err);
        }
    };

    const markAllAsRead = async () => {
        if (!endpoints.markAll) return;

        try {
            const res = await fetch(endpoints.markAll, {
                method: "PATCH",
                headers: {
                    "X-CSRF-TOKEN": csrf,
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!res.ok) {
                return;
            }

            listEl
                ?.querySelectorAll(".notification-card")
                .forEach((card) => {
                    card.classList.add("is-read");
                    card.classList.remove("is-unread");
                    const btn = card.querySelector("[data-action='mark-read']");
                    if (btn) {
                        btn.textContent = "Da doc";
                        btn.disabled = true;
                        btn.removeAttribute("data-action");
                        btn.classList.remove("notification-card__mark");
                        btn.classList.add("notification-card__status");
                    }
                });

            const data = await res.json();
            setUnread(data?.unread ?? 0);
        } catch (err) {
            console.error(err);
        }
    };

    trigger.addEventListener("click", (event) => {
        if (trigger.dataset.authenticated !== "1") return;
        event.preventDefault();
        toggleOverlay(true);
        if (!hasLoaded) {
            fetchNotifications();
        }
    });

    closeBtn?.addEventListener("click", () => toggleOverlay(false));

    overlay.addEventListener("click", (event) => {
        if (event.target === overlay) {
            toggleOverlay(false);
        }
    });

    document.addEventListener("keyup", (event) => {
        if (event.key === "Escape" && overlay.classList.contains("is-open")) {
            toggleOverlay(false);
        }
    });

    listEl?.addEventListener("click", (event) => {
        const markBtn = event.target.closest("[data-action='mark-read']");
        if (!markBtn) return;

        const card = markBtn.closest(".notification-card");
        const id = card?.dataset.notificationId;
        markAsRead(id, card);
    });

    markAllBtn?.addEventListener("click", () => {
        if (markAllBtn.disabled) return;
        markAllAsRead();
    });

    refreshBtn?.addEventListener("click", () => {
        hasLoaded = false;
        fetchNotifications();
    });

    // Keep badge in sync with initial server-side data
    setUnread(trigger.dataset.unreadCount);
});
