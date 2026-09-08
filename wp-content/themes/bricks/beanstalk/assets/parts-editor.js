(() => {
	const { registerBlockType } = wp.blocks;
	const { InspectorControls } = wp.blockEditor;
	const { PanelBody, TextControl, TextareaControl, Notice } = wp.components;
	const el = wp.element.createElement;

	const makeEdit = (title, fields) => ({ attributes, setAttributes }) => el(
		'div', { className: 'beanstalk-part-editor' },
		el(InspectorControls, {}, el(PanelBody, { title, initialOpen: true },
			...fields.map((field) => el(field.multiline ? TextareaControl : TextControl, {
				key: field.key, label: field.label, value: attributes[field.key] || '',
				onChange: (value) => setAttributes({ [field.key]: value })
			}))
		)),
		el('h2', {}, title),
		el(Notice, { status: 'info', isDismissible: false }, 'Location names, URLs, services, resources, phone numbers, and addresses are filled automatically on the public page.'),
		...fields.map((field) => el('p', { key: field.key }, el('strong', {}, field.label + ': '), attributes[field.key]))
	);

	registerBlockType('beanstalk/header', {
		title: 'Beanstalk Header', icon: 'menu', category: 'theme', supports: { html: false },
		attributes: {
			viewAllLocationsLabel:{type:'string',default:'View All Locations'}, servicesLabel:{type:'string',default:'Services'},
			whyKoalaLabel:{type:'string',default:'Why Koala?'}, whyReinsulateLabel:{type:'string',default:'Why Reinsulate?'}, resourcesLabel:{type:'string',default:'Resources'}
		},
		edit: makeEdit('Header labels', [
			{key:'viewAllLocationsLabel',label:'All locations'}, {key:'servicesLabel',label:'Services'},
			{key:'whyKoalaLabel',label:'Why Koala'}, {key:'whyReinsulateLabel',label:'Why Reinsulate'}, {key:'resourcesLabel',label:'Resources'}
		]), save: () => null
	});

	const footerFields = [
		['homeLabel','Home'],['whyKoalaLabel','Why Koala'],['whyReinsulateLabel','Why Reinsulate'],['servicesLabel','Services'],
		['resourcesLabel','Resources'],['contactLabel','Contact Us'],['factOne','Statistic one'],['factTwo','Statistic two'],['factThree','Statistic three'],
		['financingText','Financing disclosure',true],['financingLinkLabel','Financing link text'],['copyrightSuffix','Copyright text'],
		['privacyLabel','Privacy Policy'],['termsLabel','Terms and Conditions']
	].map(([key,label,multiline])=>({key,label,multiline}));
	const footerDefaults = {
		homeLabel:'Home', whyKoalaLabel:'Why Koala', whyReinsulateLabel:'Why Reinsulate', servicesLabel:'Services',
		resourcesLabel:'Resources', contactLabel:'Contact Us', factOne:'*90% of homes are underinsulated',
		factTwo:'*44% of the homes energy is used for heating and cooling', factThree:'*70% potential energy loss from poor insulation and air sealing',
		financingText:'All financing is subject to credit approval. Your terms may vary. Payment options through Wisetack are provided by our lending partners. For example, a $1,200 purchase could cost $104.89 a month for 12 months, based on an 8.9% APR, or $400 a month for 3 months, based on a 0% APR. Offers range from 0-35.9% APR based on creditworthiness. State interest rate caps may apply. No other financing charges or participation fees.',
		financingLinkLabel:'See additional terms at https://www.wisetack.com/faqs.', copyrightSuffix:'Koala Insulation. All rights reserved.',
		privacyLabel:'Privacy Policy', termsLabel:'Terms and Conditions'
	};

	registerBlockType('beanstalk/footer', {
		title: 'Beanstalk Footer', icon: 'layout', category: 'theme', supports: { html: false },
		attributes: Object.fromEntries(footerFields.map(f=>[f.key,{type:'string',default:footerDefaults[f.key]}])),
		edit: makeEdit('Footer content', footerFields), save: () => null
	});
})();
