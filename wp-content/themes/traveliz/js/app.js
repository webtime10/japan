// Класс Modal - только управление видимостью окна
class Modal {
    constructor(triggerSelector, modalSelector, overlaySelector) {
        this.triggerSelector = triggerSelector;
        this.modalSelector = modalSelector;
        this.overlaySelector = overlaySelector;
        this.trigger = document.querySelectorAll(triggerSelector);
        this.modal = null;
        this.overlay = null;
        this.closeButtons = document.querySelectorAll('.modal-close_vt');
        this.form = null;
        this.iframe = null;
        this.iframeSrc = '';
        this.init();
    }

    resolveTargets(triggerBtn) {
        if (triggerBtn && triggerBtn.classList.contains('modal-trigger_vt--swiss')) {
            const swissModal = document.getElementById('swiss-video-modal_vt');
            const swissOverlay = document.getElementById('swiss-video-overlay_vt');
            return {
                modal: swissModal || null,
                overlay: swissOverlay || null,
            };
        }

        return {
            modal: document.querySelector(this.modalSelector),
            overlay: document.querySelector(this.overlaySelector),
        };
    }

    bindMedia(modal) {
        this.modal = modal;
        this.form = modal ? modal.querySelector('form') : null;
        this.iframe = modal ? modal.querySelector('iframe') : null;
        this.iframeSrc = '';

        if (this.iframe) {
            const dataSrc = this.iframe.getAttribute('data-src');
            const currentSrc = this.iframe.getAttribute('src');
            this.iframeSrc = (dataSrc && dataSrc.trim())
                ? dataSrc
                : (currentSrc && currentSrc !== 'about:blank' ? currentSrc : '');
        }
    }

    init() {
        this.trigger.forEach((btn) => {
            btn.addEventListener('click', (e) => this.open(e));
        });

        document.querySelectorAll('.overlay_vt').forEach((overlay) => {
            overlay.addEventListener('click', () => {
                if (this.overlay === overlay && this.isOpen()) {
                    this.close();
                }
            });
        });

        this.closeButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal_vt');
                if (modal && this.modal === modal && this.isOpen()) {
                    this.close();
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen()) {
                this.close();
            }
        });
    }

    open(e) {
        if (e) e.preventDefault();

        const triggerBtn = e ? e.currentTarget : null;

        if (triggerBtn && triggerBtn.classList.contains('modal-trigger_vt--swiss')) {
            if (!triggerBtn.dataset.videoId) {
                return;
            }
        }

        const targets = this.resolveTargets(triggerBtn);
        if (!targets.modal) {
            return;
        }

        this.modal = targets.modal;
        this.overlay = targets.overlay;
        this.bindMedia(this.modal);

        if (this.form) {
            this.form.reset();
        }

        if (this.iframe && this.iframeSrc) {
            const current = this.iframe.getAttribute('src') || '';
            if (current === '' || current === 'about:blank') {
                this.iframe.setAttribute('src', this.iframeSrc);
            }
        }

        if (this.overlay) {
            this.overlay.style.display = 'block';
        }
        this.modal.style.display = 'block';
        document.body.classList.add('modal-open');

        setTimeout(() => {
            this.modal.style.opacity = '1';
            if (this.overlay) {
                this.overlay.style.opacity = '1';
            }
        }, 10);
    }

    close() {
        if (!this.modal) {
            return;
        }

        this.modal.style.opacity = '0';
        if (this.overlay) {
            this.overlay.style.opacity = '0';
        }

        this.stopVideo();

        setTimeout(() => {
            this.modal.style.display = 'none';
            if (this.overlay) {
                this.overlay.style.display = 'none';
            }
            document.body.classList.remove('modal-open');
        }, 300);
    }

    stopVideo() {
        if (!this.iframe) {
            return;
        }

        try {
            this.iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
            this.iframe.contentWindow.postMessage('{"method":"pause"}', '*');
        } catch (err) {}

        this.iframe.setAttribute('src', 'about:blank');
    }

    isOpen() {
        return this.modal && this.modal.style.display === 'block';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new Modal('.modal-trigger_vt', '.modal_vt', '.overlay_vt');
});
