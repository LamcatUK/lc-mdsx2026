/**
 * Hero reel: crossfades .hero-reel__slide figures and keeps the "now showing"
 * card (title, meta, link, progress bar) in step with the current slide.
 * Timing (hold + fade = 5.4s) matches --reel-ms in hero-reel.css, which drives
 * the progress bar and zoom animations. Respects prefers-reduced-motion and
 * offers a pause control (WCAG 2.2.2).
 */
const SLIDE_MS = 5400;

export function initHeroReel() {
	document.querySelectorAll('.hero-reel').forEach((hero) => {
		const slides = hero.querySelectorAll('.hero-reel__slide');
		if (slides.length < 2) return;

		const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
		const button = hero.querySelector('[data-reel-pause]');
		const card = hero.querySelector('[data-reel-card]');
		const cardTitle = card?.querySelector('[data-card-title]');
		const cardMeta = card?.querySelector('[data-card-meta]');
		const cardBar = card?.querySelector('[data-card-bar]');
		let index = 0;
		let paused = reduced.matches;
		let timer = null;

		const restartBar = () => {
			if (!cardBar) return;
			cardBar.style.animation = 'none';
			void cardBar.offsetWidth; // reflow so the animation restarts
			cardBar.style.animation = '';
		};
		const show = (next) => {
			slides[index].classList.remove('is-active');
			index = next % slides.length;
			const slide = slides[index];
			slide.classList.add('is-active');
			if (card) {
				cardTitle.textContent = slide.dataset.title;
				cardTitle.href = slide.dataset.url;
				cardMeta.textContent = slide.dataset.meta;
				restartBar();
			}
		};
		const stop = () => window.clearInterval(timer);
		const start = () => {
			stop();
			timer = window.setInterval(() => show(index + 1), SLIDE_MS);
		};
		const sync = () => {
			hero.classList.toggle('is-paused', paused);
			if (button) button.setAttribute('aria-pressed', String(paused));
			if (paused) stop();
			else {
				restartBar();
				start();
			}
		};

		button?.addEventListener('click', () => {
			paused = !paused;
			sync();
		});
		document.addEventListener('visibilitychange', () => {
			if (document.hidden) stop();
			else if (!paused) {
				restartBar();
				start();
			}
		});
		sync();
	});
}
