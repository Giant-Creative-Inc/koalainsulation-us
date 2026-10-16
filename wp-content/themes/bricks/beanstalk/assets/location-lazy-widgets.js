const koalaInitLazyWidgets=()=>{
 const pending=new Map();
 const load=group=>{
  if(pending.has(group))return pending.get(group);
  const task=(async()=>{
   const styles=[...document.querySelectorAll('link[data-location-widget="'+group+'"][data-widget-href]')];
   await Promise.all(styles.map(source=>new Promise((resolve,reject)=>{
    const link=document.createElement('link');link.rel='stylesheet';link.href=source.dataset.widgetHref;
    link.onload=resolve;link.onerror=()=>{link.remove();reject(Error('Widget stylesheet failed'));};document.head.append(link);
   })));
   for(const source of document.querySelectorAll('script[data-location-widget="'+group+'"][data-widget-src]')){
    await new Promise((resolve,reject)=>{
     const script=document.createElement('script');script.src=source.dataset.widgetSrc;
     if(source.nonce)script.nonce=source.nonce;
     script.onload=resolve;script.onerror=()=>{script.remove();reject(Error('Widget script failed'));};document.head.append(script);
    });
   }
   if(group==='reviews'&&typeof window.grw_boot==='function')window.grw_boot();
   window.dispatchEvent(new Event('resize'));
  })();
  pending.set(group,task);
  task.catch(()=>pending.delete(group));return task;
 };
 const sections=[['reviews',document.querySelector('#location-reviews')],['map',document.querySelector('.koala-location-nap__map')]];
 sections.forEach(([group,section])=>{
  if(!section)return;
  const hasAssets=document.querySelector('[data-location-widget="'+group+'"]');
  if(!hasAssets)return;
  const button=document.createElement('button');button.type='button';button.className='koala-location-widget__load';button.setAttribute('data-load-widget',group);button.textContent=group==='reviews'?'Load reviews':'Load service area map';
  if(group==='reviews')section.querySelector('.koala-location-customer-reviews__feed').prepend(button);
  const activate=async()=>{
   if(section.dataset.widgetReady==='true')return;
   try{await load(group);section.dataset.widgetReady='true';button.remove();}
   catch{button.textContent='Retry loading '+(group==='reviews'?'reviews':'map');if(!button.isConnected)section.append(button);}
  };
  button.addEventListener('click',activate);
  section.addEventListener('click',event=>{if(event.target.closest('[data-zip-shape-map-load]'))activate();});
  if('IntersectionObserver'in window){
   const observer=new IntersectionObserver(entries=>{if(entries.some(e=>e.isIntersecting)){observer.disconnect();activate();}},{rootMargin:'300px'});observer.observe(section);
  }else activate();
 });
};
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',koalaInitLazyWidgets,{once:true});else koalaInitLazyWidgets();
