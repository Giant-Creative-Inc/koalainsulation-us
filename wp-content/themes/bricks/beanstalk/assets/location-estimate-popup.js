(()=>{
 const dialog=document.querySelector('#koala-location-estimate');
 if(!dialog)return;
 let opener,oldOverflow,backdropStart=false;
 const close=()=>dialog.close();
 const populate=trigger=>{
  const values={17:dialog.dataset.locationSlug,18:dialog.dataset.locationId,19:dialog.dataset.pageUrl,24:trigger.textContent.trim(),25:trigger.closest('.koala-location-services__panel,.koala-location-offers__card')?.querySelector('h3')?.textContent||''};
  Object.entries(values).forEach(([id,value])=>{const field=dialog.querySelector('[name="input_'+id+'"]');if(field)field.value=value;});
 };
 let loading;
 const container=dialog.querySelector('.koala-location-estimate__form');
 const load=()=>{
  if(loading)return loading;
  const image=dialog.querySelector('[data-popup-image-src]');
  if(image&&!image.src)image.src=image.dataset.popupImageSrc;
  if(container.querySelector('form,.gform_confirmation_wrapper'))return Promise.resolve();
  container.setAttribute('aria-busy','true');
  loading=(async()=>{
   const response=await fetch(dialog.dataset.formEndpoint,{credentials:'same-origin',cache:'no-store'});
   if(!response.ok)throw new Error('Unable to load quote form');
   const data=await response.json();
   await new Promise((resolve,reject)=>{
    let link=document.querySelector('link[data-estimate-css]');
    if(link){resolve();return;}
    link=document.createElement('link');link.rel='stylesheet';link.href=data.css;link.dataset.estimateCss='';
    link.onload=resolve;link.onerror=()=>{link.remove();reject(new Error('Unable to load form styles'));};document.head.append(link);
   });
   const start=Date.now();
   while(!window.gform?.initializeOnLoaded){
    if(Date.now()-start>15000)throw new Error('Form resources are unavailable');
    await new Promise(resolve=>setTimeout(resolve,50));
   }
   const template=document.createElement('template');template.innerHTML=data.html;
   const scripts=[...template.content.querySelectorAll('script')];scripts.forEach(script=>script.remove());
   container.replaceChildren(template.content);
   for(const original of scripts){
    if(original.type&&!/^(text|application)\/javascript$/.test(original.type))continue;
    if(original.src&&[...document.scripts].some(script=>script.src===original.src))continue;
    const script=document.createElement('script');
    for(const attribute of original.attributes)script.setAttribute(attribute.name,attribute.value);
    script.textContent=original.textContent;
    if(original.src)await new Promise((resolve,reject)=>{script.onload=resolve;script.onerror=reject;container.append(script);});
    else container.append(script);
   }
   if(opener)populate(opener);
  })().catch(()=>{
   loading=null;container.replaceChildren();
   const message=document.createElement('p');message.textContent='The quote form could not load. Please try again.';
   const retry=document.createElement('button');retry.type='button';retry.textContent='Retry loading form';retry.addEventListener('click',load);
   container.append(message,retry);
  }).finally(()=>container.removeAttribute('aria-busy'));
  return loading;
 };
 document.addEventListener('click',event=>{
  const trigger=event.target.closest('[data-location-quote-popup-trigger]');
  if(!trigger||dialog.open)return;
  event.preventDefault();opener=trigger;populate(trigger);
  oldOverflow=document.documentElement.style.overflow;
  document.documentElement.style.overflow='hidden';
  dialog.showModal();dialog.scrollTop=0;load();
  dialog.querySelector('[data-estimate-close]').focus({preventScroll:true});
 });
 dialog.addEventListener('keydown',event=>{
  if(event.key!=='Tab')return;
  const focusable=[...dialog.querySelectorAll('a[href],button,input,select,textarea,[tabindex]')].filter(e=>!e.disabled&&e.tabIndex>=0&&e.getClientRects().length&&getComputedStyle(e).visibility!=='hidden');
  const first=focusable[0],last=focusable.at(-1);
  if(event.shiftKey&&document.activeElement===first){event.preventDefault();last?.focus();}
  else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first?.focus();}
 });
 dialog.querySelector('[data-estimate-close]').addEventListener('click',close);
 const outside=event=>{const r=dialog.getBoundingClientRect();return event.target===dialog&&(event.clientX<r.left||event.clientX>r.right||event.clientY<r.top||event.clientY>r.bottom);};
 dialog.addEventListener('pointerdown',event=>{backdropStart=outside(event);});
 dialog.addEventListener('click',event=>{if(backdropStart&&outside(event))close();backdropStart=false;});
 dialog.addEventListener('close',()=>{document.documentElement.style.overflow=oldOverflow||'';opener?.focus({preventScroll:true});backdropStart=false;});
})();
