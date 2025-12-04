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
});
