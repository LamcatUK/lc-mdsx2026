/** Move focus to the contact form's status message after a submission, so screen readers announce it. */
export function initContactNotice() {
	const notice = document.querySelector('[data-contact-notice]');
	if (notice) notice.focus({ preventScroll: false });
}
