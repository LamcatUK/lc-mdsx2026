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
		// the contact form carries a topic; the project gate and access request forms identify themselves by container
		const kind = select ? 'contact' : form.closest('[data-gate-form]') ? 'project-gate' : 'project-access-request';
		window.dataLayer = window.dataLayer || [];
		window.dataLayer.push({ event: 'generate_lead', form_id: kind, enquiry_topic: topic });
	});
}
