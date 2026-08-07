import type { Directive } from 'vue';

let observer: IntersectionObserver | null = null;

function getObserver(): IntersectionObserver | null {
    if (typeof IntersectionObserver === 'undefined') {
        return null;
    }
    if (!observer) {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-revealed');
                        observer?.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.15, rootMargin: '0px 0px -10% 0px' },
        );
    }
    return observer;
}

// Fade + blur reveal used across frontend sections. Bind a delay in ms for
// staggered lists: `v-reveal="Math.min(i, 6) * 70"`.
//
// `data-mv-reveal` is written via `getSSRProps` too, so the server-rendered
// HTML already carries the hidden/blurred state — otherwise SSR output would
// paint sections fully visible for a frame before hydration hides them again.
export const reveal: Directive<HTMLElement, number | undefined> = {
    getSSRProps(binding) {
        return {
            'data-mv-reveal': '',
            style: binding.value
                ? { '--mv-reveal-delay': `${binding.value}ms` }
                : undefined,
        };
    },
    mounted(el, binding) {
        el.setAttribute('data-mv-reveal', '');
        if (binding.value) {
            el.style.setProperty('--mv-reveal-delay', `${binding.value}ms`);
        }

        const io = getObserver();
        if (!io) {
            el.classList.add('is-revealed');
            return;
        }
        io.observe(el);
    },
    unmounted(el) {
        observer?.unobserve(el);
    },
};
