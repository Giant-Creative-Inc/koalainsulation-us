(()=>{
 document.querySelectorAll('.koala-location-communities').forEach(section=>{
  const button=section.querySelector('.koala-location-communities__more');
  if(!button)return;
  const items=[...section.querySelectorAll('li')];
  button.addEventListener('click',()=>{
   const open=button.getAttribute('aria-expanded')!=='true';
   button.setAttribute('aria-expanded',String(open));button.textContent=open?'Show Less':'Show More';
   if(!open)items[8]?.scrollIntoView({block:'center',behavior:matchMedia('(prefers-reduced-motion: reduce)').matches?'instant':'smooth'});
   items.forEach((item,index)=>{item.hidden=!open&&index>=9;});
  });
 });
})();
