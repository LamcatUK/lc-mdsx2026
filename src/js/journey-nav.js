/**
 * Journey nav: marks the active stage, marks earlier ones done, and fills the progress bar across the whole
 * journey (first stage's top to last stage's bottom).
 */
export function initJourneyNav() {
	const nav = document.querySelector('[data-journey-nav]');
	const stages = [...document.querySelectorAll('[data-stage]')];
	if (!nav || !stages.length) return;
	const links = [...nav.querySelectorAll('[data-journey-link]')];
	const bar = nav.querySelector('[data-journey-bar]');
	let ticking = false;

	const update = () => {
		ticking = false;
		const y = window.scrollY;
		const line = window.innerHeight * 0.35;
		let active = -1;
		stages.forEach((stage, i) => {
			if (stage.getBoundingClientRect().top <= line) active = i;
		});
		links.forEach((link, i) => {
			link.classList.toggle('on', i === active);
			link.classList.toggle('done', i < active);
			if (i === active) link.setAttribute('aria-current', 'step');
			else link.removeAttribute('aria-current');
		});
		const first = stages[0].getBoundingClientRect().top + y;
		const last = stages[stages.length - 1];
		const end = last.getBoundingClientRect().bottom + y - window.innerHeight;
		const progress = end > first ? Math.max(0, Math.min(1, (y - first + line) / (end - first + line))) : 0;
		if (bar) bar.style.width = `${(progress * 100).toFixed(1)}%`;

		// keep the active step in view in the horizontally scrolling mobile bar
		const current = links[active];
		if (current && current.offsetParent && nav.scrollWidth > nav.clientWidth) current.scrollIntoView({ inline: 'nearest', block: 'nearest' });
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
