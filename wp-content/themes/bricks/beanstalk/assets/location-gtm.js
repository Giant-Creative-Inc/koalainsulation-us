(()=>{
 let started=false,fallbackTimer;
 const interactions=['click','keydown','touchstart','wheel'];
 const load=()=>{
  if(started)return;
  started=true;window.clearTimeout(fallbackTimer);
  interactions.forEach(event=>window.removeEventListener(event,load));
  if(typeof window.koalaLoadLocationGtm==='function')window.koalaLoadLocationGtm().catch(error=>console.warn('Location GTM could not load',error));
 };
 if(/thank-you/i.test(location.href)||/[?&]gtm_debug=/.test(location.search)){load();return;}
 interactions.forEach(event=>window.addEventListener(event,load,{once:true,passive:true}));
 const schedule=()=>{if(!started)fallbackTimer=window.setTimeout(load,6000);};
 if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',schedule,{once:true});else schedule();
})();
