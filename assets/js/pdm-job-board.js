(function () {
	function openModal(modal) {
		if (!modal) return;
		modal.classList.add('is-open');
		modal.setAttribute('aria-hidden', 'false');
		// Lock body scroll
		document.body.classList.add('pdmjb-modal-open');
		document.documentElement.classList.add('pdmjb-modal-open');
		// Focus first focusable element if present
		var focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
		if (focusable) focusable.focus();
	}

	function closeModal(modal) {
		if (!modal) return;
		modal.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		// Unlock body scroll
		document.body.classList.remove('pdmjb-modal-open');
		document.documentElement.classList.remove('pdmjb-modal-open');
	}

	document.addEventListener('click', function (e) {
		var openBtn = e.target.closest('[data-pdmjb-open]');
		if (openBtn) {
			e.preventDefault();
			var target = openBtn.getAttribute('data-pdmjb-open');
			try {
				var modal = document.querySelector(target);
				openModal(modal);
			} catch (err) { }
		}
		var isClose = e.target.matches('[data-pdmjb-close]') || e.target.closest('[data-pdmjb-close]');
		if (isClose) {
			var modal = e.target.closest('.pdmjb-modal');
			closeModal(modal);
		}
	});

	// Disable Escape-to-close to prevent accidental form dismissals


})();
