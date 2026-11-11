/**
 * Global lightbox. Every img[data-lb] on the page opens it; arrows, Escape and the left / right keys work,
 * with a counter, the stage label and the alt text. Focus moves to Close on open and back to the image on close.
 *
 * Looping clips in the sliders and grids (video[data-lb-video]) join the same sequence. On phones, tapping
 * one opens it in the lightbox with controls and sound, uncropped; on larger screens it keeps playing inline.
 */
export function initLightbox() {
	const box = document.getElementById('lb');
	const items = [...document.querySelectorAll('img[data-lb], video[data-lb-video]')];
	if (!box || !items.length) return;
	const full = box.querySelector('[data-lb-img]');
	const player = box.querySelector('[data-lb-video-el]');
	const stage = box.querySelector('[data-lb-stage]');
	const caption = box.querySelector('[data-lb-cap]');
	const count = box.querySelector('[data-lb-count]');
	const closeButton = box.querySelector('[data-lb-close]');
	const phone = window.matchMedia('(max-width: 760px)');
	let current = 0;
	let opener = null;

	const stopPlayer = () => {
		player.pause();
		player.removeAttribute('src');
		player.removeAttribute('poster');
		player.load();
	};
	const show = (index) => {
		current = (index + items.length) % items.length;
		const item = items[current];
		const isVideo = item.tagName === 'VIDEO';
		stopPlayer();
		full.hidden = isVideo;
		player.hidden = !isVideo;
		if (isVideo) {
			player.src = item.currentSrc || item.querySelector('source')?.src || '';
			if (item.poster) player.poster = item.poster;
			player.style.aspectRatio = item.style.getPropertyValue('--ar') || '';
			player.play().catch(() => {});
		} else {
			full.src = item.currentSrc || item.src;
			full.alt = item.alt;
		}
		stage.textContent = item.dataset.lbStage || '';
		caption.textContent = isVideo ? item.getAttribute('aria-label') || '' : item.alt;
		count.textContent = `${current + 1} / ${items.length}`;
	};
	const open = (index) => {
		opener = items[index];
		show(index);
		box.hidden = false;
		document.documentElement.classList.add('lb-open');
		window.lenis?.stop();
		closeButton.focus();
	};
	const close = () => {
		stopPlayer();
		box.hidden = true;
		document.documentElement.classList.remove('lb-open');
		window.lenis?.start();
		opener?.focus();
	};

	items.forEach((item, index) => {
		const inline = () => item.tagName === 'VIDEO' && !phone.matches; // clips keep playing inline on larger screens
		item.tabIndex = 0;
		item.setAttribute('role', 'button');
		item.addEventListener('click', () => {
			if (!inline()) open(index);
		});
		item.addEventListener('keydown', (event) => {
			if ((event.key === 'Enter' || event.key === ' ') && !inline()) {
				event.preventDefault();
				open(index);
			}
		});
	});
	closeButton.addEventListener('click', close);
	box.querySelector('[data-lb-prev]').addEventListener('click', () => show(current - 1));
	box.querySelector('[data-lb-next]').addEventListener('click', () => show(current + 1));
	box.addEventListener('click', (event) => {
		if (event.target === box) close();
	});
	document.addEventListener('keydown', (event) => {
		if (box.hidden) return;
		if (event.key === 'Escape') close();
		if (event.key === 'ArrowLeft') show(current - 1);
		if (event.key === 'ArrowRight') show(current + 1);
		if (event.key === 'Tab') {
			// keep focus inside the dialog
			const focusable = [...box.querySelectorAll('button, video[controls]:not([hidden])')];
			const first = focusable[0];
			const last = focusable[focusable.length - 1];
			if (event.shiftKey && document.activeElement === first) {
				event.preventDefault();
				last.focus();
			} else if (!event.shiftKey && document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	});
}
