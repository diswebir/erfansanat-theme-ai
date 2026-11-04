(() => {
  'use strict';

  const form = document.querySelector('.es-options-form');
  if (!form || !window.EsThemeOptions) return;

  const saveButton = form.querySelector('.es-options-save');
  const savingMessage = form.querySelector('.es-options-saving');
  const notice = document.querySelector('.es-options-notice');

  const showNotice = (message, isError = false) => {
    if (!notice) return;
    notice.textContent = message;
    notice.classList.toggle('is-error', isError);
    notice.hidden = false;
  };

  form.addEventListener('click', (event) => {
    const button = event.target.closest('.es-options-select-media');
    if (!button || !window.wp?.media) return;

    const target = document.getElementById(button.dataset.target);
    if (!target) return;

    const frame = window.wp.media({
      title: 'انتخاب پرونده رسانه‌ای',
      button: { text: 'استفاده از این پرونده' },
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

  form.addEventListener('submit', async (event) => {
    event.preventDefault();
    if (saveButton) saveButton.disabled = true;
    if (savingMessage) savingMessage.textContent = window.EsThemeOptions.messages?.saving || '';
    if (notice) notice.hidden = true;

    const options = {};
    form.querySelectorAll('[data-option-path]').forEach((field) => {
      options[field.dataset.optionPath] = field.type === 'checkbox' ? field.checked : field.value;
    });

    const body = new URLSearchParams({
      action: window.EsThemeOptions.action,
      nonce: window.EsThemeOptions.nonce,
      options: JSON.stringify(options),
    });

    try {
      const response = await fetch(window.EsThemeOptions.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body,
      });
      const result = await response.json();
      if (!response.ok || !result.success) {
        throw new Error(result.data?.message || window.EsThemeOptions.messages?.error || 'خطا در ذخیره‌سازی');
      }
      showNotice(result.data?.message || window.EsThemeOptions.messages?.saved || 'ذخیره شد.');
    } catch (error) {
      showNotice(error.message || window.EsThemeOptions.messages?.error || 'خطا در ذخیره‌سازی', true);
    } finally {
      if (saveButton) saveButton.disabled = false;
      if (savingMessage) savingMessage.textContent = '';
    }
  });
})();
