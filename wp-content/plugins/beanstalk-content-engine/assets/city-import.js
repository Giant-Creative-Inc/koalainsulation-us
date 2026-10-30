/* Scoped to Tools > City Page Import. CSV content is always inserted as text. */
(() => {
 'use strict';
 const root = document.getElementById('beanstalk-city-import');
 if (!root) return;
 const form = root.querySelector('form'), status = root.querySelector('#city-import-status');
 const body = root.querySelector('tbody'), apply = root.querySelector('#city-import-apply');
 const select = root.querySelector('#city-import-select'), report = root.querySelector('#city-import-report');
 const preview = root.querySelector('#city-import-preview');
 const revalidate = root.querySelector('#city-import-revalidate');
 let job = '', items = [], busy = false, remaining = false;
 const checked = new Set();
 function controls(value) {
  busy = value;
  form.querySelectorAll('input,button').forEach(el => { el.disabled = value; });
  apply.disabled = value || remaining || !items.some(item => item.status === 'validated');
  select.disabled = apply.disabled;
  preview.disabled = value || !remaining;
  revalidate.disabled = value || !job || items.some(i => ['updated','created','interrupted'].includes(i.status));
  report.disabled = value || !items.length;
 }
 function render(data) {
  job = data.job; items = data.items;
  if (data.total !== undefined) remaining = data.cursor < data.total;
  body.replaceChildren();
  const fragment = document.createDocumentFragment();
  for (const item of items) {
   const tr = document.createElement('tr'), td = document.createElement('td');
   const box = document.createElement('input'); box.type = 'checkbox';
   box.disabled = item.status !== 'validated'; box.checked = !box.disabled && checked.has(item.index);
   box.setAttribute('aria-label', `Select ${item.title}`);
   box.addEventListener('change', () => box.checked ? checked.add(item.index) : checked.delete(item.index));
   td.append(box); tr.append(td);
   for (const value of [item.row, item.title, item.slug, item.status, item.detail]) {
    const cell = document.createElement('td'); cell.textContent = value; tr.append(cell);
   }
   fragment.append(tr);
  }
  body.append(fragment);
 }
 async function request(operation, extra) {
  const data = extra || new FormData();
  data.set('action', 'beanstalk_city_import'); data.set('nonce', beanstalkCityImport.nonce);
  data.set('operation', operation); data.set('job', job);
  const response = await fetch(beanstalkCityImport.url, {method: 'POST', body: data, credentials: 'same-origin'});
  const result = await response.json();
  if (!result.success) throw new Error(typeof result.data === 'string' ? result.data : 'Import request failed.');
  render(result.data); return result.data;
 }
 async function run(task) {
  if (busy) return;
  controls(true);
  try { await task(); }
  catch (error) { status.textContent = `${error.message} Completed rows are retained. Reload this screen to retrieve the saved results; do not repeat uncertain updates.`; }
  finally { controls(false); }
 }
 form.addEventListener('submit', event => {
  event.preventDefault();
  const upload = new FormData(form);
  run(async () => {
   checked.clear(); let data = await request('upload', upload);
   while (data.cursor < data.total) {
    status.textContent = `Validating ${data.cursor} of ${data.total} rows…`;
    data = await request('preview');
   }
   status.textContent = `Preview complete: ${items.filter(i => i.status === 'validated').length} valid, ${items.filter(i => i.status === 'error').length} errors. Select rows to update or create as drafts. No pages have changed.`;
  });
 });
 revalidate.addEventListener('click', () => run(async () => {
  checked.clear();
  let data = await request('revalidate');
  while (data.cursor < data.total) { status.textContent = `Validating ${data.cursor} of ${data.total} saved rows…`; data = await request('preview'); }
  status.textContent = `Preview refreshed: ${items.filter(i => i.status === 'validated').length} valid, ${items.filter(i => i.status === 'error').length} errors. No pages have changed.`;
 }));
 preview.addEventListener('click', () => run(async () => {
  let data = await request('preview');
  while (data.cursor < data.total) { status.textContent = `Validating ${data.cursor} of ${data.total} rows…`; data = await request('preview'); }
  status.textContent = 'Preview complete. Select valid rows to update or create as drafts. No pages have changed.';
 }));
 select.addEventListener('click', () => {
  items.filter(i => i.status === 'validated').forEach(i => checked.add(i.index));
  render({job, items});
 });
 apply.addEventListener('click', () => run(async () => {
  const selected = items.filter(i => checked.has(i.index) && i.status === 'validated').map(i => i.index);
  if (!selected.length) { status.textContent = 'Select at least one valid row.'; return; }
  if (!window.confirm(`Import ${selected.length} selected rows? Existing pages will be updated; missing pages will be created as drafts. Their Beanstalk template will be enabled.`)) return;
  for (let i = 0; i < selected.length; i += 5) {
   status.textContent = `Applying ${i} of ${selected.length} selected updates…`;
   const data = new FormData(); selected.slice(i, i + 5).forEach(index => data.append('selected[]', index));
   await request('apply', data);
  }
  checked.clear(); status.textContent = `Import finished. ${items.filter(i => i.status === 'updated').length} updated, ${items.filter(i => i.status === 'created').length} drafts created; ${items.filter(i => ['error', 'interrupted'].includes(i.status)).length} errors or interrupted rows. Download the report for details.`;
 }));
 report.addEventListener('click', () => {
  // Prevent spreadsheet formula execution when opening a user-supplied value.
  const cell = value => '"' + String(value).replace(/^[\s]*[=+@-]/, "'$&").replaceAll('"','""') + '"';
  const csv = [['row','title','slug','status','detail'], ...items.map(i => [i.row,i.title,i.slug,i.status,i.detail])].map(row => row.map(cell).join(',')).join('\r\n');
  const url = URL.createObjectURL(new Blob(['\uFEFF' + csv], {type:'text/csv;charset=utf-8'}));
  const link = document.createElement('a'); link.href = url; link.download = 'city-import-results.csv'; link.click(); URL.revokeObjectURL(url);
 });
 // Recover the current administrator's saved preview after a reload.
 run(async () => {
  const data = new FormData(); data.set('action','beanstalk_city_import'); data.set('nonce',beanstalkCityImport.nonce); data.set('operation','resume');
  const response = await fetch(beanstalkCityImport.url,{method:'POST',body:data,credentials:'same-origin'});
  const result = await response.json();
  if (result.success) { render(result.data); status.textContent = 'Saved results restored. Use Revalidate uploaded CSV to refresh this preview.'; }
 });
})();
