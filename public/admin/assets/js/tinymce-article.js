(function () {
	'use strict';

	function attachRequiredContentValidation(textarea) {
		const form = textarea.closest('form');
		if (!form) return;

		form.addEventListener('submit', (e) => {
			if (!window.tinymce) return;
			window.tinymce.triggerSave();

			if (textarea.value.trim() === '') {
				e.preventDefault();
				window.tinymce.get(textarea.id)?.focus();
				alert('Le contenu est requis.');
			}
		});
	}

	function initArticleEditor(selectorOrTextareaId) {
		if (!window.tinymce) return;

		const textarea = typeof selectorOrTextareaId === 'string'
			? document.querySelector(selectorOrTextareaId.startsWith('#') ? selectorOrTextareaId : ('#' + selectorOrTextareaId))
			: null;

		if (!textarea) return;

		window.tinymce.init({
			license_key: 'gpl',
			selector: '#' + textarea.id,
			height: 450,
			base_url: '/assets/js/tinymce',
			suffix: '.min',
			menubar: false,
			plugins: 'lists link image table code autoresize',
			toolbar: 'undo redo | blocks | bold italic | bullist numlist | link image table | code',
			branding: false,
			paste_data_images: false,
			automatic_uploads: true,
			// Keep URLs stable in saved HTML (avoid ../../uploads/...).
			relative_urls: false,
			remove_script_host: false,
			document_base_url: '/',
			// Image plugin dialog options.
			image_description: true,
			image_dimensions: true,
			image_advtab: false,
			a11y_advanced_options: true,
			extended_valid_elements: 'img[alt|role|src|srcset|sizes|width|height|class|style|title|loading]',
			images_upload_url: '/admin/upload-image.php',
			images_upload_credentials: true,
			setup: (editor) => {
				const stripEmptyAltUnlessDecorativeRole = () => {
					const body = editor.getBody();
					if (!body) return;
					body.querySelectorAll('img[alt=""]').forEach((img) => {
						if (img.hasAttribute('data-mce-object') || img.hasAttribute('data-mce-placeholder')) return;
						// Keep alt="" only when explicitly marked decorative.
						const role = (img.getAttribute('role') || '').toLowerCase();
						if (role === 'presentation' || role === 'none') return;
						img.removeAttribute('alt');
					});
				};

				editor.on('init', () => {
					stripEmptyAltUnlessDecorativeRole();
					attachRequiredContentValidation(textarea);
				});
				editor.on('SetContent', stripEmptyAltUnlessDecorativeRole);
				editor.on('NodeChange', stripEmptyAltUnlessDecorativeRole);
			}
		});
	}

	window.initArticleEditor = initArticleEditor;
})();
