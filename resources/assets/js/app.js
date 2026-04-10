import './bootstrap';

document.querySelectorAll('[data-gallery]').forEach((gallery) => {
	const featured = gallery.querySelector('[data-gallery-featured]');

	gallery.querySelectorAll('[data-gallery-thumb]').forEach((button) => {
		button.addEventListener('click', () => {
			const image = button.getAttribute('data-gallery-thumb');

			if (!featured || !image) {
				return;
			}

			featured.setAttribute('src', image);
			featured.setAttribute('alt', button.getAttribute('data-gallery-alt') ?? '');
		});
	});
});