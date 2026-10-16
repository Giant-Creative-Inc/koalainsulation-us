(() => {
 const el=wp.element.createElement;
 wp.blocks.registerBlockType('koala/location-overview',{
  title:'Location Overview',icon:'text-page',category:'theme',supports:{html:false},
  edit:()=>el('div',{},el(wp.components.Notice,{status:'info',isDismissible:false},'Managed by ACF: Location insulation rich text 1 and 2, location phone number, and Hide location overview. The heading and photo follow the Bricks template.'),el(wp.serverSideRender,{block:'koala/location-overview'})),
  save:()=>null
 });
})();
