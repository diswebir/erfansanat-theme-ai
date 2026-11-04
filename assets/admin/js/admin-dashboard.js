(function () {
    'use strict';

    var config = window.esAdminData || {};
    var maximumImportBytes = 1048576;

    function getStatus(container, selector) {
        return container ? container.querySelector(selector || '[data-es-admin-status]') : null;
    }

    function setStatus(element, message, type) {
        if (!element) {
            return;
        }

        element.textContent = message || '';
        element.className = 'es-admin-status' + (type ? ' is-' + type : '');
        element.setAttribute('role', type === 'error' ? 'alert' : 'status');
    }

    function request(action, data) {
        var body = data || new FormData();
        body.set('action', action);
        body.set('_ajax_nonce', config.nonce || '');

        return fetch(config.ajaxUrl || '', {
            method: 'POST',
            body: body,
            credentials: 'same-origin',
            headers: { Accept: 'application/json' }
        }).then(function (response) {
            return response.text().then(function (text) {
                var result;
                try {
                    result = JSON.parse(text);
                } catch (error) {
                    throw new Error('پاسخ سرور قابل خواندن نیست؛ صفحه را تازه‌سازی و دوباره تلاش کنید.');
                }

                if (!response.ok || !result || result.success !== true) {
                    var message = result && result.data && result.data.message;
                    throw new Error(message || 'درخواست انجام نشد. دسترسی و اعتبار نشست را بررسی کنید.');
                }

                return result.data || {};
            });
        });
    }

    function withBusy(button, operation) {
        if (button.disabled) {
            return Promise.resolve();
        }

        var originalLabel = button.textContent;
        button.disabled = true;
        button.setAttribute('aria-busy', 'true');
        button.classList.add('is-busy');

        return Promise.resolve().then(operation).finally(function () {
            button.disabled = false;
            button.removeAttribute('aria-busy');
            button.classList.remove('is-busy');
            button.textContent = originalLabel;
        });
    }

    function updateRepeaterCount(repeater) {
        var items = Array.prototype.slice.call(repeater.querySelectorAll('[data-es-repeater-item]'));
        var count = items.length;
        var output = repeater.querySelector('[data-es-repeater-count]');

        items.forEach(function (item, index) {
            item.setAttribute('data-es-repeater-index', String(index));
            item.querySelectorAll('[name]').forEach(function (field) {
                field.name = field.name.replace(/(options\[[^\]]+\]\[)\d+(\]\[)/, '$1' + index + '$2');
            });
            var moveUp = item.querySelector('[data-es-repeater-move-up]');
            var moveDown = item.querySelector('[data-es-repeater-move-down]');
            if (moveUp) moveUp.disabled = index === 0;
            if (moveDown) moveDown.disabled = index === count - 1;
        });

        repeater.setAttribute('data-es-repeater-next-index', String(count));
        if (output) {
            output.textContent = String(count);
        }
    }

    function selectMedia(button) {
        var wrapper = button.closest('[data-es-media-field]');
        if (!wrapper) {
            return;
        }

        if (!window.wp || !window.wp.media) {
            setStatus(getStatus(wrapper.closest('form')), 'کتابخانه رسانه در دسترس نیست. صفحه را تازه‌سازی کنید.', 'error');
            return;
        }

        if (!wrapper._esMediaFrame) {
            var frame = window.wp.media({
                title: 'انتخاب تصویر',
                button: { text: 'استفاده از تصویر' },
                library: { type: 'image' },
                multiple: false
            });

            frame.on('select', function () {
                var selection = frame.state().get('selection').first();
                var attachment = selection ? selection.toJSON() : null;
                var value = wrapper.querySelector('[data-es-media-value]');
                var slot = wrapper.querySelector('[data-es-media-preview-slot]');

                if (!attachment || !value || !slot) {
                    return;
                }

                value.value = String(attachment.id || '');
                value.dispatchEvent(new Event('change', { bubbles: true }));

                var image = slot.querySelector('[data-es-media-preview]');
                if (!image) {
                    image = document.createElement('img');
                    image.className = 'es-media-preview';
                    image.setAttribute('data-es-media-preview', '');
                    image.alt = 'پیش‌نمایش رسانه انتخاب‌شده';
                    slot.appendChild(image);
                }

                image.src = attachment.sizes && attachment.sizes.thumbnail
                    ? attachment.sizes.thumbnail.url
                    : attachment.url;
                image.alt = attachment.alt || 'پیش‌نمایش رسانه انتخاب‌شده';
            });

            wrapper._esMediaFrame = frame;
        }

        wrapper._esMediaFrame.open();
    }

    function removeMedia(button) {
        var wrapper = button.closest('[data-es-media-field]');
        if (!wrapper) {
            return;
        }

        var value = wrapper.querySelector('[data-es-media-value]');
        var preview = wrapper.querySelector('[data-es-media-preview]');
        if (value) {
            value.value = '';
            value.dispatchEvent(new Event('change', { bubbles: true }));
        }
        if (preview) {
            preview.remove();
        }
    }

    function exportOptions(button) {
        var tools = button.closest('[data-es-tools]');
        var status = getStatus(tools, '[data-es-tools-status]');

        return withBusy(button, function () {
            setStatus(status, 'در حال آماده‌سازی فایل پشتیبان…', 'loading');
            return request('erfansanat_export_options').then(function (data) {
                var json = JSON.stringify(data.options || {}, null, 2);
                var output = tools.querySelector('[data-es-export-output]');
                var copy = tools.querySelector('[data-es-copy-export]');
                if (output) {
                    output.value = json;
                }
                if (copy) {
                    copy.disabled = false;
                }

                var blob = new Blob([json], { type: 'application/json;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var link = document.createElement('a');
                link.href = url;
                link.download = 'erfansanat-theme-options-' + new Date().toISOString().slice(0, 10) + '.json';
                link.hidden = true;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
                setStatus(status, 'فایل پشتیبان ساخته و دانلود شد.', 'success');
            });
        }).catch(function (error) {
            setStatus(status, error.message, 'error');
        });
    }

    function importOptions(button) {
        var tools = button.closest('[data-es-tools]');
        var status = getStatus(tools, '[data-es-tools-status]');
        var input = tools.querySelector('[data-es-import-json]');
        var json = input ? input.value.trim() : '';

        if (!json) {
            setStatus(status, 'ابتدا فایل یا محتوای JSON را انتخاب کنید.', 'error');
            return;
        }

        if (new Blob([json]).size > maximumImportBytes) {
            setStatus(status, 'حجم فایل بیشتر از یک مگابایت است.', 'error');
            return;
        }

        try {
            var parsed = JSON.parse(json);
            if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
                throw new Error('ساختار JSON باید یک شیء تنظیمات باشد.');
            }
        } catch (error) {
            setStatus(status, error.message || 'ساختار JSON معتبر نیست.', 'error');
            return;
        }

        return withBusy(button, function () {
            setStatus(status, 'در حال بررسی و درون‌ریزی تنظیمات…', 'loading');
            var data = new FormData();
            data.set('import_json', json);
            return request('erfansanat_import_options', data).then(function (result) {
                setStatus(status, result.message || 'تنظیمات وارد شد. برای مشاهده مقادیر جدید، صفحه را تازه‌سازی کنید.', 'success');
            });
        }).catch(function (error) {
            setStatus(status, error.message, 'error');
        });
    }

    function resetOptions(button) {
        var tools = button.closest('[data-es-tools]');
        var status = getStatus(tools, '[data-es-tools-status]');
        if (!window.confirm('همه تنظیمات به مقادیر پیش‌فرض برگردند؟')) {
            return;
        }

        return withBusy(button, function () {
            setStatus(status, 'در حال بازنشانی تنظیمات…', 'loading');
            return request('erfansanat_reset_options').then(function (result) {
                setStatus(status, result.message || 'تنظیمات بازنشانی شد.', 'success');
                window.setTimeout(function () { window.location.reload(); }, 900);
            });
        }).catch(function (error) {
            setStatus(status, error.message, 'error');
        });
    }

    document.addEventListener('click', function (event) {
        var saveButton = event.target.closest('[data-es-save-options]');
        if (saveButton) {
            var form = saveButton.closest('form[data-es-options-form]');
            if (!form) {
                return;
            }

            var status = getStatus(form);
            withBusy(saveButton, function () {
                setStatus(status, 'در حال ذخیره تنظیمات…', 'loading');
                return request('erfansanat_save_options', new FormData(form)).then(function (result) {
                    setStatus(status, result.message || 'تنظیمات با موفقیت ذخیره شد.', 'success');
                });
            }).catch(function (error) {
                setStatus(status, error.message, 'error');
            });
            return;
        }

        var mediaSelect = event.target.closest('[data-es-media-select]');
        if (mediaSelect) {
            selectMedia(mediaSelect);
            return;
        }

        var mediaRemove = event.target.closest('[data-es-media-remove]');
        if (mediaRemove) {
            removeMedia(mediaRemove);
            return;
        }

        var repeaterAdd = event.target.closest('[data-es-repeater-add]');
        if (repeaterAdd) {
            var repeater = repeaterAdd.closest('[data-es-repeater]');
            var template = repeater && repeater.querySelector('template[data-es-repeater-template]');
            if (!template) {
                return;
            }

            var index = parseInt(repeater.getAttribute('data-es-repeater-next-index') || '0', 10);
            if (!Number.isFinite(index) || index < 0) {
                index = 0;
            }
            var fragment = template.content.cloneNode(true);
            fragment.querySelectorAll('[name]').forEach(function (field) {
                field.name = field.name.replace(/__INDEX__/g, String(index));
            });
            var newItem = fragment.querySelector('[data-es-repeater-item]');
            if (newItem) {
                newItem.setAttribute('data-es-repeater-index', String(index));
                repeater.insertBefore(fragment, template);
                repeater.setAttribute('data-es-repeater-next-index', String(index + 1));
                updateRepeaterCount(repeater);
            }
            return;
        }

        var moveUpButton = event.target.closest('[data-es-repeater-move-up]');
        var moveDownButton = event.target.closest('[data-es-repeater-move-down]');
        if (moveUpButton || moveDownButton) {
            var moveButton = moveUpButton || moveDownButton;
            var moveItem = moveButton.closest('[data-es-repeater-item]');
            var moveRepeater = moveButton.closest('[data-es-repeater]');
            if (moveItem && moveRepeater) {
                if (moveUpButton && moveItem.previousElementSibling && moveItem.previousElementSibling.matches('[data-es-repeater-item]')) {
                    moveRepeater.insertBefore(moveItem, moveItem.previousElementSibling);
                } else if (moveDownButton && moveItem.nextElementSibling && moveItem.nextElementSibling.matches('[data-es-repeater-item]')) {
                    moveRepeater.insertBefore(moveItem.nextElementSibling, moveItem);
                }
                updateRepeaterCount(moveRepeater);
            }
            return;
        }

        var repeaterRemove = event.target.closest('[data-es-repeater-remove]');
        if (repeaterRemove) {
            var item = repeaterRemove.closest('[data-es-repeater-item]');
            var parentRepeater = repeaterRemove.closest('[data-es-repeater]');
            if (item) {
                item.remove();
                updateRepeaterCount(parentRepeater);
            }
            return;
        }

        var exportButton = event.target.closest('[data-es-export-options]');
        if (exportButton) {
            exportOptions(exportButton);
            return;
        }

        var importButton = event.target.closest('[data-es-import-options]');
        if (importButton) {
            importOptions(importButton);
            return;
        }

        var resetButton = event.target.closest('[data-es-reset-options]');
        if (resetButton) {
            resetOptions(resetButton);
            return;
        }

        var copyButton = event.target.closest('[data-es-copy-export]');
        if (copyButton) {
            var output = copyButton.closest('[data-es-tools]').querySelector('[data-es-export-output]');
            var copyStatus = getStatus(copyButton.closest('[data-es-tools]'), '[data-es-tools-status]');
            if (!output || !output.value) {
                return;
            }
            var copyPromise = navigator.clipboard && navigator.clipboard.writeText
                ? navigator.clipboard.writeText(output.value)
                : Promise.resolve().then(function () {
                    output.focus();
                    output.select();
                    document.execCommand('copy');
                });
            copyPromise.then(function () {
                setStatus(copyStatus, 'JSON در کلیپ‌بورد کپی شد.', 'success');
            }).catch(function () {
                setStatus(copyStatus, 'کپی خودکار انجام نشد؛ متن را انتخاب و کپی کنید.', 'error');
            });
        }
    });

    document.querySelectorAll('[data-es-repeater]').forEach(updateRepeaterCount);

    document.addEventListener('change', function (event) {
        var fileInput = event.target.closest('[data-es-import-file]');
        if (!fileInput || !fileInput.files || !fileInput.files[0]) {
            return;
        }

        var file = fileInput.files[0];
        var tools = fileInput.closest('[data-es-tools]');
        var status = getStatus(tools, '[data-es-tools-status]');
        if (file.size > maximumImportBytes) {
            fileInput.value = '';
            setStatus(status, 'حجم فایل بیشتر از یک مگابایت است.', 'error');
            return;
        }

        var reader = new FileReader();
        reader.onload = function () {
            var input = tools.querySelector('[data-es-import-json]');
            if (input) {
                input.value = String(reader.result || '');
                setStatus(status, 'فایل بارگذاری شد؛ JSON را بررسی و سپس درون‌ریزی کنید.', 'success');
            }
        };
        reader.onerror = function () {
            setStatus(status, 'خواندن فایل ناموفق بود.', 'error');
        };
        reader.readAsText(file);
    });
})();
