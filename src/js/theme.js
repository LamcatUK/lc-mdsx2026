import { initNavToggle } from './nav-toggle';
import { initNavDropdowns } from './nav-dropdown';
import { initDialogs } from './dialog';
import { initHeroReel } from './hero-reel';
import { initPortfolioFilter } from './portfolio-filter';
import { initReveal } from './reveal';
import { initContactNotice } from './contact-notice';

document.addEventListener('DOMContentLoaded', () => {
	initNavToggle();
	initNavDropdowns();
	initDialogs();
	initHeroReel();
	initPortfolioFilter();
	initReveal();
	initContactNotice();
});
