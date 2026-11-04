(() => {
  'use strict';

  const root = document.body;
  if (!root) return;

  const galleryInput = root.querySelector('[data-legacy-gallery-value]');
  const galleryPreview = root.querySelector('[data-legacy-gallery-preview]');

  const renderGallery = (ids) => {
    if (!galleryPreview) return;
    galleryPreview.replaceChildren();
    ids.filter(Boolean).forEach((id) => {
      const chip = document.createElement('span');
      chip.className = 'es-legacy-gallery-chip';
      chip.textContent = `#${id}`;
      galleryPreview.append(chip);
    });
  };

  const syncLegacyGalleryFromFields = () => {
    if (!galleryInput) return;
    const ids = Array.from(root.querySelectorAll('.es-legacy-image-fields input[type="number"]'))
      .map((input) => input.value.trim())
      .filter((id) => id && id !== '0');
    galleryInput.value = ids.join(',');
    renderGallery(ids);
  };

  if (galleryInput) renderGallery(galleryInput.value.split(',').map((item) => item.trim()));

  root.addEventListener('input', (event) => {
    if (event.target.matches('.es-legacy-image-fields input[type="number"]')) syncLegacyGalleryFromFields();
  });

  root.addEventListener('click', (event) => {
    const add = event.target.closest('.es-legacy-repeater-add');
    if (add) {
      const repeater = add.closest('[data-legacy-repeater]');
      const template = repeater?.querySelector('[data-legacy-repeater-template]');
      const rows = repeater?.querySelector('[data-legacy-repeater-rows]');
      if (!repeater || !template || !rows) return;
      const index = Number.parseInt(repeater.dataset.nextIndex || '0', 10);
      repeater.dataset.nextIndex = String(index + 1);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = template.innerHTML.replaceAll('__ROW__', `item-${index}`).trim();
      if (wrapper.firstElementChild) rows.append(wrapper.firstElementChild);
      return;
    }

    const remove = event.target.closest('.es-legacy-repeater-remove');
    if (remove) {
      remove.closest('[data-legacy-repeater-row]')?.remove();
      return;
    }

    const button = event.target.closest('.es-legacy-select-media');
    if (!button || !window.wp?.media) return;
    const mode = button.dataset.mediaMode || 'single';
    if (mode === 'gallery') {
      if (!galleryInput) return;
      const frame = window.wp.media({
        title: 'انتخاب گالری پروژه',
        button: { text: 'استفاده از تصاویر' },
        library: { type: 'image' },
        multiple: true,
      });
      frame.on('select', () => {
        const ids = frame.state().get('selection').toJSON().map((item) => String(item.id));
        galleryInput.value = ids.join(',');
        root.querySelectorAll('.es-legacy-image-fields input[type="number"]').forEach((input, index) => {
          input.value = ids[index] || '0';
        });
        renderGallery(ids);
      });
      frame.open();
      return;
    }

    const target = document.getElementById(button.dataset.target || '');
    if (!target) return;
    const frame = window.wp.media({
      title: 'انتخاب تصویر',
      button: { text: 'استفاده از تصویر' },
      library: { type: 'image' },
      multiple: false,
    });
    frame.on('select', () => {
      const attachment = frame.state().get('selection').first()?.toJSON();
      if (attachment?.id) {
        target.value = String(attachment.id);
        if (target.closest('.es-legacy-image-fields')) syncLegacyGalleryFromFields();
      }
    });
    frame.open();
  });
})();
