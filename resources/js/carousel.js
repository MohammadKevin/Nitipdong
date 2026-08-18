// Smooth Horizontal Carousel
export function initCarousel() {
    const carousels = document.querySelectorAll('[data-carousel]');

    carousels.forEach(carousel => {
        const container = carousel.querySelector('[data-carousel-container]');
        const prevBtn = carousel.querySelector('[data-carousel-prev]');
        const nextBtn = carousel.querySelector('[data-carousel-next]');

        if (!container) return;

        // Smooth scroll function
        const smoothScroll = (element, target, duration = 600) => {
            const start = element.scrollLeft;
            const change = target - start;
            const startTime = performance.now();

            const easeInOutCubic = t => t < 0.5
                ? 4 * t * t * t
                : (t - 1) * (2 * t - 2) * (2 * t - 2) + 1;

            const animateScroll = (currentTime) => {
                const elapsed = currentTime - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const eased = easeInOutCubic(progress);

                element.scrollLeft = start + change * eased;

                if (progress < 1) {
                    requestAnimationFrame(animateScroll);
                }
            };

            requestAnimationFrame(animateScroll);
        };

        // Get scroll amount (one item width + gap)
        const getScrollAmount = () => {
            const firstItem = container.querySelector('.product-card');
            if (!firstItem) return 300;
            const itemWidth = firstItem.offsetWidth;
            const gap = parseInt(getComputedStyle(container).gap) || 16;
            return itemWidth + gap;
        };

        // Update button visibility
        const updateButtons = () => {
            if (!prevBtn || !nextBtn) return;

            const isAtStart = container.scrollLeft <= 10;
            const isAtEnd = container.scrollLeft >= container.scrollWidth - container.clientWidth - 10;

            prevBtn.style.opacity = isAtStart ? '0' : '1';
            prevBtn.style.pointerEvents = isAtStart ? 'none' : 'auto';
            nextBtn.style.opacity = isAtEnd ? '0' : '1';
            nextBtn.style.pointerEvents = isAtEnd ? 'none' : 'auto';
        };

        // Scroll previous
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                const scrollAmount = getScrollAmount();
                const targetScroll = Math.max(0, container.scrollLeft - scrollAmount * 2);
                smoothScroll(container, targetScroll);
            });
        }

        // Scroll next
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const scrollAmount = getScrollAmount();
                const maxScroll = container.scrollWidth - container.clientWidth;
                const targetScroll = Math.min(maxScroll, container.scrollLeft + scrollAmount * 2);
                smoothScroll(container, targetScroll);
            });
        }

        // Update on scroll
        container.addEventListener('scroll', updateButtons);

        // Initial update
        updateButtons();

        // Update on resize
        window.addEventListener('resize', updateButtons);

        // Touch swipe support
        let isDown = false;
        let startX;
        let scrollLeft;

        container.addEventListener('mousedown', (e) => {
            if (e.target.closest('a') || e.target.closest('button')) return;
            isDown = true;
            container.style.cursor = 'grabbing';
            startX = e.pageX - container.offsetLeft;
            scrollLeft = container.scrollLeft;
        });

        container.addEventListener('mouseleave', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });

        container.addEventListener('mouseup', () => {
            isDown = false;
            container.style.cursor = 'grab';
        });

        container.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - container.offsetLeft;
            const walk = (x - startX) * 2;
            container.scrollLeft = scrollLeft - walk;
        });
    });
}

// Auto-init on DOM load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCarousel);
} else {
    initCarousel();
}
