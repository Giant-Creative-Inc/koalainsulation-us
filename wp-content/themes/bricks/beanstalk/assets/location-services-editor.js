(() => {
 const el=wp.element.createElement;
 wp.blocks.registerBlockType('koala/location-services', {
  title:'Location Services and Who We Are',icon:'layout',category:'theme',supports:{html:false},
  edit:()=>el('div',{},el(wp.components.Notice,{status:'info',isDismissible:false},'Managed by ACF: Services title, Services description, Location service selections, and Who We Are fields. Uses the existing Bricks video and poster.'),el(wp.serverSideRender,{block:'koala/location-services'})),
  save:()=>null
 });
})();
