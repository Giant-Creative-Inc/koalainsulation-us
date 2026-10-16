(()=>{
 const el=wp.element.createElement;
 wp.blocks.registerBlockType('koala/location-community',{
  apiVersion:3,title:'Location ACF Section',icon:'location',category:'theme',attributes:{section:{type:'string'}},usesContext:['postId'],supports:{html:false},
  edit:props=>el('div',wp.blockEditor?wp.blockEditor.useBlockProps():{},el(wp.components.Notice,{status:'info',isDismissible:false},'Content comes from this location’s ACF fields and selected records. Changes there update this section automatically.'),el(wp.serverSideRender,{block:'koala/location-community',attributes:props.attributes})),save:()=>null
 });
})();
