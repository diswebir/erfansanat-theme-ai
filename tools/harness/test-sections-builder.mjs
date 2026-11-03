// Behavioral smoke test for the admin Sections Builder (no external DOM package).
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

class Element {
  constructor(kind, attrs = {}) {
    this.kind = kind; this.attrs = { ...attrs }; this.children = []; this.parentNode = null; this.listeners = {};
    this.hidden = false; this.checked = false; this.value = ''; this.disabled = false; this.textContent = '';
    this.classList = { toggle: (name, force) => { this.attrs[`class:${name}`] = !!force; }, contains: (name) => String(this.attrs.class || '').split(/\s+/).includes(name) || this.attrs[`class:${name}`] === true };
  }
  getAttribute(name) { return this.attrs[name] ?? null; }
  setAttribute(name, value) { this.attrs[name] = String(value); }
  hasAttribute(name) { return Object.hasOwn(this.attrs, name); }
  addEventListener(name, fn) { this.listeners[name] = fn; }
  matches(selector) {
    const match = selector.match(/^\[([^\]]+)\]$/);
    if (match) return this.hasAttribute(match[1]);
    if (selector === 'button') return this.kind === 'button';
    if (selector === '[type="submit"]') return this.attrs.type === 'submit';
    if (selector === '[data-es-section-enabled]') return this.hasAttribute('data-es-section-enabled');
    return false;
  }
  closest(selector) {
    if (selector === '[data-es-section-item]') return this.kind === 'card' ? this : (this.parentNode?.kind === 'card' ? this.parentNode : null);
    if (selector === '[data-es-section-handle]') return this.hasAttribute('data-es-section-handle') ? this : null;
    if (selector === 'button') return this.kind === 'button' ? this : null;
    return null;
  }
  querySelector(selector) {
    if (this.kind === 'form') return this.controls[selector] || null;
    if (this.kind === 'card') return this.controls[selector] || null;
    return null;
  }
  querySelectorAll(selector) {
    if (this.kind === 'list' && selector === '[data-es-section-item]') return this.children.slice();
    if (this.kind === 'card' && selector === '[data-es-section-field]') return this.fields;
    if (this.kind === 'field' && selector === 'input, textarea, select') return this.controls;
    return [];
  }
  focus() { this.focused = true; }
  appendChild(child) {
    if (child.parentNode) child.parentNode.children = child.parentNode.children.filter((item) => item !== child);
    this.children.push(child); child.parentNode = this; return child;
  }
  insertBefore(child, reference) {
    if (child.parentNode) child.parentNode.children = child.parentNode.children.filter((item) => item !== child);
    const index = this.children.indexOf(reference);
    if (index < 0) this.children.push(child); else this.children.splice(index, 0, child);
    child.parentNode = this; return child;
  }
  focus() { this.focused = true; }
  get nextElementSibling() {
    if (!this.parentNode) return null;
    const list = this.parentNode.children; return list[list.indexOf(this) + 1] || null;
  }
}

function button(attr) { return new Element('button', { [attr]: '', type: 'button' }); }
function makeCard(key, title) {
  const card = new Element('card', { 'data-es-section-item': '', 'data-es-section-key': key });
  card.fields = [];
  card.controls = {
    '[data-es-section-add]': button('data-es-section-add'),
    '[data-es-section-remove]': button('data-es-section-remove'),
    '[data-es-section-up]': button('data-es-section-up'),
    '[data-es-section-down]': button('data-es-section-down'),
    '[data-es-section-toggle-editor]': button('data-es-section-toggle-editor'),
    '[data-es-section-handle]': button('data-es-section-handle'),
    '[data-es-section-enabled]': new Element('input', { 'data-es-section-enabled': '' }),
    '[data-es-section-editor]': new Element('editor', { 'data-es-section-editor': '' }),
  };
  card.controls['[data-es-section-enabled]'].checked = true;
  card.controls['[data-es-section-toggle-editor]'].setAttribute('aria-expanded', 'false');
  card.controls['[data-es-section-handle]'].parentNode = card;
  const field = new Element('field', { 'data-es-section-field': 'title', 'data-es-section-field-type': 'text' });
  const value = new Element('input'); value.tagName = 'input'; value.value = title; value.classList = { contains: () => false };
  field.controls = [value]; card.fields.push(field);
  Object.values(card.controls).forEach((control) => { control.parentNode = card; });
  return card;
}

const active = new Element('list');
const available = new Element('list');
const status = new Element('status');
const submit = new Element('button', { type: 'submit' });
const form = new Element('form', { 'data-es-sections-form': '', 'data-ajax-url': '/wp-admin/admin-ajax.php' });
form.controls = { '[data-es-section-list]': active, '[data-es-section-available]': available, '[data-es-sections-status]': status, '[type="submit"]': submit };
form.addEventListener = Element.prototype.addEventListener;
const documentListeners = {};
const document = {
  querySelector(selector) { return selector === '[data-es-sections-form]' ? form : null; },
  addEventListener(type, listener) { documentListeners[type] = listener; },
};
const first = makeCard('hero', 'Hero');
const second = makeCard('projects', 'Projects');
active.appendChild(first); active.appendChild(second);

class MockFormData {
  constructor() { this.entries = []; }
  append(key, value) { this.entries.push([key, value]); }
}
let sentPayload = null;
const window = { FormData: MockFormData, fetch: async (_url, options) => {
  sentPayload = options.body;
  return { ok: true, json: async () => ({ success: true }) };
} };
const context = { document, window, FormData: MockFormData, Array, String, Object, Number, JSON };
vm.runInNewContext(fs.readFileSync('theme/assets/admin/sections.js', 'utf8'), context, { filename: 'sections.js' });

function click(control) { documentListeners.click({ target: control, preventDefault() {} }); }

click(first.controls['[data-es-section-remove]']);
assert.equal(first.parentNode, available, 'remove moves the card to the available list');
assert.equal(first.controls['[data-es-section-enabled]'].checked, false, 'remove disables the section');
click(first.controls['[data-es-section-add]']);
assert.equal(first.parentNode, active, 'add returns the card to the active list');
assert.equal(first.controls['[data-es-section-enabled]'].checked, true, 'add enables the section');

documentListeners.keydown({ target: second.controls['[data-es-section-handle]'], key: 'ArrowUp', preventDefault() {} });
assert.deepEqual(active.children.map((card) => card.getAttribute('data-es-section-key')), ['projects', 'hero'], 'keyboard ArrowUp reorders the card');

const enabled = first.controls['[data-es-section-enabled]'];
enabled.checked = false;
form.listeners.change({ target: enabled });
click(first.controls['[data-es-section-toggle-editor]']);
assert.equal(first.controls['[data-es-section-toggle-editor]'].getAttribute('aria-expanded'), 'true', 'editor disclosure keeps aria-expanded in sync');
assert.equal(first.controls['[data-es-section-editor]'].hidden, false, 'editor panel opens');

(async () => {
  form.listeners.submit({ preventDefault() {} });
  await new Promise((resolve) => setTimeout(resolve, 0));
  const encoded = sentPayload.entries.find(([key]) => key === 'state')?.[1];
  const state = JSON.parse(encoded);
  assert.deepEqual(state.order, ['projects', 'hero'], 'save serializes the visible builder order');
  assert.equal(state.enabled.hero, false, 'save serializes section visibility state');
  assert.deepEqual(state.available, [], 'save serializes the available-section bucket');
  assert.equal(state.data.hero.title, 'Hero', 'save serializes field values');
  assert.equal(sentPayload.entries.find(([key]) => key === 'action')?.[1], 'es_save_sections');
  console.log('sections builder: add/remove, keyboard order, toggle, editor, and serialized save passed');
})().catch((error) => { console.error(error); process.exitCode = 1; });
