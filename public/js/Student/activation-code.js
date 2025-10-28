document.addEventListener("DOMContentLoaded", () => {
    const copyButtons = document.querySelectorAll(".activation-copy");

    copyButtons.forEach((button) => {
        button.addEventListener("click", async () => {
            const container = button.closest(".activation-history__item");
            const codeBlock = container
                ? container.querySelector("code")
                : null;
            const code = codeBlock ? codeBlock.textContent.trim() : "";

            if (!code) {
                return;
            }

            const reset = () => {
                button.dataset.state = "";
                button.textContent = "Sao chép";
            };

            try {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(code);
                } else {
                    throw new Error("Clipboard API not available");
                }
            } catch (error) {
                const fallbackInput = document.createElement("input");
                fallbackInput.value = code;
                fallbackInput.setAttribute("aria-hidden", "true");
                fallbackInput.style.position = "absolute";
                fallbackInput.style.opacity = "0";
                document.body.appendChild(fallbackInput);
                fallbackInput.select();
                try {
                    document.execCommand("copy");
                } catch (_) {
                    console.warn("Không thể sao chép mã kích hoạt.", error);
                }
                document.body.removeChild(fallbackInput);
            }

            button.dataset.state = "copied";
            button.textContent = "Đã sao chép";
            setTimeout(reset, 2000);
        });
    });
});
