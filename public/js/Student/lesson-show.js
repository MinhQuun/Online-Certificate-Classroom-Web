document.addEventListener('DOMContentLoaded', () => {
    initCourseAccordions();
    initLessonVideoProgress();
});

function initCourseAccordions() {
    const accordions = document.querySelectorAll('.accordion');

    accordions.forEach((accordion) => {
        const toggle = accordion.querySelector('.module__toggle');
        const panel = accordion.querySelector('.module__panel');

        if (!toggle || !panel) {
            return;
        }

        accordion.setAttribute('aria-expanded', 'false');
        panel.style.maxHeight = '0';

        toggle.addEventListener('click', (event) => {
            event.preventDefault();

            const isExpanded = accordion.getAttribute('aria-expanded') === 'true';

            accordions.forEach((other) => {
                if (other === accordion) {
                    return;
                }
                const otherPanel = other.querySelector('.module__panel');
                other.setAttribute('aria-expanded', 'false');
                if (otherPanel) {
                    otherPanel.style.maxHeight = '0';
                }
            });

            accordion.setAttribute('aria-expanded', String(!isExpanded));
            if (!isExpanded) {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            } else {
                panel.style.maxHeight = '0';
            }
        });

        const isActive = accordion.querySelector('.lesson-list li.is-active');
        if (isActive) {
            accordion.setAttribute('aria-expanded', 'true');
            setTimeout(() => {
                panel.style.maxHeight = panel.scrollHeight + 'px';
            }, 100);
        }
    });

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            accordions.forEach((accordion) => {
                const isExpanded = accordion.getAttribute('aria-expanded') === 'true';
                if (!isExpanded) {
                    return;
                }
                const panel = accordion.querySelector('.module__panel');
                if (panel) {
                    panel.style.maxHeight = panel.scrollHeight + 'px';
                }
            });
        }, 250);
    });
}

function initLessonVideoProgress() {
    const video = document.querySelector('[data-lesson-video]');
    const config = window.lessonProgressConfig;

    if (!video || !config) {
        return;
    }

    if (video.dataset.progressEnabled !== '1') {
        return;
    }

    const warningNode = document.querySelector('[data-progress-warning]');
    const handler = new LessonVideoProgress(video, warningNode, config);
    handler.init();
}

class LessonVideoProgress {
    constructor(video, warningNode, config) {
        this.video = video;
        this.warningNode = warningNode;
        this.config = config;

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

        this.video.addEventListener('loadedmetadata', this.onLoadedMetadata);
        this.video.addEventListener('play', this.onPlay);
        this.video.addEventListener('pause', this.onPause);
        this.video.addEventListener('ended', this.onEnded);
        this.video.addEventListener('timeupdate', this.onTimeUpdate);
        this.video.addEventListener('seeking', this.onSeeking);
        this.video.addEventListener('seeked', this.onSeeked);
        this.video.addEventListener('ratechange', this.onRateChange);
        document.addEventListener('visibilitychange', this.onVisibilityChange);
        window.addEventListener('beforeunload', this.onBeforeUnload);

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

        this.furthestTime = Math.max(this.furthestTime, this.video.currentTime || 0);
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
        const limit = this.furthestTime + this.seekLeeway;

        if (target > limit) {
            const safeTarget = Math.min(limit, this.getDuration());
            if (!Number.isNaN(safeTarget) && safeTarget >= 0) {
                this.video.currentTime = safeTarget;
            }
            this.showWarning('Không thể tua quá nhanh, Vui lòng xem lần lượt!.');
        }

        this.lastTimeUpdate = this.video.currentTime || 0;
    }

    onRateChange() {
        if (this.video.playbackRate > 1.25) {
            this.video.playbackRate = 1.25;
            this.showWarning('Tốc độ phát chỉ tối đa 1.25x.');
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
            event: 'start',
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
                this.showWarning('Không ghi nhận được tiến độ, vui lòng kiểm tra kết nối.');
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
            event: 'progress',
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
            console.warn('Failed to record lesson progress', error);
        });
    }

    sendPayload(payload, useBeacon = false) {
        const requestPayload = { ...payload, _token: this.config.csrfToken };

        if (useBeacon && navigator.sendBeacon) {
            try {
                const blob = new Blob([JSON.stringify(requestPayload)], { type: 'application/json' });
                navigator.sendBeacon(this.config.progressUrl, blob);
                return Promise.resolve(true);
            } catch (error) {
                // Fallback to fetch below.
            }
        }

        return fetch(this.config.progressUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': this.config.csrfToken,
                Accept: 'application/json',
            },
            credentials: 'same-origin',
            body: JSON.stringify(requestPayload),
        }).then((response) => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return true;
        });
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
        this.warningNode.classList.add('is-visible');

        if (this.warningTimer) {
            clearTimeout(this.warningTimer);
        }
        this.warningTimer = setTimeout(() => {
            this.warningNode.classList.remove('is-visible');
            this.warningNode.hidden = true;
        }, 3000);
    }
}
