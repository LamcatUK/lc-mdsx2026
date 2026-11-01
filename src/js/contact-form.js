/**
 * Conversion tracking for Contact Form 7. After a successful send CF7 fires
 * `wpcf7mailsent` on the form's wrapper; push a GTM/GA4 `generate_lead` event with
 * the chosen topic (the visible label, not the piped recipient addresses), and follows the
 * form's thank-you page when it has one.
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

		// A form with `thank_you_page: /path/` in its Additional Settings sends the visitor there (inc/cf7.php);
		// the short wait lets the tracking event above go out first.
		const redirect = event.detail?.apiResponse?.redirect_url;
		if (redirect) {
			window.setTimeout(() => {
				window.location.href = redirect;
			}, 400);
		}
	});
}
