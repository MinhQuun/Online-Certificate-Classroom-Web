"use strict";

document.addEventListener("DOMContentLoaded", () => {
    const page = document.querySelector("[data-certificates-page]");
    if (!page) {
        return;
    }

    const filterForm = document.getElementById("certificateFilters");
    if (filterForm) {
        filterForm
            .querySelectorAll("[data-auto-submit]")
            .forEach((element) => {
                element.addEventListener("change", () => filterForm.submit());
            });
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        return new Promise((resolve, reject) => {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            textarea.style.position = "fixed";
            textarea.style.left = "-9999px";
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();

            try {
                document.execCommand("copy");
                resolve();
            } catch (error) {
                reject(error);
            } finally {
                textarea.remove();
            }
        });
    }

    document.querySelectorAll("[data-copy-code]").forEach((button) => {
        button.addEventListener("click", (event) => {
            event.preventDefault();
            const code = button.getAttribute("data-copy-code") || "";
            if (!code) {
                return;
            }

            const originalText = button.textContent;
            copyToClipboard(code)
                .then(() => {
                    button.textContent = "Đã sao chép";
                    button.classList.add("copied");
                    setTimeout(() => {
                        button.textContent = originalText;
                        button.classList.remove("copied");
                    }, 1600);
                })
                .catch(() => {
                    button.textContent = "Lỗi sao chép";
                    setTimeout(() => {
                        button.textContent = originalText;
                    }, 1800);
                });
        });
    });
});
