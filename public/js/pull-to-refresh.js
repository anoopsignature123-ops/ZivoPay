/**
 * ZIVO PAY - Smart Universal Loader, Pull-To-Refresh Engine & Form Blur System
 */
(function () {
    'use strict';

    // 1. Fullscreen ZIVO Emerald Page Loader Controller
    function setupGlobalLoader() {
        if (!document.getElementById('globalPageLoader')) {
            const loaderOverlay = document.createElement('div');
            loaderOverlay.id = 'globalPageLoader';
            loaderOverlay.className = 'loader-hidden';
            loaderOverlay.innerHTML = `
                <div class="zivo-spinner-container">
                    <div class="zivo-spinner-outer"></div>
                    <div class="zivo-spinner-inner"></div>
                    <img src="/assets/images/logo.png" alt="ZIVO PAY" class="zivo-loader-logo" onerror="this.style.display='none'">
                </div>
                <div class="zivo-loader-text">ZIVO PAY</div>
                <div class="zivo-loader-sub">Loading Financial Ecosystem...</div>
            `;
            document.body.appendChild(loaderOverlay);
        }

        const loader = document.getElementById('globalPageLoader');

        function hideLoader() {
            if (loader) {
                loader.classList.add('loader-hidden');
            }
        }

        function showLoader(message) {
            if (loader) {
                if (message) {
                    const subText = loader.querySelector('.zivo-loader-sub');
                    if (subText) subText.textContent = message;
                }
                loader.classList.remove('loader-hidden');
            }
        }

        // Fast & smooth hide on DOM ready
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(hideLoader, 150);
        } else {
            window.addEventListener('DOMContentLoaded', function () {
                setTimeout(hideLoader, 150);
            });
        }

        window.addEventListener('load', hideLoader);

        // Expose loader functions globally
        window.showZivoLoader = showLoader;
        window.hideZivoLoader = hideLoader;
    }

    // 2. Pull-To-Refresh Spring Physics Engine
    function setupPullToRefresh() {
        if (!document.body) return;

        if (!document.getElementById('pullToRefreshIndicator')) {
            const pullPill = document.createElement('div');
            pullPill.id = 'pullToRefreshIndicator';
            pullPill.innerHTML = `
                <div class="pull-icon-container">
                    <div class="pull-spinner-ring"></div>
                </div>
                <span class="pull-text">Pull down to refresh</span>
            `;
            document.body.appendChild(pullPill);
        }

        const indicator = document.getElementById('pullToRefreshIndicator');
        const iconContainer = indicator ? indicator.querySelector('.pull-icon-container') : null;
        const pullText = indicator ? indicator.querySelector('.pull-text') : null;

        let startY = 0;
        let currentY = 0;
        let isPulling = false;
        let isRefreshing = false;
        const PULL_THRESHOLD = 80;

        function getScrollTop() {
            return window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
        }

        window.addEventListener('touchstart', function (e) {
            if (getScrollTop() === 0 && !isRefreshing && e.touches && e.touches.length === 1) {
                startY = e.touches[0].screenY;
                isPulling = true;
            }
        }, { passive: true });

        window.addEventListener('touchmove', function (e) {
            if (!isPulling || isRefreshing || !e.touches || e.touches.length !== 1) return;

            const y = e.touches[0].screenY;
            const diff = y - startY;

            if (diff > 0 && getScrollTop() === 0) {
                const dampening = 0.45;
                currentY = Math.min(diff * dampening, 120);

                if (currentY > 10 && indicator) {
                    indicator.classList.add('pull-visible');
                    const progress = Math.min(currentY / PULL_THRESHOLD, 1);
                    if (iconContainer) {
                        iconContainer.style.transform = `rotate(${progress * 360}deg)`;
                    }

                    if (pullText) {
                        if (currentY >= PULL_THRESHOLD) {
                            pullText.textContent = 'Release to refresh';
                        } else {
                            pullText.textContent = 'Pull down to refresh';
                        }
                    }
                }
            } else if (indicator) {
                indicator.classList.remove('pull-visible');
            }
        }, { passive: true });

        window.addEventListener('touchend', function () {
            if (!isPulling) return;
            isPulling = false;

            if (currentY >= PULL_THRESHOLD && !isRefreshing) {
                triggerRefresh();
            } else {
                resetPull();
            }
        });

        // Mouse Pull Support
        let isMouseDown = false;
        window.addEventListener('mousedown', function (e) {
            if (getScrollTop() === 0 && !isRefreshing && e.clientY < 80) {
                startY = e.clientY;
                isMouseDown = true;
            }
        });

        window.addEventListener('mousemove', function (e) {
            if (!isMouseDown || isRefreshing) return;

            const diff = e.clientY - startY;
            if (diff > 0 && getScrollTop() === 0) {
                currentY = Math.min(diff * 0.45, 120);

                if (currentY > 10 && indicator) {
                    indicator.classList.add('pull-visible');
                    const progress = Math.min(currentY / PULL_THRESHOLD, 1);
                    if (iconContainer) {
                        iconContainer.style.transform = `rotate(${progress * 360}deg)`;
                    }

                    if (pullText) {
                        if (currentY >= PULL_THRESHOLD) {
                            pullText.textContent = 'Release to refresh';
                        } else {
                            pullText.textContent = 'Pull down to refresh';
                        }
                    }
                }
            }
        });

        window.addEventListener('mouseup', function () {
            if (!isMouseDown) return;
            isMouseDown = false;

            if (currentY >= PULL_THRESHOLD && !isRefreshing) {
                triggerRefresh();
            } else {
                resetPull();
            }
        });

        function triggerRefresh() {
            isRefreshing = true;
            if (indicator) indicator.classList.add('pull-visible');
            if (pullText) pullText.textContent = 'Refreshing...';
            if (window.showZivoLoader) {
                window.showZivoLoader('Refreshing Data...');
            }

            setTimeout(function () {
                window.location.reload();
            }, 350);
        }

        function resetPull() {
            currentY = 0;
            if (indicator) indicator.classList.remove('pull-visible');
            if (pullText) pullText.textContent = 'Pull down to refresh';
            if (iconContainer) iconContainer.style.transform = 'rotate(0deg)';
        }
    }

    // 3. Smart Conditional Loaders, Ripple Wave & Form Glass Blur Controller
    function setupSmartLoadersAndActions() {
        // A. Smart Link Navigation Handler
        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[href]');
            if (!link) return;

            const href = link.getAttribute('href');
            const target = link.getAttribute('target');

            // Skip dummy links, javascript triggers, anchor links, or new tab links
            if (!href || href === '#' || href.startsWith('javascript:') || href.startsWith('#') || target === '_blank') {
                return;
            }

            if (link.getAttribute('data-no-loader') === 'true') {
                return;
            }

            if (window.showZivoLoader) {
                window.showZivoLoader('Loading Page...');
            }
        });

        // B. Smart Button Click Handler
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('button, .btn, .poster-btn-action-view, .poster-btn-action-edit, .poster-btn-action-login, input[type="submit"]');
            if (!btn) return;

            const btnType = btn.getAttribute('type') || 'button';
            const isToggleAction = btnType === 'button' && (btn.getAttribute('onclick') || btn.classList.contains('js-sidebar-toggle') || btn.classList.contains('js-mobile-menu-toggle'));

            // 1. Always play clean Ripple Wave effect on button click
            const rect = btn.getBoundingClientRect();
            const ripple = document.createElement('span');
            ripple.className = 'btn-ripple-wave';
            const size = Math.max(rect.width, rect.height);
            ripple.style.width = ripple.style.height = `${size}px`;
            ripple.style.left = `${e.clientX - rect.left - size / 2}px`;
            ripple.style.top = `${e.clientY - rect.top - size / 2}px`;
            btn.appendChild(ripple);
            setTimeout(() => ripple.remove(), 500);

            // 2. If it's a toggle button (like password eye toggle), do NOT blur form or show loader
            if (isToggleAction) {
                return;
            }

            // 3. Handle Submit Buttons & Action Loading Buttons
            if (btnType === 'submit' || btn.getAttribute('data-action') === 'load') {
                const form = btn.closest('form');
                const card = btn.closest('.bg-panel, .poster-clean-table, .gold-card-animated');

                if (form) {
                    form.classList.add('form-blur-loading');
                } else if (card) {
                    card.classList.add('card-blur-loading');
                }

                if (!btn.querySelector('.btn-spinner')) {
                    const spinner = document.createElement('span');
                    spinner.className = 'btn-spinner';
                    btn.prepend(spinner);
                }

                btn.style.pointerEvents = 'none';

                if (window.showZivoLoader && (btnType === 'submit' || form)) {
                    window.showZivoLoader('Processing Action...');
                }

                // Safety timeout reset
                setTimeout(() => {
                    if (form) form.classList.remove('form-blur-loading');
                    if (card) card.classList.remove('card-blur-loading');
                    const sp = btn.querySelector('.btn-spinner');
                    if (sp) sp.remove();
                    btn.style.pointerEvents = '';
                }, 3000);
            }
        });

        // C. Smart Form Submit Handler
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (form && !form.getAttribute('data-no-loader')) {
                form.classList.add('form-blur-loading');
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.querySelector('.btn-spinner')) {
                    const spinner = document.createElement('span');
                    spinner.className = 'btn-spinner';
                    submitBtn.prepend(spinner);
                    submitBtn.style.pointerEvents = 'none';
                }
                if (window.showZivoLoader) {
                    window.showZivoLoader('Submitting Request...');
                }
            }
        });
    }

    // Initialize System
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            setupGlobalLoader();
            setupPullToRefresh();
            setupSmartLoadersAndActions();
        });
    } else {
        setupGlobalLoader();
        setupPullToRefresh();
        setupSmartLoadersAndActions();
    }
})();
