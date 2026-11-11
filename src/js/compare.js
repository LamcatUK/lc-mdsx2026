import { isStatic } from './project-motion';

/**
 * Compare slider (idea to reality): pointer drag anywhere on the image, arrow keys (5% steps) on the grip.
 * The first time it scrolls into view it sweeps once from 85% to 50% so people see it moves.
 */
export function initCompare() {
	document.querySelectorAll('[data-compare]').forEach((box) => {
		const grip = box.querySelector('[data-compare-grip]');
		let pos = 50;
		let dragging = false;
		let touched = false;

		const set = (value) => {
			pos = Math.max(0, Math.min(100, value));
			box.style.setProperty('--pos', `${pos}%`);
			grip?.setAttribute('aria-valuenow', String(Math.round(pos)));
		};
		const fromPointer = (event) => {
			const rect = box.getBoundingClientRect();
			set(((event.clientX - rect.left) / rect.width) * 100);
		};

		box.addEventListener('pointerdown', (event) => {
			dragging = touched = true;
			box.setPointerCapture(event.pointerId);
			fromPointer(event);
		});
		box.addEventListener('pointermove', (event) => dragging && fromPointer(event));
		box.addEventListener('pointerup', () => (dragging = false));
		box.addEventListener('pointercancel', () => (dragging = false));
		grip?.addEventListener('keydown', (event) => {
			if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
				touched = true;
				set(pos + (event.key === 'ArrowLeft' ? -5 : 5));
				event.preventDefault();
			}
		});

		if (isStatic() || !('IntersectionObserver' in window)) return;
		const sweep = new IntersectionObserver(
			(entries) => {
				if (!entries[0].isIntersecting) return;
				sweep.disconnect();
				const start = performance.now();
				const step = (now) => {
					if (touched) return;
					const t = Math.min(1, (now - start) / 1400);
					const eased = t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
					set(85 - 35 * eased);
					if (t < 1) window.requestAnimationFrame(step);
				};
				set(85);
				window.requestAnimationFrame(step);
			},
			{ rootMargin: '0px 0px -30% 0px' }
		);
		sweep.observe(box);
	});
}
