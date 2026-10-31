import { initNavToggle } from './nav-toggle';
import { initNavDropdowns } from './nav-dropdown';
import { initDialogs } from './dialog';
import { initHeroReel } from './hero-reel';
import { initPortfolioFilter } from './portfolio-filter';
import { initReveal } from './reveal';
import { initContactForm } from './contact-form';
import { initHeaderScroll } from './header-scroll';
import { initLenis } from './lenis';
import { initSiteSearch } from './site-search';

document.addEventListener('DOMContentLoaded', () => {
	// tells the failsafe in header.php that the script ran; if it never does, content is revealed anyway
	document.documentElement.classList.add('js-ready');
	initNavToggle();
	initNavDropdowns();
	initDialogs();
	initHeroReel();
	initPortfolioFilter();
	initReveal();
	initContactForm();
	initHeaderScroll();
	initLenis();
	initSiteSearch();
});
