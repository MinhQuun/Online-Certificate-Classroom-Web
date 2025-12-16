document.addEventListener("DOMContentLoaded", () => {
    initCourseAccordions();
    const videoHandler = initLessonVideoProgress();
    initLessonDemoPass(videoHandler);
});

function initCourseAccordions() {
    const accordions = document.querySelectorAll(".accordion");

    accordions.forEach((accordion) => {
        const toggle = accordion.querySelector(".module__toggle");
        const panel = accordion.querySelector(".module__panel");

        if (!toggle || !panel) {
            return;
        }

        accordion.setAttribute("aria-expanded", "false");
        panel.style.maxHeight = "0";

        toggle.addEventListener("click", (event) => {
            event.preventDefault();

            const isExpanded =
                accordion.getAttribute("aria-expanded") === "true";

            accordions.forEach((other) => {
                if (other === accordion) {
                    return;
                }
                const otherPanel = other.querySelector(".module__panel");
                other.setAttribute("aria-expanded", "false");
                if (otherPanel) {
                    otherPanel.style.maxHeight = "0";
                }
            });

            accordion.setAttribute("aria-expanded", String(!isExpanded));
            if (!isExpanded) {
                panel.style.maxHeight = panel.scrollHeight + "px";
            } else {
                panel.style.maxHeight = "0";
            }
        });

        const isActive = accordion.querySelector(".lesson-list li.is-active");
        if (isActive) {
            accordion.setAttribute("aria-expanded", "true");
            setTimeout(() => {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }, 100);
        }
    });

    let resizeTimer;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            accordions.forEach((accordion) => {
                const isExpanded =
                    accordion.getAttribute("aria-expanded") === "true";
                if (!isExpanded) {
                    return;
                }
                const panel = accordion.querySelector(".module__panel");
                if (panel) {
                    panel.style.maxHeight = panel.scrollHeight + "px";
                }
            });
        }, 250);
    });
}

function initLessonVideoProgress() {
    const video = document.querySelector("[data-lesson-video]");
    const config = window.lessonProgressConfig;

    if (!video || !config) {
        return null;
    }

    if (video.dataset.progressEnabled !== "1") {
        return null;
    }

    const warningNode = document.querySelector("[data-progress-warning]");
    const handler = new LessonVideoProgress(video, warningNode, config);
    handler.init();
    return handler;
}

function initLessonDemoPass(videoHandler) {
    const video = document.querySelector("[data-lesson-video]");
    const config = window.lessonProgressConfig;
    const container = document.querySelector("[data-demo-pass]");

    if (!video || !config || !container) {
        return null;
    }

    const demoConfig = config.demoPass;
    if (!demoConfig || demoConfig.enabled !== true) {
        return null;
    }

    const handler = new LessonDemoPass(
        video,
        container,
        demoConfig,
        config,
        videoHandler
    );
    handler.init();
    return handler;
}

class LessonVideoProgress {
    constructor(video, warningNode, config) {
        this.video = video;
        this.warningNode = warningNode;
        this.config = config;

        this.hasCompletedBefore = Boolean(config.isCompleted);
        this.allowSeekAfterComplete = config.allowSeekAfterComplete !== false;
        this.watchCount = Number(config.watchCount ?? 0);
        this.allowUnrestrictedSeek =
            this.allowSeekAfterComplete && this.hasCompletedBefore === true;
        this.sessionStarted = false;
        this.sessionStartPending = false;
        this.isSeeking = false;
        this.seekLeeway = Number(config.maxSeekAheadSeconds ?? 12);
        if (!Number.isFinite(this.seekLeeway) || this.seekLeeway < 0) {
            this.seekLeeway = 12;
        }

        this.furthestTime = Math.max(0, Number(config.resumeSeconds ?? 0));
        this.duration = Math.max(0, Number(config.durationSeconds ?? 0));

        this.watchAccumulator = 0;
        this.lastTimeUpdate = null;
        this.lastSentAt = 0;
        this.warningTimer = null;
        this.lastWarningAt = 0;
    }

    init() {
        this.onLoadedMetadata = this.onLoadedMetadata.bind(this);
        this.onPlay = this.onPlay.bind(this);
        this.onPause = this.onPause.bind(this);
        this.onEnded = this.onEnded.bind(this);
        this.onTimeUpdate = this.onTimeUpdate.bind(this);
        this.onSeeking = this.onSeeking.bind(this);
        this.onSeeked = this.onSeeked.bind(this);
        this.onRateChange = this.onRateChange.bind(this);
        this.onVisibilityChange = this.onVisibilityChange.bind(this);
        this.onBeforeUnload = this.onBeforeUnload.bind(this);

        this.video.addEventListener("loadedmetadata", this.onLoadedMetadata);
        this.video.addEventListener("play", this.onPlay);
        this.video.addEventListener("pause", this.onPause);
        this.video.addEventListener("ended", this.onEnded);
        this.video.addEventListener("timeupdate", this.onTimeUpdate);
        this.video.addEventListener("seeking", this.onSeeking);
        this.video.addEventListener("seeked", this.onSeeked);
        this.video.addEventListener("ratechange", this.onRateChange);
        document.addEventListener("visibilitychange", this.onVisibilityChange);
        window.addEventListener("beforeunload", this.onBeforeUnload);

        if (this.video.readyState >= 1) {
            this.onLoadedMetadata();
        }
    }

    onLoadedMetadata() {
        if (Number.isFinite(this.video.duration) && this.video.duration > 0) {
            this.duration = Math.floor(this.video.duration);
        }

        if (this.furthestTime > 0 && this.duration > 0) {
            const maxResume = Math.max(this.duration - 1, 0);
            const resumeTarget = Math.min(this.furthestTime, maxResume);
            if (resumeTarget > 2 && !Number.isNaN(resumeTarget)) {
                this.video.currentTime = resumeTarget;
            }
        }

        this.furthestTime = Math.max(
            this.furthestTime,
            this.video.currentTime || 0
        );
        this.lastTimeUpdate = this.video.currentTime || 0;
    }

    onPlay() {
        this.ensureSessionStarted();
        this.lastTimeUpdate = this.video.currentTime || 0;
    }

    onPause() {
        this.flushProgress();
    }

    onEnded() {
        this.furthestTime = this.getDuration();
        this.hasCompletedBefore = true;
        if (this.allowSeekAfterComplete) {
            this.allowUnrestrictedSeek = true;
        }
        this.flushProgress({ completed: true, force: true, useBeacon: true });
    }

    onTimeUpdate() {
        if (this.video.paused || this.isSeeking) {
            return;
        }

        const current = this.video.currentTime || 0;
        if (this.lastTimeUpdate !== null) {
            const delta = current - this.lastTimeUpdate;
            if (delta > 0 && delta < 5) {
                this.watchAccumulator += delta;
            }
        }
        this.lastTimeUpdate = current;
        this.furthestTime = Math.max(this.furthestTime, current);

        const elapsedSinceSend = Date.now() - this.lastSentAt;
        if (this.watchAccumulator >= 15 || elapsedSinceSend > 15000) {
            this.flushProgress();
        }
    }

    onSeeking() {
        this.isSeeking = true;
    }

    onSeeked() {
        this.isSeeking = false;
        const target = this.video.currentTime || 0;

        if (this.allowUnrestrictedSeek) {
            this.lastTimeUpdate = target;
            this.furthestTime = Math.max(this.furthestTime, target);
            return;
        }

        const limit = this.furthestTime + this.seekLeeway;

        if (target > limit) {
            const safeTarget = Math.min(limit, this.getDuration());
            if (!Number.isNaN(safeTarget) && safeTarget >= 0) {
                this.video.currentTime = safeTarget;
            }
            this.showWarning("Không thể tua quá nhanh, vui lòng xem lần lượt.");
        }

        this.lastTimeUpdate = this.video.currentTime || 0;
    }

    onRateChange() {
        if (this.video.playbackRate > 1.25) {
            this.video.playbackRate = 1.25;
            this.showWarning("Tốc độ phát chỉ tối đa 1.25x.");
        }
    }

    onVisibilityChange() {
        if (document.hidden) {
            this.flushProgress({ useBeacon: true });
        }
    }

    onBeforeUnload() {
        this.flushProgress({ force: true, useBeacon: true });
    }

    ensureSessionStarted() {
        if (this.sessionStarted || this.sessionStartPending) {
            return;
        }

        this.sessionStartPending = true;
        const payload = {
            event: "start",
            current_time: Math.floor(this.video.currentTime || 0),
            duration: this.getDuration(),
            watched_delta: 0,
            completed: false,
        };

        this.sendPayload(payload)
            .then(() => {
                this.sessionStarted = true;
            })
            .catch(() => {
                this.showWarning(
                    "Không ghi nhận được tiến độ, vui lòng kiểm tra kết nối."
                );
            })
            .finally(() => {
                this.sessionStartPending = false;
            });
    }

    flushProgress(options = {}) {
        const { force = false, completed = false, useBeacon = false } = options;

        this.ensureSessionStarted();

        const integerDelta = Math.floor(this.watchAccumulator);
        if (!force && integerDelta === 0 && !completed) {
            return;
        }

        const payload = {
            event: "progress",
            current_time: Math.floor(this.video.currentTime || 0),
            duration: this.getDuration(),
            watched_delta: Math.max(0, integerDelta),
            completed: completed || this.video.ended,
        };

        this.watchAccumulator -= integerDelta;
        if (this.watchAccumulator < 0) {
            this.watchAccumulator = 0;
        }

        this.lastSentAt = Date.now();

        this.sendPayload(payload, useBeacon).catch((error) => {
            console.warn("Failed to record lesson progress", error);
        });
    }

    sendPayload(payload, useBeacon = false) {
        const requestPayload = { ...payload, _token: this.config.csrfToken };

        if (useBeacon && navigator.sendBeacon) {
            try {
                const blob = new Blob([JSON.stringify(requestPayload)], {
                    type: "application/json",
                });
                navigator.sendBeacon(this.config.progressUrl, blob);
                return Promise.resolve(true);
            } catch (error) {
                // Fallback to fetch below.
            }
        }

        return fetch(this.config.progressUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.config.csrfToken,
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify(requestPayload),
        }).then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return true;
        });
    }

    markCompletedByDemo(durationSeconds = null) {
        if (Number.isFinite(durationSeconds) && durationSeconds > 0) {
            const safeDuration = Math.floor(durationSeconds);
            this.duration = Math.max(this.duration, safeDuration);
            const targetTime = Math.min(this.getDuration(), safeDuration);
            if (!Number.isNaN(targetTime) && targetTime >= 0) {
                this.video.currentTime = targetTime;
            }
            this.furthestTime = Math.max(this.furthestTime, this.getDuration());
        } else {
            this.furthestTime = Math.max(this.furthestTime, this.getDuration());
        }

        this.hasCompletedBefore = true;
        if (this.allowSeekAfterComplete) {
            this.allowUnrestrictedSeek = true;
        }
        this.lastTimeUpdate = this.video.currentTime || 0;
    }

    getDuration() {
        if (this.duration > 0) {
            return this.duration;
        }

        if (Number.isFinite(this.video.duration) && this.video.duration > 0) {
            this.duration = Math.floor(this.video.duration);
        }

        return this.duration;
    }

    showWarning(message) {
        const now = Date.now();
        if (now - this.lastWarningAt < 1000) {
            return;
        }
        this.lastWarningAt = now;

        if (!this.warningNode) {
            console.warn(message);
            return;
        }

        this.warningNode.textContent = message;
        this.warningNode.hidden = false;
        this.warningNode.classList.add("is-visible");

        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
        }
        this.warningTimer = setTimeout(() => {
            this.warningNode.classList.remove("is-visible");
            this.warningNode.hidden = true;
        }, 3000);
    }
}

class LessonDemoPass {
    constructor(video, container, demoConfig, baseConfig, videoProgressHandler) {
        this.video = video;
        this.container = container;
        this.demoConfig = demoConfig;
        this.baseConfig = baseConfig;
        this.videoProgressHandler = videoProgressHandler;
        this.passButton = container.querySelector("[data-demo-pass-action]");
        this.statusNode = container.querySelector("[data-demo-pass-status]");
        this.isSubmitting = false;
        this.defaultLabel = this.passButton ? this.passButton.textContent.trim() : "Pass video";
    }

    init() {
        if (!this.passButton) {
            return;
        }
        this.setStatus(
            this.demoConfig.note ||
                "Pass video."
        );
        this.passButton.addEventListener("click", () => this.handlePass());
    }

    setStatus(message, isError = false) {
        if (!this.statusNode) {
            return;
        }
        this.statusNode.textContent = message;
        this.statusNode.classList.toggle("is-error", Boolean(isError));
    }

    setLoading(state) {
        this.isSubmitting = state;
        if (this.passButton) {
            this.passButton.disabled = state;
            this.passButton.textContent = state
                ? "Đang pass..."
                : this.defaultLabel;
        }
    }

    handlePass() {
        if (this.isSubmitting) {
            return;
        }

        const durationSeconds = Math.floor(
            this.video?.duration || this.baseConfig.durationSeconds || 0
        );
        const payload = {
            _token: this.baseConfig.csrfToken,
            pass_reason: "demo_pass_video",
        };
        if (durationSeconds > 0) {
            payload.duration_seconds = durationSeconds;
        }

        this.setLoading(true);
        this.setStatus("Đang pass video để demo tiến độ...");

        fetch(this.demoConfig.url, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": this.baseConfig.csrfToken,
                Accept: "application/json",
            },
            credentials: "same-origin",
            body: JSON.stringify(payload),
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}`);
                }
                return response.json();
            })
            .then(() => {
                this.setStatus(
                    "Đã pass video ",
                    false
                );
                if (
                    this.videoProgressHandler &&
                    typeof this.videoProgressHandler.markCompletedByDemo ===
                        "function"
                ) {
                    this.videoProgressHandler.markCompletedByDemo(
                        durationSeconds
                    );
                }
            })
            .catch((error) => {
                this.setStatus(
                    "Không pass được, vui lòng kiểm tra quyền hoặc mạng.",
                    true
                );
                console.warn("Demo pass failed", error);
            })
            .finally(() => {
                this.setLoading(false);
            });
    }
}
