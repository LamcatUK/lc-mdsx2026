/**
 * Conversion tracking for Contact Form 7. After a successful send CF7 fires
 * `wpcf7mailsent` on the form's wrapper; push a GTM/GA4 `generate_lead` event with
 * the chosen topic (the visible label, not the piped recipient addresses).
 */
export function initContactForm() {
	document.addEventListener('wpcf7mailsent', (event) => {
		const form = event.target.querySelector('form') || event.target;
		const select = form.querySelector('select[name="topic"]');
		const topic = select ? select.options[select.selectedIndex]?.text || '' : '';
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({ event: 'generate_lead', form_id: 'contact', enquiry_topic: topic });
	});
}
