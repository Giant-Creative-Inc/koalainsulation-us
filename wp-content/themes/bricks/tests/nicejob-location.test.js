const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const path = require('node:path');
const source = fs.readFileSync(path.join(__dirname, '../assets/js/custom/all-pages.js'), 'utf8');
const helper = source.slice(source.indexOf('  // Queue NiceJob'), source.indexOf('  var siteUrl'));
function fixture() {
  const listeners = new Map();
  const appended = [];
  const microtasks = [];
  const context = {
    window: {
      addEventListener(name, fn) { if (!listeners.has(name)) listeners.set(name, new Set()); listeners.get(name).add(fn); },
      removeEventListener(name, fn) { listeners.get(name)?.delete(fn); },
    },
    document: {
      querySelector() { return appended.length ? appended[0] : null; },
      body: { appendChild(script) { appended.push(script); } },
    },
    queueMicrotask(fn) { microtasks.push(fn); },
  };
  vm.createContext(context);
  vm.runInContext(helper + '\nthis.load = loadNiceJobAfterInteraction;', context);
  return {
    load: context.load, appended,
    interact() { for (const fn of [...(listeners.get('pointerdown') || [])]) fn(); },
    flush() { while (microtasks.length) microtasks.shift()(); },
  };
}
let page = fixture();
page.load({src: 'https://cdn.nicejob.co/js/sdk.min.js?id=123'}, '123');
assert.equal(page.appended.length, 0, 'SDK must wait for interaction');
page.interact(); page.flush();
assert.equal(page.appended.length, 1, 'populated location ID must enable SDK');
page.load({src: 'duplicate'}, '123'); page.flush();
assert.equal(page.appended.length, 1, 'repeated calls must not duplicate SDK');
for (const id of [undefined, null, '', '   ', false]) {
  page = fixture();
  page.load({src: 'fallback'}, id);
  page.interact(); page.flush();
  assert.equal(page.appended.length, 0, 'empty location ID must not load SDK');
  page.load({src: 'https://cdn.nicejob.co/js/sdk.min.js?id=456'}, '456'); page.flush();
  assert.equal(page.appended.length, 1, 'rejected empty ID must not prevent a later valid load');
}
assert.match(source, /loadNiceJobAfterInteraction\(script, locationNiceJobId\)/, 'loader must receive ID from current location response');
assert.doesNotMatch(source, /5343641076236288/, 'national fallback ID must be removed');
assert.match(source, /if \(data\.grShortcode && !locationNiceJobId\)/, 'Google must not override a configured NiceJob provider');
console.log('PASS: location-only NiceJob loading, interaction delay, empty IDs, provider priority and deduplication.');
