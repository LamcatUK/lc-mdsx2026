import { isStatic } from './project-motion';

/**
 * Concept sketchbook strip. On desktop (761px up) the strip is pinned with position: sticky and
 * scrubbed sideways by the page scroll, with a "03 / 08" counter and progress track. Below that, or with
 * reduced motion, the strip stays a native scroll-snap row. No scroll library: sticky + one rAF handler
 * keeps it compatible with the Lenis smooth scroll.
 */
export function initSketchbook() {
	const tracks = [...document.querySelectorAll('[data-sketchbook]')];
	if (!tracks.length) return;
	const wide = window.matchMedia('(min-width: 761px)');

	const setup = (track) => {
		const pin = track.querySelector('.sketchbook__pin');
		const scroller = track.querySelector('[data-sketchbook-scroller]');
		const strip = track.querySelector('[data-sketchbook-strip]');
		const bar = track.querySelector('[data-sketchbook-bar]');
		const count = track.querySelector('[data-sketchbook-count]');
		const total = strip.children.length;
		let dist = 0;
		let top = 0;
		let ticking = false;

		const measure = () => {
			if (!track.classList.contains('is-pinned')) return;
			dist = Math.max(0, strip.scrollWidth - scroller.clientWidth);
			top = parseFloat(window.getComputedStyle(pin).top) || 0;
			track.style.height = `${pin.offsetHeight + dist}px`;
			update();
		};
		const update = () => {
			ticking = false;
			if (!track.classList.contains('is-pinned') || !dist) return;
			const progress = Math.max(0, Math.min(1, (top - track.getBoundingClientRect().top) / dist));
			strip.style.transform = `translate3d(${(-progress * dist).toFixed(1)}px, 0, 0)`;
			if (bar) bar.style.width = `${(100 / total + progress * (100 - 100 / total)).toFixed(1)}%`;
			if (count) count.textContent = `${String(Math.min(total, 1 + Math.round(progress * (total - 1)))).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
		};
		const onScroll = () => {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		};
		const meta = track.querySelector('.sketchbook__meta');
		const apply = () => {
			// a strip that fits the page (or just one or two sketches) is laid out as a static row: no pin, no counter
			const fits = track.classList.contains('sketchbook__track--few') || strip.scrollWidth <= scroller.clientWidth + 8;
			track.classList.toggle('is-fit', fits);
			if (meta) meta.hidden = fits;
			const on = wide.matches && !isStatic() && !fits;
			track.classList.toggle('is-pinned', on);
			if (!on) {
				track.style.height = '';
				strip.style.transform = '';
			}
			measure();
		};

		// not pinned (phone, reduced motion): the counter and progress bar follow the strip's own scrolling
		const onStripScroll = () => {
			if (track.classList.contains('is-pinned')) return;
			const max = scroller.scrollWidth - scroller.clientWidth;
			if (max <= 0) return;
			const progress = Math.max(0, Math.min(1, scroller.scrollLeft / max));
			if (bar) bar.style.width = `${(100 / total + progress * (100 - 100 / total)).toFixed(1)}%`;
			if (count) count.textContent = `${String(Math.min(total, 1 + Math.round(progress * (total - 1)))).padStart(2, '0')} / ${String(total).padStart(2, '0')}`;
		};
		scroller.addEventListener('scroll', onStripScroll, { passive: true });

		window.addEventListener('scroll', onScroll, { passive: true });
		window.addEventListener('resize', measure);
		window.addEventListener('load', measure);
		wide.addEventListener('change', apply);
		apply();
	};

	tracks.forEach(setup);
}
