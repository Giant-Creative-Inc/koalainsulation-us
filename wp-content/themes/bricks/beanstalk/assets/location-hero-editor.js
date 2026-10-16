(() => {
 const el = wp.element.createElement;
 const attrs = {
  media: {imageId:{type:'number'},posterId:{type:'number'},provider:{type:'string'},videoId:{type:'string'},privacyHash:{type:'string'}},
  reviews:{heading:{type:'string'}},quote:{label:{type:'string',default:'Get a FREE Quote'}}
 };
 ['media','reviews','quote'].forEach(name => wp.blocks.registerBlockType('koala/location-hero-'+name, {
  title:'Location Hero '+name,icon:'location',category:'theme',attributes:attrs[name],supports:{html:false},
  edit: props => el('div',{},
   name !== 'quote' ? el(wp.components.Notice,{status:'info',isDismissible:false},'Managed by the location’s ACF Hero fields. Save changes in ACF to update this content.') :
    el(wp.components.TextControl,{label:'Quote button label',value:props.attributes.label,onChange:label=>props.setAttributes({label})}),
   el(wp.serverSideRender,{block:'koala/location-hero-'+name,attributes:props.attributes})),
  save:()=>null
 }));
})();
// Explain ownership when selecting ACF-managed core text blocks.
wp.hooks.addFilter('editor.BlockEdit', 'koala/location-hero-acf-ownership', BlockEdit => props => {
 const name = props.attributes?.metadata?.name;
 const managed = ['koala-location-hero-heading','koala-location-hero-introduction'].includes(name);
 if (!managed || !props.isSelected) return wp.element.createElement(BlockEdit, props);
 return wp.element.createElement(wp.element.Fragment, {},
  wp.element.createElement(wp.components.Notice, {status:'info',isDismissible:false}, 'This content is managed by the location’s ACF Hero fields. ACF saves replace direct edits here.'),
  wp.element.createElement(BlockEdit, props));
});
