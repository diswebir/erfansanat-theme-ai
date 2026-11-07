(() => {
  'use strict';

  const builder = document.querySelector('[data-section-builder]');
  if (!builder) return;

  const list = builder.querySelector('[data-section-list]');
  let draggedCard = null;

  const updateOrder = () => {
    list.querySelectorAll('[data-section-card]').forEach((card, index) => {
      const order = card.querySelector('[data-section-order]');
      const label = card.querySelector('[data-section-order-label]');
      if (order) order.value = String(index);
      if (label) label.textContent = String(index + 1);
    });
  };

  list.addEventListener('dragstart', (event) => {
    const card = event.target.closest('[data-section-card]');
    if (!card || !event.target.closest('.es-section-drag')) {
      event.preventDefault();
      return;
    }
    draggedCard = card;
    card.classList.add('is-dragging');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', card.dataset.sectionId || 'section');
  });

  list.addEventListener('dragover', (event) => {
    if (!draggedCard) return;
    event.preventDefault();
    const target = event.target.closest('[data-section-card]');
    if (!target || target === draggedCard) return;
    const bounds = target.getBoundingClientRect();
    const after = event.clientY > bounds.top + bounds.height / 2;
    target.classList.add('is-drop-target');
    list.insertBefore(draggedCard, after ? target.nextSibling : target);
  });

  list.addEventListener('dragleave', (event) => {
    const target = event.target.closest('[data-section-card]');
    if (target && !target.contains(event.relatedTarget)) target.classList.remove('is-drop-target');
  });

  list.addEventListener('drop', (event) => {
    event.preventDefault();
    list.querySelectorAll('.is-drop-target').forEach((item) => item.classList.remove('is-drop-target'));
    updateOrder();
  });

  list.addEventListener('dragend', () => {
    if (draggedCard) draggedCard.classList.remove('is-dragging');
    draggedCard = null;
    list.querySelectorAll('.is-drop-target').forEach((item) => item.classList.remove('is-drop-target'));
    updateOrder();
  });

  builder.addEventListener('click', (event) => {
    const toggle = event.target.closest('.es-section-toggle');
    if (toggle) {
      const card = toggle.closest('[data-section-card]');
      const expanded = toggle.getAttribute('aria-expanded') === 'true';
      card.classList.toggle('is-collapsed', expanded);
      toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
      toggle.textContent = expanded ? 'بازکردن' : 'بستن';
      return;
    }

    const addRow = event.target.closest('.es-repeater-add');
    if (addRow) {
      const repeater = addRow.closest('[data-section-repeater]');
      const template = repeater?.querySelector('[data-repeater-template]');
      const rows = repeater?.querySelector('[data-repeater-rows]');
      if (!template || !rows) return;
      const index = Number.parseInt(repeater.dataset.nextIndex || '0', 10);
      repeater.dataset.nextIndex = String(index + 1);
      const wrapper = document.createElement('div');
      wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(index)).trim();
      if (wrapper.firstElementChild) rows.append(wrapper.firstElementChild);
      return;
    }

    const removeRow = event.target.closest('.es-repeater-remove');
    if (removeRow) {
      removeRow.closest('[data-repeater-row]')?.remove();
      return;
    }

    const mediaButton = event.target.closest('.es-section-select-media');
    if (!mediaButton || !window.wp?.media) return;
    const control = mediaButton.closest('.es-section-media-control');
    const input = control?.querySelector('input[data-media-type]');
    if (!input) return;

    const mediaType = input.dataset.mediaType === 'video' ? 'video' : 'image';
    const frame = window.wp.media({
      title: mediaType === 'video' ? 'انتخاب ویدئو' : 'انتخاب تصویر',
      button: { text: 'استفاده از پرونده' },
      library: { type: mediaType },
      multiple: false,
    });
    frame.on('select', () => {
      const attachment = frame.state().get('selection').first()?.toJSON();
      if (attachment?.url) {
        input.value = attachment.url;
        input.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });
    frame.open();
  });

  updateOrder();
})();
