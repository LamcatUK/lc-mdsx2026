import { initNavToggle } from './nav-toggle';
import { initNavDropdowns } from './nav-dropdown';
import { initDialogs } from './dialog';
import { initHeroReel } from './hero-reel';
import { initPortfolioFilter } from './portfolio-filter';
import { initReveal } from './reveal';
import { initContactForm } from './contact-form';
import { initHeaderScroll } from './header-scroll';
import { initLenis } from './lenis';
import { initWorkCursor } from './work-cursor';

document.addEventListener('DOMContentLoaded', () => {
	initNavToggle();
	initNavDropdowns();
	initDialogs();
	initHeroReel();
	initPortfolioFilter();
	initReveal();
	initContactForm();
	initHeaderScroll();
	initLenis();
	initWorkCursor();
});
