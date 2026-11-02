(() => {
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 document.querySelectorAll('[data-location-overview] details').forEach(details=>{
  const summary=details.querySelector('summary'),content=details.querySelector('.koala-location-overview__more');
  let animation,expanded=details.open,version=0;
  summary.addEventListener('click',async event=>{
   event.preventDefault();
   const current=++version;
   expanded=!expanded;
   animation?.cancel();content.style.overflow='';
   if(reduced.matches){details.open=expanded;return;}
   details.open=true;
   const full=content.getBoundingClientRect().height;
   content.style.overflow='hidden';
   animation=content.animate(expanded?[{height:'0px',paddingTop:'0px',opacity:0},{height:full+'px',paddingTop:'32px',opacity:1}]:[{height:full+'px',paddingTop:'32px',opacity:1},{height:'0px',paddingTop:'0px',opacity:0}],{duration:240,easing:'ease-in-out'});
   try{await animation.finished;}catch{return;}
   if(current!==version)return;
   details.open=expanded;content.style.overflow='';
  });
 });
})();
