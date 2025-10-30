document
    .getElementById("courseFilter")
    ?.addEventListener("change", function () {
        const courseId = this.value;
        const url = new URL(window.location.href);
        if (courseId) {
            url.searchParams.set("course", courseId);
        } else {
            url.searchParams.delete("course");
        }
        window.location.href = url.toString();
    });
