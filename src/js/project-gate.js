/**
 * Lead-capture gate on portfolio projects: the modal opens by itself once (the page behind it only
 * ever holds the public teaser, the locked content is not in the page), and a successful Contact
 * Form 7 send reloads the page so the server, now holding the signed cookie, returns the full
 * case study. dialog.js wires the open and close buttons.
 */
export function initProjectGate() {
	const dialog = document.querySelector('dialog[data-gate-auto]');
	if (!dialog) return;

	const lenisStop = () => window.lenis?.stop();
	const lenisStart = () => window.lenis?.start();
	dialog.addEventListener('close', lenisStart);
	dialog.addEventListener('cancel', lenisStart);
	document.querySelectorAll('[data-dialog-target="' + dialog.id + '"]').forEach((button) => button.addEventListener('click', lenisStop));

	// Give the page a moment to settle before the modal appears.
	window.setTimeout(() => {
		if (!dialog.open) {
			dialog.showModal();
			dialog.querySelector('input[type="email"]')?.focus(); // rather than the close button
			lenisStop();
		}
	}, 700);

	// A sent email unlocks; so does a failed delivery (the server grants access either way).
	['wpcf7mailsent', 'wpcf7mailfailed'].forEach((name) =>
		dialog.addEventListener(name, () => {
			window.setTimeout(() => {
				window.location.hash = 'project-details';
				window.location.reload();
			}, 900);
		})
	);
}
