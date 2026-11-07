(() => {
  'use strict';

  const box = document.querySelector('[data-product-metabox]');
  if (!box) return;

  const renderGalleryPreview = (container, ids) => {
    if (!container) return;
    container.replaceChildren();
    ids.filter(Boolean).forEach((id) => {
      const badge = document.createElement('span');
      badge.className = 'es-product-gallery-item';
      badge.textContent = `#${id}`;
      container.append(badge);
    });
  };

  box.querySelectorAll('[data-product-gallery-value]').forEach((input) => {
    const preview = input.parentElement.querySelector('[data-product-gallery-preview]');
    renderGalleryPreview(preview, input.value.split(',').map((value) => value.trim()));
  });

  box.addEventListener('click', (event) => {
    const add = event.target.closest('.es-product-repeater-add');
    if (add) {
      const repeater = add.closest('[data-product-repeater]');
      const template = repeater?.querySelector('[data-product-repeater-template]');
      const rows = repeater?.querySelector('[data-product-repeater-rows]');
      if (!repeater || !template || !rows) return;
      const index = Number.parseInt(repeater.dataset.nextIndex || '0', 10);
      repeater.dataset.nextIndex = String(index + 1);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = template.innerHTML.replaceAll('__ROW__', `item-${index}`).trim();
      if (wrapper.firstElementChild) rows.append(wrapper.firstElementChild);
      return;
    }

    const remove = event.target.closest('.es-product-repeater-remove');
    if (remove) {
      remove.closest('[data-product-repeater-row]')?.remove();
      return;
    }

    const mediaButton = event.target.closest('.es-product-select-media');
    if (!mediaButton || !window.wp?.media) return;
    const mode = mediaButton.dataset.mediaMode || 'single';

    if (mode === 'gallery') {
      const input = box.querySelector('[data-product-gallery-value]');
      const preview = box.querySelector('[data-product-gallery-preview]');
      if (!input) return;
      const frame = window.wp.media({
        title: 'انتخاب تصاویر محصول',
        button: { text: 'استفاده از تصاویر' },
        library: { type: 'image' },
        multiple: true,
      });
      frame.on('select', () => {
        const selection = frame.state().get('selection').toJSON();
        const ids = selection.map((item) => String(item.id));
        input.value = ids.join(',');
        renderGalleryPreview(preview, ids);
      });
      frame.open();
      return;
    }

    const target = document.getElementById(mediaButton.dataset.target || '');
    if (!target) return;
    const frame = window.wp.media({
      title: 'انتخاب فایل کاتالوگ محصول',
      button: { text: 'استفاده از این فایل' },
      multiple: false,
    });
    frame.on('select', () => {
      const attachment = frame.state().get('selection').first()?.toJSON();
      if (attachment?.url) {
        target.value = attachment.url;
        target.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
    frame.open();
  });
})();
