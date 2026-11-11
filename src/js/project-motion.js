/**
 * Project page motion helpers.
 *
 * - isStatic(): true for prefers-reduced-motion or a `?static` query (used for design capture).
 *   Switches off the hero push-in, the pinned sketchbook strip (falls back to native scroll), the compare
 *   sweep, parallax and autoplaying loops; `html.is-static` mirrors it for CSS.
 * - Reel arrows: scroll the "on the floor" row by 60% of its width.
 * - Cinematic image: light scrubbed parallax (the image is 120% tall inside a clipped box).
 */
export const isStatic = () => /[?&]static/.test(window.location.search) || window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export function initProjectMotion() {
	if (isStatic()) document.documentElement.classList.add('is-static');

	document.querySelectorAll('[data-reel-row]').forEach((row) => {
		const root = row.closest('.floor');
		root?.querySelectorAll('[data-reel]').forEach((button) => {
			button.addEventListener('click', () => {
				row.scrollBy({ left: Number(button.dataset.reel) * row.clientWidth * 0.6, behavior: isStatic() ? 'auto' : 'smooth' });
			});
		});
	});

	if (isStatic()) return;
	const cines = [...document.querySelectorAll('[data-cine]')];
	if (!cines.length) return;
	let ticking = false;
	const update = () => {
		const vh = window.innerHeight;
		cines.forEach((cine) => {
			const rect = cine.getBoundingClientRect();
			if (rect.bottom < 0 || rect.top > vh) return;
			const progress = (vh - rect.top) / (vh + rect.height);
			const img = cine.querySelector('[data-cine-img]');
			if (img) img.style.transform = `translate3d(0, ${((progress - 0.5) * 10).toFixed(2)}%, 0)`;
		});
		ticking = false;
	};
	window.addEventListener(
		'scroll',
		() => {
			if (!ticking) {
				ticking = true;
				window.requestAnimationFrame(update);
			}
		},
		{ passive: true }
	);
	update();
}
