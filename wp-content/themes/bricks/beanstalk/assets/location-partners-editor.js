(() => {
 const el=wp.element.createElement;
 wp.blocks.registerBlockType('koala/location-partners', {
  title:'Location Partners',icon:'groups',category:'theme',supports:{html:false},
  edit:()=>el('div',{},el(wp.components.Notice,{status:'info',isDismissible:false},'Hidden by default. Enable Show partners? in ACF, then add a title and partner logos with optional links.'),el(wp.serverSideRender,{block:'koala/location-partners'})),
  save:()=>null
 });
})();
