// DOM-free behavioral test for the wp.media adapter (Node built-ins only).
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

class Element {
  constructor(attrs = {}) { this.attrs = attrs; this.children = []; this.value = ''; this.events = []; }
  getAttribute(key) { return this.attrs[key] ?? null; }
  querySelector(selector) { return this.attrs.controls?.[selector] ?? null; }
  closest(selector) {
    if (selector === '[data-es-media-select]' && this.attrs.select) return this;
    if (selector === '[data-es-media-remove]' && this.attrs.remove) return this;
    if (selector === '[data-es-media]') return this.attrs.control ?? null;
    return null;
  }
  appendChild(child) { this.children.push(child); return child; }
  replaceChildren(...children) { this.children = children; }
  dispatchEvent(event) { this.events.push(event.type); return true; }
}

const documentListeners = {};
let frame;
let mediaOptions;
const document = {
  addEventListener(name, callback) { documentListeners[name] = callback; },
  createElement(name) { const el = new Element(); el.tagName = name; return el; }
};
class FakeEvent { constructor(type, options) { this.type = type; this.bubbles = options?.bubbles; } }
const window = {
  location: { href: 'https://example.test/wp-admin/admin.php' },
  wp: { media(options) {
    mediaOptions = options;
    frame = { callback: null, opened: false, on(name, cb) { if (name === 'select') this.callback = cb; }, open() { this.opened = true; }, state() { return { get() { return { toJSON: () => frame.selection }; } }; } };
    return frame;
  } }
};
const context = { window, document, Event: FakeEvent, URL, Number, String, Boolean, Array, Object, Math };
vm.runInNewContext(fs.readFileSync('theme/assets/admin/media.js', 'utf8'), context, { filename: 'media.js' });

function control(mode = 'single', kind = 'image') {
  const input = new Element();
  const preview = new Element();
  const root = new Element({
    'data-es-media-mode': mode,
    'data-es-media-kind': kind,
    controls: { '.es-media__value': input, '.es-media__preview': preview }
  });
  return { root, input, preview };
}
function click(target) {
  documentListeners.click({ target, preventDefault() {} });
}

const single = control();
const select = new Element({ select: true, control: single.root });
click(select);
assert.equal(mediaOptions.multiple, false);
assert.equal(frame.opened, true);
frame.selection = [{ id: 42, type: 'image', title: 'Product image', url: 'https://cdn.example.test/image.jpg', sizes: { thumbnail: { url: 'https://cdn.example.test/thumb.jpg' } } }];
frame.callback();
assert.equal(single.input.value, '42', 'single selection stores the attachment ID');
assert.equal(single.preview.children.length, 1, 'single selection updates its preview');
assert.equal(single.preview.children[0].children[0].src, 'https://cdn.example.test/thumb.jpg');

const remove = new Element({ remove: true, control: single.root });
click(remove);
assert.equal(single.input.value, '', 'remove clears the hidden field');
assert.equal(single.preview.children.length, 0, 'remove clears the preview');
assert.deepEqual(single.input.events, ['input', 'change', 'input', 'change']);

const multiple = control('multiple', 'any');
const multiSelect = new Element({ select: true, control: multiple.root });
click(multiSelect);
assert.equal(mediaOptions.multiple, true);
frame.selection = [
  { id: 7, type: 'image', url: 'https://cdn.example.test/7.jpg' },
  { id: 9, type: 'image', url: 'https://cdn.example.test/9.jpg' }
];
frame.callback();
assert.equal(multiple.input.value, '7,9', 'multiple selection stores a clean CSV of IDs');
assert.equal(multiple.preview.children.length, 2);

const unsafe = control('single', 'any');
click(new Element({ select: true, control: unsafe.root }));
frame.selection = [{ id: 11, type: 'document', title: 'unsafe file', url: 'javascript:alert(1)' }];
frame.callback();
assert.equal(unsafe.input.value, '11');
assert.equal(unsafe.preview.children[0].children[0].tagName, 'span', 'unsafe scheme is rendered as text, never a link');
assert.equal(unsafe.preview.children[0].children[0].href, undefined);

console.log('media picker: single/multiple selection, preview, remove and unsafe URL checks passed');
