/* Preview the current city fields, validating before opening a frontend page. */
(() => {
 'use strict';
 let busy = false;
 document.addEventListener('click', async event => {
  const button = event.target.closest('#post-preview');
  if (!button) return;
  event.preventDefault(); event.stopImmediatePropagation();
  if (busy) return;
  busy = true;
  const preview = window.open('about:blank', 'beanstalk-city-preview');
  const previousReady = document.getElementById('beanstalk-city-preview-ready');
  if (previousReady) previousReady.remove();
  const previous = document.getElementById('beanstalk-city-preview-error');
  if (previous) previous.remove();
  button.setAttribute('aria-busy', 'true');
  try {
   if (window.tinymce) window.tinymce.triggerSave();
   const data = new FormData(document.getElementById('post'));
   data.set('action', 'beanstalk_city_editor_preview');
   data.set('nonce', beanstalkCityEditorPreview.nonce);
   const response = await fetch(beanstalkCityEditorPreview.url, {method:'POST', body:data, credentials:'same-origin'});
   const result = await response.json();
   if (!result.success) throw new Error(typeof result.data === 'string' ? result.data : 'The city preview could not be prepared.');
   const ready = document.createElement('div'); ready.id = 'beanstalk-city-preview-ready'; ready.className = 'notice notice-success';
   const paragraph = document.createElement('p'); paragraph.textContent = 'City preview ready. ';
   const link = document.createElement('a'); link.href = result.data.url; link.target = '_blank'; link.rel = 'noopener'; link.textContent = 'Open preview';
   paragraph.append(link); ready.append(paragraph); document.getElementById('acf-group_beanstalk_city_editor').before(ready);
   if (preview && !preview.closed) preview.location.href = result.data.url;
  } catch (error) {
   if (preview) preview.close();
   const notice = document.createElement('div'); notice.id = 'beanstalk-city-preview-error'; notice.className = 'notice notice-error'; notice.setAttribute('role','alert');
   const text = document.createElement('p'); text.textContent = 'Preview needs more information: ' + error.message;
   notice.append(text);
   const group = document.getElementById('acf-group_beanstalk_city_editor');
   group.before(notice); notice.scrollIntoView({block:'center'});
  } finally { busy = false; button.removeAttribute('aria-busy'); }
 }, true);
})();
