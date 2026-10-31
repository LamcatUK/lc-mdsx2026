import { initNavToggle } from './nav-toggle';
import { initNavDropdowns } from './nav-dropdown';
import { initDialogs } from './dialog';
import { initHeroReel } from './hero-reel';
import { initPortfolioFilter } from './portfolio-filter';
import { initReveal } from './reveal';
import { initContactForm } from './contact-form';

document.addEventListener('DOMContentLoaded', () => {
	initNavToggle();
	initNavDropdowns();
	initDialogs();
	initHeroReel();
	initPortfolioFilter();
	initReveal();
	initContactForm();
});
