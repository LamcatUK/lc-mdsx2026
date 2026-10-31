/**
 * Hero reel: crossfades .hero-reel__slide figures every 4.2s. Respects
 * prefers-reduced-motion and offers a pause control (WCAG 2.2.2).
 */
export function initHeroReel() {
	document.querySelectorAll('.hero-reel').forEach((hero) => {
		const slides = hero.querySelectorAll('.hero-reel__slide');
		if (slides.length < 2) return;

		const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
		const button = hero.querySelector('[data-reel-pause]');
		let index = 0;
		let paused = reduced.matches;
		let timer = null;

		const show = (next) => {
			slides[index].classList.remove('is-active');
			index = next % slides.length;
			slides[index].classList.add('is-active');
		};
		const start = () => {
			stop();
			timer = window.setInterval(() => show(index + 1), 4200);
		};
		const stop = () => window.clearInterval(timer);
		const sync = () => {
			if (button) button.setAttribute('aria-pressed', String(paused));
			paused ? stop() : start();
		};

		button?.addEventListener('click', () => {
			paused = !paused;
			sync();
		});
		document.addEventListener('visibilitychange', () => (document.hidden ? stop() : !paused && start()));
		sync();
	});
}
