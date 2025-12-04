document.addEventListener("DOMContentLoaded", () => {
    const courseSelector = document.getElementById("progressCourseSelector");
    const statusFilter = document.getElementById("progressStatusFilter");
    const searchInput = document.getElementById("progressSearchInput");
    const filterForm = document.getElementById("progressFilterForm");
    const resetButton = document.getElementById("progressFilterReset");

    if (courseSelector) {
        courseSelector.addEventListener("change", () => {
            const template = courseSelector.dataset.showTemplate || "";
            if (template && courseSelector.value) {
                const targetUrl = template.replace("__COURSE__", courseSelector.value);
                const url = new URL(targetUrl, window.location.origin);

                if (statusFilter?.value) {
                    url.searchParams.set("status", statusFilter.value);
                }
                if (searchInput?.value) {
                    url.searchParams.set("search", searchInput.value.trim());
                }

                window.location.href = url.toString();
                return;
            }

            filterForm?.submit();
        });
    }

    if (statusFilter && filterForm) {
        statusFilter.addEventListener("change", () => filterForm.submit());
    }

    if (resetButton && filterForm) {
        resetButton.addEventListener("click", () => {
            if (statusFilter) statusFilter.value = "";
            if (searchInput) searchInput.value = "";
            filterForm.submit();
        });
    }

    const courseGrid = document.querySelector("[data-course-grid]");
    const viewButtons = document.querySelectorAll("[data-course-view]");
    const courseItems = Array.from(document.querySelectorAll("[data-course-item]"));
    const courseSearch = document.querySelector("[data-course-search]");
    const courseSort = document.querySelector("[data-course-sort]");
    const courseCount = document.querySelector("[data-course-count]");
    const courseEmpty = document.querySelector("[data-course-empty]");
    const filterButtons = Array.from(document.querySelectorAll("[data-course-filter]"));

    const normalizeText = (value = "") =>
        value.toString().normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();

    let activeFilter = "all";
    let searchQuery = "";

    const applyFilters = () => {
        const query = normalizeText(searchQuery);
        courseItems.forEach(item => {
            const title = normalizeText(item.dataset.title || "");
            const learners = Number(item.dataset.learners || 0);
            const average = Number(item.dataset.average || 0);
            const isActive = item.dataset.active === "1";

            const matchSearch = !query || title.includes(query);
            let matchFilter = true;
            switch (activeFilter) {
                case "has-learners":
                    matchFilter = learners > 0;
                    break;
                case "no-learners":
                    matchFilter = learners === 0;
                    break;
                case "in-progress":
                    matchFilter = average > 0;
                    break;
                case "active":
                    matchFilter = isActive;
                    break;
                default:
                    matchFilter = true;
            }
            item.style.display = matchSearch && matchFilter ? "" : "none";
        });
        updateCount();
        applySort();
    };

    const applySort = () => {
        if (!courseGrid || !courseSort) return;
        const sortValue = courseSort.value;
        const sorted = [...courseItems].sort((a, b) => {
            const aName = (a.dataset.title || "").toLowerCase();
            const bName = (b.dataset.title || "").toLowerCase();
            const aLearners = Number(a.dataset.learners || 0);
            const bLearners = Number(b.dataset.learners || 0);
            const aAvg = Number(a.dataset.average || 0);
            const bAvg = Number(b.dataset.average || 0);
            const aActive = a.dataset.active === "1";
            const bActive = b.dataset.active === "1";

            switch (sortValue) {
                case "progress-desc":
                    return bAvg - aAvg || bLearners - aLearners || aName.localeCompare(bName);
                case "students-desc":
                    return bLearners - aLearners || bAvg - aAvg || aName.localeCompare(bName);
                case "active-first":
                    if (aActive !== bActive) return aActive ? -1 : 1;
                    return bAvg - aAvg || bLearners - aLearners || aName.localeCompare(bName);
                default:
                    return aName.localeCompare(bName);
            }
        });

        const fragment = document.createDocumentFragment();
        sorted.forEach(item => fragment.appendChild(item));
        courseGrid.appendChild(fragment);
    };

    const updateCount = () => {
        const visible = courseItems.filter(item => item.style.display !== "none");
        if (courseCount) {
            courseCount.textContent = `Đang hiển thị: ${visible.length}`;
        }
        if (courseEmpty) {
            courseEmpty.hidden = visible.length > 0;
        }
    };

    if (courseGrid && viewButtons.length) {
        const setView = (view) => {
            courseGrid.setAttribute("data-view", view);
            viewButtons.forEach(btn => btn.classList.toggle("active", btn.dataset.courseView === view));
        };
        viewButtons.forEach(btn => {
            btn.addEventListener("click", () => setView(btn.dataset.courseView || "grid"));
        });
    }

    if (courseSearch) {
        let debounceId;
        courseSearch.addEventListener("input", (e) => {
            searchQuery = e.target.value || "";
            clearTimeout(debounceId);
            debounceId = setTimeout(() => applyFilters(), 180);
        });
    }

    if (filterButtons.length) {
        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                activeFilter = btn.dataset.courseFilter || "all";
                filterButtons.forEach(b => b.classList.toggle("is-active", b === btn));
                applyFilters();
            });
        });
    }

    if (courseSort) {
        courseSort.addEventListener("change", () => applySort());
    }

    // initial state
    applyFilters();
});
