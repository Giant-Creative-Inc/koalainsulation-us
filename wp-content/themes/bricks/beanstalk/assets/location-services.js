(() => {
 document.querySelectorAll('[data-location-services]').forEach(section => {
  const tabs=[...section.querySelectorAll('.koala-location-services__tab')];
  const panels=[...section.querySelectorAll('.koala-location-services__panel')];
  const tablist=section.querySelector('.koala-location-services__tabs');
  const panelContainer=section.querySelector('.koala-location-services__panels');
  const mobile=matchMedia('(max-width: 991px)');
  const reduced=matchMedia('(prefers-reduced-motion: reduce)');
  let active=0, generation=0;
  const revealTitle=index=>{
   if(!mobile.matches)return;
   const top=tabs[index].getBoundingClientRect().top;
   const headerBottom=document.querySelector('.beanstalk-header')?.getBoundingClientRect().bottom||0;
   scrollBy({top:top-headerBottom-12,behavior:reduced.matches?'instant':'smooth'});
  };
  const displayPanels=async(index,animate)=>{
   const version=++generation;
   const old=panels.find(panel=>!panel.hidden);
   panels.forEach(panel=>{
    panel.getAnimations().forEach(animation=>animation.cancel());
    panel.style.overflow='';panel.inert=false;
   });
   section.removeAttribute('data-services-animating');
   const next=panels[index];
   if(!next)return;
   if(!animate||!mobile.matches||reduced.matches||old===next){
    panels.forEach((panel,i)=>{panel.hidden=i!==index;});
    if(animate)revealTitle(index);
    return;
   }
   section.setAttribute('data-services-animating','');
   const tween=async(panel,opening)=>{
    const style=getComputedStyle(panel);
    const full={height:panel.getBoundingClientRect().height+'px',opacity:1,marginTop:style.marginTop,marginBottom:style.marginBottom};
    const empty={height:'0px',opacity:0,marginTop:'0px',marginBottom:'0px'};
    panel.style.overflow='hidden';
    const animation=panel.animate(opening?[empty,full]:[full,empty],{duration:opening?240:160,easing:'ease-in-out'});
    try{await animation.finished;}catch{return false;}
    panel.style.overflow='';return version===generation;
   };
   if(old){
    old.inert=true;
    if(!await tween(old,false))return;
    old.hidden=true;old.inert=false;
   }
   next.hidden=false;
   if(!await tween(next,true))return;
   section.removeAttribute('data-services-animating');
   revealTitle(index);
  };
  const activate=(index,focus=false,animate=true)=>{
   active=index;
   tabs.forEach((tab,i)=>{
    const selected=i===index;
    if(mobile.matches){
     tab.removeAttribute('aria-selected');
     tab.setAttribute('aria-expanded',String(selected));
     tab.tabIndex=0;
    }else{
     tab.removeAttribute('aria-expanded');
     tab.setAttribute('aria-selected',String(selected));
     tab.tabIndex=selected?0:-1;
    }
   });
   displayPanels(index,animate);
   if(focus)tabs[index].focus();
  };
  const arrange=()=>{
   if(!tablist)return;
   if(mobile.matches){
    tablist.removeAttribute('role');
    tablist.removeAttribute('aria-orientation');
    tabs.forEach((tab,i)=>{
     tab.setAttribute('role','button');
     panels[i].setAttribute('role','region');
     tab.after(panels[i]);
    });
   }else{
    tablist.setAttribute('role','tablist');
    tablist.setAttribute('aria-orientation','vertical');
    tabs.forEach((tab,i)=>{
     tab.setAttribute('role','tab');
     panels[i].setAttribute('role','tabpanel');
     panelContainer.append(panels[i]);
    });
   }
   activate(active,false,false);
  };
  arrange();
  mobile.addEventListener('change', arrange);
  reduced.addEventListener('change',()=>{if(reduced.matches)activate(active,false,false);});
  tabs.forEach((tab,index)=>{
   tab.addEventListener('click',()=>{
    activate(index);

   });
   tab.addEventListener('keydown',event=>{
    let next=index;
    if(event.key==='ArrowDown')next=(index+1)%tabs.length;
    else if(event.key==='ArrowUp')next=(index-1+tabs.length)%tabs.length;
    else if(event.key==='Home')next=0;
    else if(event.key==='End')next=tabs.length-1;
    else return;
    event.preventDefault();activate(next,true);
   });
  });
 });
})();
