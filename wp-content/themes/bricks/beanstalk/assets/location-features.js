(() => {
 const reduced=matchMedia('(prefers-reduced-motion: reduce)');
 document.querySelectorAll('.koala-location-feature').forEach(section=>{
  const details=[...section.querySelectorAll('details')];
  const media=section.querySelector('.koala-location-feature__images');
  const images=[...media.querySelectorAll('.koala-location-feature__photo')];
  media.classList.add('is-ready');
  const select=index=>images.forEach((image,i)=>{
   image.classList.toggle('is-active',i===index);
   image.setAttribute('aria-hidden',String(i!==index));
  });
  select(Math.max(0,details.findIndex(item=>item.open)));
  const controls=details.map((item,index)=>{
   let animation,expanded=item.open,version=0;
   const setExpanded=async next=>{
    const current=++version;
    const start=item.getBoundingClientRect().height;
    expanded=next;
    animation?.cancel();
    item.style.height='';item.style.overflow='';
    item.classList.toggle('is-expanding',expanded);
    if(expanded)select(index);
    if(reduced.matches||!item.animate){item.open=expanded;return;}
    item.open=expanded;
    const end=item.getBoundingClientRect().height;
    item.open=true;
    item.style.overflow='hidden';
    animation=item.animate([{height:start+'px'},{height:end+'px'}],{duration:expanded?260:200,easing:'cubic-bezier(.4,0,.2,1)',fill:'both'});
    try{await animation.finished;}catch{return;}
    if(current!==version)return;
    item.open=expanded;
    animation.cancel();animation=null;
    item.style.overflow='';
   };
   item.classList.toggle('is-expanding',expanded);
   item.querySelector('summary').addEventListener('click',event=>{
    event.preventDefault();
    const next=!expanded;
    if(next)controls.forEach((control,i)=>{if(i!==index&&control.expanded)control.setExpanded(false);});
    setExpanded(next);
   });
   return {setExpanded,get expanded(){return expanded;}};
  });
 });
})();
