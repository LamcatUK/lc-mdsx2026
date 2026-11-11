import { isStatic } from './project-motion';

/**
 * Video handling shared by the project hero, the film block and the reels.
 *
 * Loops (data-video="loop"): muted, looping, autoplaying; paused when scrolled offscreen; a visible pause
 * control on any clip over 5 seconds (WCAG 2.2.2). In static mode (reduced motion / ?static) they show the poster.
 *
 * Films ([data-film]): click the play button to play in place, with sound and controls. A Vimeo ID
 * (data-vimeo) loads the Vimeo player only after that click, so nothing third-party loads before interaction.
 * The hero also has a Close button that restores the page copy.
 */
export function initVideo() {
	const loops = [...document.querySelectorAll('video[data-video="loop"]')];
	const still = isStatic();

	loops.forEach((video) => {
		const pause = video.parentElement.querySelector('[data-video-pause]');
		let userPaused = false;

		if (still) {
			video.removeAttribute('autoplay');
			video.pause();
			return;
		}

		const sync = () => {
			if (pause) {
				pause.setAttribute('aria-pressed', String(userPaused));
				pause.querySelector('.visually-hidden').textContent = userPaused ? 'Play video' : 'Pause video';
			}
		};
		const reveal = () => {
			if (pause && video.duration > 5) pause.hidden = false;
		};
		if (video.readyState >= 1) reveal();
		else video.addEventListener('loadedmetadata', reveal, { once: true });

		pause?.addEventListener('click', () => {
			userPaused = !userPaused;
			if (userPaused) video.pause();
			else video.play().catch(() => {});
			sync();
		});

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(
				(entries) => {
					entries.forEach((entry) => {
						if (entry.isIntersecting && !userPaused) video.play().catch(() => {});
						else video.pause();
					});
				},
				{ threshold: 0.15 }
			).observe(video);
		}
	});

	document.querySelectorAll('[data-film]').forEach((root) => {
		const play = root.querySelector('[data-film-play]');
		const close = root.querySelector('[data-film-close]');
		const media = root.querySelector('[data-film-media]');
		const video = media?.querySelector('video[data-video="film"]');
		const vimeo = root.dataset.vimeo;
		let frame = null;

		play?.addEventListener('click', () => {
			root.classList.add('is-playing');
			if (vimeo) {
				frame = document.createElement('iframe');
				frame.src = `https://player.vimeo.com/video/${encodeURIComponent(vimeo)}?dnt=1&title=0&byline=0&portrait=0&autoplay=1`;
				frame.allow = 'autoplay; fullscreen; picture-in-picture';
				frame.allowFullscreen = true;
				frame.title = root.getAttribute('aria-label') || 'Film';
				media.appendChild(frame);
			} else if (video) {
				video.controls = true;
				video.play().catch(() => {});
			}
			(close || video || frame)?.focus?.();
		});

		close?.addEventListener('click', () => {
			video?.pause();
			if (video) video.controls = false;
			frame?.remove();
			frame = null;
			root.classList.remove('is-playing');
			play?.focus();
		});
	});
}
