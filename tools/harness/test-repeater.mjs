// Lightweight behavior test for the native repeater controller (no DOM library). 
import assert from 'node:assert/strict';
import fs from 'node:fs';
import vm from 'node:vm';

class Element {
  constructor(kind = 'element', attrs = {}) { this.kind = kind; this.attrs = { ...attrs }; this.children = []; this.parent = null; this.value = ''; }
  getAttribute(name) { return this.attrs[name] ?? null; }
  setAttribute(name, value) { this.attrs[name] = String(value); }
  hasAttribute(name) { return Object.hasOwn(this.attrs, name); }
  closest(selector) {
    if (selector === '[data-es-repeater]' && this.kind === 'container') return this;
    if (selector === '[data-es-repeater]' && this.parent) return this.parent;
    if (selector === '[data-es-repeater]' && this.parentRow) return this.parentRow.parent;
    if (selector === '[data-es-repeater-item]' && this.kind === 'row') return this;
    if (selector === '[data-es-repeater-item]' && this.parentRow) return this.parentRow;
    const marker = selector.match(/^\[([^\]]+)\]$/)?.[1];
    if (marker && this.attrs[marker] !== undefined) return this;
    return null;
  }
  querySelectorAll(selector) {
    if (this.kind === 'container' && selector === '[data-es-repeater-item]') return this.children.filter((item) => item.kind === 'row');
    if (this.kind === 'row' && selector.includes('[name]')) return this.children.filter((item) => item.hasAttribute('name') || item.hasAttribute('id'));
    if (this.kind === 'row') return this.children.filter((item) => item.tagName && ['input', 'textarea', 'select'].includes(item.tagName));
    return [];
  }
  querySelector(selector) {
    if (this.kind === 'container' && selector.startsWith('template')) return this.template;
    if (this.kind === 'container' && selector === '[data-es-repeater-add]') return this.addButton;
    if (this.kind === 'row' && selector === '[data-es-repeater-handle]') return this.handle;
    if (this.kind === 'row' && selector === 'input, textarea, select') return this.children.find((item) => item.tagName === 'input') || null;
    return null;
  }
  focus() { this.focused = true; }
  remove() { if (this.parent) this.parent.children = this.parent.children.filter((item) => item !== this); this.parent = null; }
}

function makeRow(key, label) {
  const row = new Element('row', { 'data-es-repeater-item': key });
  row.parentRow = row;
  const input = new Element('input', { name: `es_options[links][${key}][label]`, id: `es-links-${key}-label` });
  input.tagName = 'input'; input.value = label; input.parentRow = row;
  row.children.push(input);
  row.handle = new Element('handle', { 'data-es-repeater-handle': '' }); row.handle.parentRow = row;
  row.removeButton = new Element('remove', { 'data-es-repeater-remove': '' }); row.removeButton.parentRow = row;
  return row;
}

const listeners = {};
const document = { addEventListener(type, fn) { listeners[type] = fn; } };
const template = { content: { cloneNode() { const fragment = { row: makeRow('item-__INDEX__', '') , querySelector() { return this.row; } }; return fragment; } } };
const container = new Element('container');
container.template = template;
container.addButton = new Element('add', { 'data-es-repeater-add': '' }); container.addButton.parent = container;
container.insertBefore = function (node, reference) {
  const row = node.row || node;
  if (row.parent === this) this.children = this.children.filter((item) => item !== row);
  const index = reference ? this.children.indexOf(reference) : -1;
  if (index < 0) this.children.push(row); else this.children.splice(index, 0, row);
  row.parent = this;
};
const first = makeRow('item-0', 'A'); first.parent = container;
const second = makeRow('item-1', 'B'); second.parent = container;
container.children = [first, second];
const context = { document, window: {}, Array, String, Object, Number };
vm.runInNewContext(fs.readFileSync('theme/assets/admin/repeater.js', 'utf8'), context, { filename: 'repeater.js' });

listeners.keydown({ target: first.handle, key: 'ArrowDown', preventDefault() {} });
assert.deepEqual(container.children.map((row) => row.children[0].value), ['B', 'A'], 'keyboard ArrowDown reorders rows');
assert.equal(container.children[0].getAttribute('data-es-repeater-item'), 'item-0');
assert.equal(container.children[0].children[0].getAttribute('name'), 'es_options[links][item-0][label]', 'reordering updates posted names');

listeners.click({ target: container.children[0].removeButton, preventDefault() {} });
assert.deepEqual(container.children.map((row) => row.children[0].value), ['A'], 'remove deletes the selected row');
assert.equal(container.children[0].children[0].getAttribute('name'), 'es_options[links][item-0][label]', 'remaining row is renumbered');

listeners.click({ target: container.addButton, preventDefault() {} });
assert.equal(container.children.length, 2, 'add creates a new row from template');
assert.equal(container.children[1].getAttribute('data-es-repeater-item'), 'item-1');
assert.equal(container.children[1].children[0].getAttribute('name'), 'es_options[links][item-1][label]');

console.log('repeater editor: keyboard reorder, delete, add and stable posted names passed');
